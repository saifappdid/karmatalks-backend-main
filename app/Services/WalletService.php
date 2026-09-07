<?php
namespace App\Services;

use App\Models\Order;
use App\Models\Razorpay;
use App\Models\User;
use App\Models\WalletPackage;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Api;

class WalletService
{
    public function addWallet($request, $user)
    {
        $user             = User::with(['country'])->findOrFail($user->id);
        $currency         = $user->country->iso3 === 'IND' ? 'INR' : 'USD';
        $package          = WalletPackage::findOrFail($request->package_id);
        $checkTransaction = Razorpay::where(['user_id' => $user->id, 'type' => 'wallet'])->whereIn('status', ['captured'])->count();
        if ($package->for_new_user == 'yes' && $checkTransaction > 0) {
            throw ValidationException::withMessages(['package_id' => 'This package is only for new users and you have already availed it.']);
        }
        $amount = $currency == 'INR' ? $package->amount : $package->amount_dollar;
        $bonus  = $currency == 'INR' ? $package->bonus : $package->bonus_dollar;
        $data   = [
            'user'    => $user,
            'package' => $package,
            'amount'  => $amount,
            'bonus'   => $bonus,
        ];
        $api       = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $orderData = [
            'receipt'         => 'karmatalk_' . uniqId(),
            'amount'          => $amount * 100,
            'currency'        => $currency,
            'payment_capture' => 1,
            'notes'           => [
                "user"    => json_encode($user->id),
                "package" => json_encode($package->id),
            ],
        ];
        $razorpayOrder   = $api->order->create($orderData);
        $razorpayOrderId = $razorpayOrder['id'];
        $razorpay        = Razorpay::create([
            'type'       => 'wallet',
            'user_id'    => $user->id,
            'order_id'   => $razorpayOrderId,
            'order_data' => json_encode($data),
            'amount'     => $amount,
            'status'     => $razorpayOrder['status'],
            'currency'   => $currency,
        ]);
        return $razorpayOrderId;
    }

    public function fetchWallet($razorpayOrder, bool $fromCron = false)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // check if for_new_user is yes then check user have any captured transaction if yes then return error message
        $orderDataDecoded = json_decode($razorpayOrder->order_data);
        $package          = $orderDataDecoded->package;
        if ($package->for_new_user == 'yes') {
            $checkTransaction = Razorpay::where(['user_id' => $razorpayOrder->user_id, 'type' => 'wallet'])->whereIn('status', ['captured'])->count();
            if ($checkTransaction > 0) {
                if ($fromCron) {
                    return;
                } else {
                    $response = [
                        'success' => true,
                        'status'  => 'success',
                        'message' => 'Order has been placed successfully. Thank you!',
                    ];
                }
            }
        }

        $user         = User::findOrFail($razorpayOrder->user_id);
        $orderData    = json_decode($razorpayOrder->order_data);
        $fetchPayment = $api->order->fetch($razorpayOrder->order_id);
        $status       = ['paid', 'captured'];
        if (in_array($fetchPayment->status, $status)) {
            if ($fromCron) {
                $response = $this->paymentSuccessProcess($razorpayOrder, $user, $orderData);
            } else {
                $response = [
                    'success' => true,
                    'status'  => 'success',
                    'message' => 'Order has been placed successfully. Thank you!',
                ];
            }
            return response($response, 200);
        } else if ($fetchPayment->status == 'failed' || now()->diffInMinutes($razorpayOrder->created_at) >= 120) {
            if ($fromCron) {
                $response = $this->paymentFailedProcess($razorpayOrder);
            } else {
                $response = [
                    'success' => false,
                    'status'  => 'failed',
                    'message' => 'Oops! Due to technical issue your payment get failed contact with administrator',
                ];
            }
            return response($response, 408);
        } else {
            $response = [
                'success' => false,
                'status'  => 'failed',
                'message' => 'Temporarily server down',
            ];
            return response($response, 408);
        }
    }   

    public function paymentSuccessProcess($razorpayOrder, $user, $orderData)
    {
        DB::beginTransaction();
        $order = WalletTransaction::create([
            'type'             => 'credit',
            'user_id'          => $user->id,
            'amount'           => $razorpayOrder->amount,
            'previous_balance' => $user->wallet,
            'updated_balance'  => $user->wallet + $razorpayOrder->amount,
            'package_id'       => json_decode($razorpayOrder->order_data)->package->id,
            'remark'           => 'Wallet recharge via Razorpay',
            'currency'         => $razorpayOrder->currency,
        ]);
        $user->increment('wallet', $razorpayOrder->amount);
        $user->refresh();
        $bonus = $orderData->bonus ?? 0;
        if ($bonus > 0) {
            $order = WalletTransaction::create([
                'type'             => 'credit',
                'user_id'          => $user->id,
                'amount'           => $bonus,
                'previous_balance' => $user->wallet,
                'updated_balance'  => $user->wallet + $bonus,
                'remark'           => 'Wallet recharge bonus',
                'currency'         => $razorpayOrder->currency,
            ]);
            $user->increment('wallet', $bonus);
        }
        $razorpayOrder->update(['status' => 'captured']);
        DB::commit();
        $response = [
            'success' => true,
            'status'  => 'success',
            'message' => 'Order has been placed successfully. Thank you!',
        ];
        return $response;
    }

    public function paymentFailedProcess($razorpayOrder)
    {
        $razorpayOrder->update(['status' => 'failed']);
        $response = [
            'success' => false,
            'status'  => 'failed',
            'message' => 'Oops! Due to technical issue your payment get failed contact with administrator',
        ];
        return $response;
    }
}
