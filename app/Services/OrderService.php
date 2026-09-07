<?php

namespace App\Services;

use App\Models\User;
use App\Models\Astro;
use App\Models\Order;
use Razorpay\Api\Api;
use App\Models\Country;
use App\Models\Product;
use App\Models\Razorpay;
use App\Models\Promocode;
use App\Models\OrderDetail;
use App\Models\ProductVariant;
use App\Models\OrderTransaction;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Notifications\SendNotification;

class OrderService
{
    public $currency;
    public function __construct()
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $country = Country::find($user?->country_id);
            if ($country) {
                $this->currency = ($country->iso3 === 'IND')
                    ? 'INR'
                    : 'USD';
            }
        }
    }
    public function createOrder($request, $user)
    {
        $user = User::findOrFail($user->id);
        $recommendedBy = Astro::where('id', $request->recommended_by)->first();
        $product = Product::findOrFail($request->product_id);
        $variation = ProductVariant::find($request->variation_id);
        $product['variation'] = $variation;
        $address = $user->addresses()->where('id', $request->address_id)->firstOrFail();
        $amount = $variation ? $variation->price : $product->price;
        $promocode = Promocode::whereDate('end', '>=', date('Y-m-d'))
            ->where('status', 'active')
            ->where('code', $request->promocode)
            ->first();

        if ($promocode) {
            if ($amount >= $promocode->minimum_order_amount) {
                if ($promocode->discount_type == 'percentage') {
                    $discount_calculate = round(($amount * ($promocode->discount) / 100));

                    $discount = ($discount_calculate <= $promocode->max_discount_amount) ? $discount_calculate : $promocode->max_discount_amount;
                    $final_total = ($amount - $discount);
                } else {
                    $discount = ($promocode->discount);
                    $final_total = ($amount - $discount);
                }
            }
        }

        $total = $final_total ?? $amount;
        $walletUsed = 0;
        if ($request->wallet_used == 'yes' && $user->wallet > 0 && $user->wallet < $total) {
            $final_total = $total - $user->wallet;
            $walletUsed += $user->wallet;
        }
        $data = [
            'user' => $user,
            'recommended_by' => $recommendedBy ?? null,
            'product' => $product,
            'address' => $address,
            'promocode' => $promocode,
            'promocode_discount' => $discount ?? 0,
            'sub_total' => $amount,
            'total' => $total,
            'wallet' => $walletUsed,
        ];
        if ($request->wallet_used == 'yes' && $user->wallet < $total) {
            DB::beginTransaction();
            $order = Order::create([
                'type' => $product->type,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->phone,
                'promocode' => $promocode->code ?? null,
                'promocode_discount' => $promocode_discount ?? 0,
                'sub_total' => $amount,
                'total' => $total,
                'order_uid' => $this->generateOrderId(),
                'address' => json_encode($address),
                'recommended_by' => $recommendedBy ?? null,
                'status' => 'confirmed',
                'payment_mode' => 'online',
                'confirmed_at' => now(),
                'currency' => $this->currency
            ]);
            $quantity = 1;
            $orderDetail = OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product' => json_encode($product),
                'quantity' => $quantity,
                'price' => $product->variation ? $product->variation->price : $product->price,
                'total' => $quantity * ($product->variation ? $product->variation->price : $product->price),
            ]);

            $orderTransaction = OrderTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'transaction_id' => '-',
                'razorpay_id' => null,
                'amount' => $total,
                'wallet_used' => $total ?? 0,
                'remark' => 'Amount paid successfully using wallet',
                'status' => 'paid',
                'currency' => $this->currency
            ]);
            $user->wallet = $user->wallet - $total;
            $user->save();
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $orderTransaction->wallet_used,
                'previous_balance' => $user->wallet + $orderTransaction->wallet_used,
                'updated_balance' => $user->wallet,
                'remark' => 'Session payment',
                'currency' => $this->currency
            ]);
            DB::commit();
            $response = [
                'success' => true,
                'razorpay' => false,
                'status' => 'success',
                'message' => 'Order has been placed successfully. Thank you!'
            ];
            return response($response, 200);
        }
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $orderData = [
            'receipt' => 'karmatalk_' . uniqId(),
            'amount' => $total * 100, //  rupees in paise
            'currency' => $this->currency,
            'payment_capture' => 1, // auto capture
            'notes' => [
                "user" => json_encode($user->id),
                "product" => json_encode($product->id),
            ],
        ];

        $razorpayOrder = $api->order->create($orderData);
        $razorpayOrderId = $razorpayOrder['id'];

        $razorpayOrderData = Razorpay::create([
            'user_id' => $user->id,
            'order_id' => $razorpayOrderId,
            'order_data' => json_encode($data),
            'amount' => $total,
            'type' => 'purchase',
            'currency' => $this->currency,
        ]);
        return $razorpayOrderId;
    }

    public function fetchOrder($razorpayOrder)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $user = User::findOrFail($razorpayOrder->user_id);
        $fetchPayment = $api->order->fetch($razorpayOrder->order_id);
        $orderData = json_decode($razorpayOrder->order_data);
        $status = ['paid', 'captured'];
        if (in_array($fetchPayment->status, $status)) {
            DB::beginTransaction();
            $order = Order::create([
                'type' => $orderData->product->type,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->phone,
                'promocode' => $orderData->promocode->code ?? null,
                'promocode_discount' => $orderData->promocode_discount ?? 0,
                'sub_total' => $orderData->sub_total,
                'total' => $orderData->total,
                'order_uid' => $this->generateOrderId(),
                'address' => json_encode($orderData->address),
                'recommended_by' => $orderData->recommended_by ?? null,
                'status' => 'confirmed',
                'payment_mode' => 'online',
                'confirmed_at' => now(),
                'currency' => $razorpayOrder->currency,
            ]);
            $quantity = 1;
            $orderDetail = OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $orderData->product->id,
                'product' => json_encode($orderData->product),
                'quantity' => $quantity,
                'price' => $orderData->product->variation ? $orderData->product->variation->price : $orderData->product->price,
                'total' => $quantity * ($orderData->product->variation ? $orderData->product->variation->price : $orderData->product->price),
            ]);

            OrderTransaction::create([
                'user_id' => $user->id,
                'order_id' => $order->id,
                'transaction_id' => $fetchPayment->receipt,
                'razorpay_id' => $razorpayOrder->id,
                'amount' => $fetchPayment->amount_paid / 100,
                'remark' => 'Amount paid successfully',
                'status' => $fetchPayment->status,
                'currency' => $razorpayOrder->currency,
            ]);

            $razorpayOrder->update([
                'status' => 'captured'
            ]);
            DB::commit();

            $firebaseService = new SendNotification();
            $firebaseService->sendNotification($user->device_id, 'Congratulations!', 'Your order has been placed successfully!', $data ?? []);
            $response = [
                'success' => true,
                'status' => 'success',
                'message' => 'Order has been placed successfully. Thank you!'
            ];
            return response($response, 200);
        } else if ($fetchPayment->status == 'failed' || now()->diffInMinutes($razorpayOrder->created_at) >= 120) {
            $razorpayOrder->update([
                'status' => 'failed'
            ]);

            $response = [
                'success' => false,
                'status' => 'failed',
                'message' => 'Oops! Due to technical issue your payment get failed contact with administrator'
            ];
            return response($response, 408);
        } else {
            $response = [
                'success' => false,
                'status' => 'failed',
                'message' => 'Temporarily server down'
            ];
            return response($response, 408);
        }
    }

    public function generateOrderId()
    {
        $order = Order::whereNotNull('order_uid')->orderBy('id', 'desc')->first();
        if (!$order) {
            $orderId = 'ORD' . date('dmY') . '001';
            return $orderId;
        }
        $orderPrefix = substr($order->order_uid, 0, 2);
        $oldPrefix = $orderPrefix  . date('dmY');
        $prefix = $orderPrefix . date('dmY');
        $currentId = strtok($order->order_uid, ' ');
        $serialNumber = substr($currentId, strlen($oldPrefix));

        $newSerialNumber = (int)$serialNumber + 1;
        $newSerialNumber = str_pad($newSerialNumber, strlen($serialNumber), '0', STR_PAD_LEFT);
        $orderId = $prefix . $newSerialNumber;
        return $orderId;
    }
}
