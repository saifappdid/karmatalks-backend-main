<?php

namespace App\Http\Controllers\Admin;

use App\Models\Razorpay;
use Illuminate\Http\Request;
use App\Services\BookingService;
use App\Http\Controllers\Controller;

class RazorpayWebhookController extends Controller
{
    public function fetchBookingPayment(Request $request)
    {
        $webhookData = $request->all();
        $service = new BookingService();
        if ($webhookData['event'] == "order.paid") {
            $orderId = $webhookData['payload']['payment']['entity']['order_id'];
            $razorpayOrder = Razorpay::where('order_id', $orderId)
                ->where('status', 'created')->first();
            $order   = $service->fetchBooking($razorpayOrder);
            return $order;
        } elseif ($webhookData['event'] == "payment.failed") {
            $orderId = $webhookData['payload']['payment']['entity']['order_id'];
            $razorpayOrder = Razorpay::where('order_id', $orderId)
                ->where('status', 'created')->first();
            $order   = $service->fetchBooking($razorpayOrder);
            return $order;
        } else {
            $orderId = $webhookData['payload']['payment']['entity']['order_id'];
            $razorpayOrder = Razorpay::where('order_id', $orderId)
                ->where('status', 'created')->first();
            $order   = $service->fetchBooking($razorpayOrder);
            return $order;
        }
    }
}
