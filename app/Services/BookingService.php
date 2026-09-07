<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Astro;
use Razorpay\Api\Api;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Service;
use App\Models\Razorpay;
use App\Models\AstroPrice;
use App\Models\AstroService;
use App\Models\WalletTransaction;
use App\Models\BookingTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\AstroResource;
use App\Notifications\SendNotification;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public $currency;
    public $converter;
    public $timeZone;
    public function __construct()
    {
        $user = auth('sanctum')->user();
        if ($user) {
            $country = Country::find($user->country_id);
            $this->currency = ($country->iso3 === 'IND')
                ? 'INR'
                : 'USD';
            $timeZone = json_decode($country->timezones);
            $this->timeZone = $timeZone[0]->zoneName;
            if ($timeZone) {
                $this->converter = new TimeConverterService($timeZone[0]->zoneName);
            }
        }
    }
    public function createBooking($userId, $request)
    {
        $user = User::findOrFail($userId);
        $astro = Astro::findOrFail($request->astro_id);
        if ($astro->is_online == '0') {
            throw ValidationException::withMessages(['Astro is currently not available for schedule consultation.']);
        }
        $price = AstroPrice::findOrFail($request->price_id);
        $astro['price'] = $price;
        $service = $astro->services()->where(['service_id' => $request->service_id, 'astro_id' => $request->astro_id])->first();
        $subTotal = $this->currency === 'USD' ? $price->charge_dollar : $price->charge;
        // $promocode = Promocode::whereDate('end', '>=', date('Y-m-d'))
        //     ->where('status', 'active')
        //     ->where('code', $request->promocode)
        //     ->first();
        // if ($promocode) {
        //     if ($subTotal >= $promocode->minimum_order_amount) {
        //         if ($promocode->discount_type == 'percentage') {
        //             $discount_calculate = round(($subTotal * ($promocode->discount) / 100));

        //             $discount = ($discount_calculate <= $promocode->max_discount_amount) ? $discount_calculate : $promocode->max_discount_amount;
        //             $discountedAmount = ($subTotal - $discount);
        //         } else {
        //             $discount = ($promocode->discount);
        //             $discountedAmount = ($subTotal - $discount);
        //         }
        //     }
        // }
        $total = $discountedAmount ?? $subTotal;
        $walletUsed = 0;
        if ($request->wallet_use == 'yes' && $user->wallet > 0 && $user->wallet < $total) {
            $final_total = $total - $user->wallet;
            $walletUsed += $user->wallet;
        }

        $data = [
            'data' => $request->all(),
            'service' => $service,
            'pricing' => $price,
            'astro' => new AstroResource($astro),
            'promocode' => $promocode ?? null,
            'promocode_discount' => $discount ?? null,
            'sub_total' => $subTotal,
            'final_total' => ($final_total ?? $total),
            'wallet' => $walletUsed,
        ];
        $bookingStartIST = Carbon::parse(
            $request->start_time,
            $this->timeZone
        )->setTimezone('Asia/Kolkata')->format('H:i:s');
        $bookingEndIST = Carbon::parse(
            $request->end_time,
            $this->timeZone
        )->setTimezone('Asia/Kolkata')->format('H:i:s');
        if ($request->wallet_use == 'yes' && $user->wallet >= $total) {
            $booking = Booking::create([
                'type' => $request->type,
                'user_id' => $user->id,
                'astro_id' => $astro->id,
                'name' => $user->name,
                'mobile' => $user->phone,
                'session_date' => $request->session_date,
                'start_time' => $bookingStartIST,
                'end_time' => $bookingEndIST,
                'duration' => $request->duration,
                'questions' => $price->questions,
                'promocode' => $data['promocode'] ?? null,
                'promocode_discount' => $data['promocode_discount'] ?? 0,
                'wallet' => $total ?? null,
                'sub_total' => $subTotal,
                'final_total' => $total,
                'booking_uid' => $this->generateBookingId(),
                'booking_data' => json_encode($data),
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'currency' => $this->currency,
                'name' => $request->name,
                'date_of_birth' => $request->date_of_birth,
                'place' => $request->place,
            ]);

            $bookingTransaction = BookingTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'razorpay_id' => null,
                'transaction_id' => '-',
                'amount' => ($total),
                'wallet_used' => $total ?? 0,
                'status' => 'paid',
                'remark' => 'Amount paid through wallet',
                'currency' => $this->currency
            ]);
            $user->wallet = $user->wallet - $total;
            $user->save();
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $bookingTransaction->wallet_used,
                'previous_balance' => $user->wallet + $bookingTransaction->wallet_used,
                'updated_balance' => $user->wallet,
                'remark' => 'Session payment',
                'currency' => $this->currency
            ]);
            DB::commit();

            $firebaseService = new SendNotification();
            try {
                $firebaseService->sendNotification($user->device_id, 'Congratulations!', 'Your booking has been placed successfully!', []);
                $firebaseService->sendNotification($astro->device_id, 'Congratulations!', 'You have a new booking!', []);
            } catch (\Throwable $th) {
                \Log::info('Error sending notification: ' . $th->getMessage());
            }
            $response = [
                'success' => true,
                'razorpay' => false,
                'status' => 'success',
                'message' => 'Booking has been placed successfully. Thank you!'
            ];
            return response($response, 200);
        }
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $orderData = [
            'receipt' => 'karmatalk_' . uniqId(),
            'amount' => ($final_total ?? $total) * 100, //  rupees in paise
            'currency' => $this->currency,
            'payment_capture' => 1, // auto capture
            'notes' => [
                "user" => json_encode($user->id),
                "service" => json_encode($service),
            ],
        ];

        $razorpayOrder = $api->order->create($orderData);
        $razorpayOrderId = $razorpayOrder['id'];

        $razorpayOrderData = Razorpay::create([
            'user_id' => $user->id,
            'order_id' => $razorpayOrderId,
            'order_data' => json_encode($data),
            'amount' => ($final_total ?? $total),
            'type' => 'booking',
            'currency' => $this->currency,
        ]);
        return $razorpayOrderId;
    }
    public function fetchBooking($razorpayOrder)
    {
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
        $user = User::findOrFail($razorpayOrder->user_id);
        $fetchPayment = $api->order->fetch($razorpayOrder->order_id);
        $bookingData = json_decode($razorpayOrder->order_data);
        $status = ['paid', 'captured'];
        $astro = Astro::findOrFail($bookingData->astro->id);

        if (in_array($fetchPayment->status, $status)) {
            $bookingStartIST = Carbon::parse(
                $bookingData->data->start_time,
                $this->timeZone
            )->setTimezone('Asia/Kolkata')->format('H:i:s');
            $bookingEndIST = Carbon::parse(
                $bookingData->data->end_time,
                $this->timeZone
            )->setTimezone('Asia/Kolkata')->format('H:i:s');
            DB::beginTransaction();

            $booking = Booking::create([
                'type' => $bookingData->data->type,
                'user_id' => $user->id,
                'astro_id' => $bookingData->astro->id,
                'name' => $user->name,
                'mobile' => $user->phone,
                'session_date' => $bookingData->data->session_date,
                'start_time' => $bookingStartIST,
                'end_time' => $bookingEndIST,
                'duration' => $bookingData->data->duration,
                'questions' => $bookingData->pricing->questions,
                'promocode' => $bookingData->promocode ?? null,
                'promocode_discount' => $bookingData->promocode_discount ?? 0,
                'wallet' => $bookingData->wallet ?? null,
                'sub_total' => $bookingData->sub_total,
                'final_total' => $bookingData->final_total,
                'booking_uid' => $this->generateBookingId(),
                'booking_data' => json_encode($bookingData),
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'currency' => $razorpayOrder->currency,
            ]);

            $bookingTransaction = BookingTransaction::create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'razorpay_id' => $razorpayOrder->id,
                'transaction_id' => $fetchPayment->receipt,
                'amount' => ($fetchPayment->amount_paid / 100),
                'wallet_used' => $bookingData->wallet ?? 0,
                'status' => $fetchPayment->status,
                'remark' => 'Amount paid successfully',
                'currency' => $razorpayOrder->currency,
            ]);
            $razorpayOrder->update([
                'status' => 'captured'
            ]);
            $user->wallet = $user->wallet - $bookingData->wallet;
            $user->save();
            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => 'debit',
                'amount' => $bookingData->wallet,
                'previous_balance' => $user->wallet + $bookingData->wallet,
                'updated_balance' => $user->wallet,
                'remark' => 'Session payment',
                'currency' => $razorpayOrder->currency,
            ]);
            DB::commit();

            $firebaseService = new SendNotification();
            try {
                $firebaseService->sendNotification($user->device_id, 'Congratulations!', 'Your booking has been placed successfully!', $data ?? []);
                $firebaseService->sendNotification($astro->device_id, 'Congratulations!', 'You have a new booking!', $data ?? []);
            } catch (\Throwable $th) {
                \Log::info('Error sending notification: ' . $th->getMessage());
            }

            $response = [
                'success' => true,
                'status' => 'success',
                'message' => 'Booking has been placed successfully. Thank you!'
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

    public function generateBookingId()
    {
        $booking = Booking::whereNotNull('booking_uid')->orderBy('id', 'desc')->first();
        if (!$booking) {
            $bookingId = 'BK' . date('dmY') . '001';
            return $bookingId;
        }
        $bookingPrefix = substr($booking->booking_uid, 0, 2);
        $oldPrefix = $bookingPrefix  . date('dmY');
        $prefix = $bookingPrefix . date('dmY');
        $currentId = strtok($booking->booking_uid, ' ');
        $serialNumber = substr($currentId, strlen($oldPrefix));

        $newSerialNumber = (int)$serialNumber + 1;
        $newSerialNumber = str_pad($newSerialNumber, strlen($serialNumber), '0', STR_PAD_LEFT);
        $bookingId = $prefix . $newSerialNumber;
        return $bookingId;
    }
}
