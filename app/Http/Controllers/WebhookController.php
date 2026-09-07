<?php
namespace App\Http\Controllers;

use App\Models\Razorpay;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handlePayment(Request $request, WalletService $walletService)
    {
        // secret: testsecretwebhook

        if ($request['event'] == 'payment.captured') {
            $orderId       = $request['payload']['payment']['entity']['order_id'];
            $razorpayOrder = Razorpay::where('order_id', $orderId)
                ->whereIn('status', ['created', 'failed'])->where('type', 'wallet')->first();
            if ($razorpayOrder) {
                $user      = User::findOrFail($razorpayOrder->user_id);
                $orderData = json_decode($razorpayOrder->order_data);
                $walletService->paymentSuccessProcess($razorpayOrder, $user, $orderData);
            }
        } elseif ($request['event'] == 'payment.failed') {
            $orderId       = $request['payload']['payment']['entity']['order_id'];
            $razorpayOrder = Razorpay::where('order_id', $orderId)
                ->where('status', 'created')->where('type', 'wallet')->first();
            if ($razorpayOrder) {
                $walletService->paymentFailedProcess($razorpayOrder);
            }
        }
    }
}
