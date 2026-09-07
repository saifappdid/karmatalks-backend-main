<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Razorpay;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class WalletController extends Controller
{
    use ResponseTrait;
    public function addWallet(Request $request, WalletService $walletService)
    {
        $request->validate([
            'package_id' => 'required|numeric|exists:wallet_packages,id',
        ]);

        $getBoughtPackages = WalletTransaction::where('user_id', auth('sanctum')->user()->id)
            ->where('package_id', $request->package_id)->whereHas('package', function ($q) {
            $q->where('for_new_user', 'yes');
        })
            ->whereIn('status', ['completed'])
            ->first();

        if ($getBoughtPackages) {
            throw ValidationException::withMessages(['package_id' => 'This package is only for new users and you have already purchased it.']);
        }

        $user  = auth('sanctum')->user();
        $order = $walletService->addWallet($request, $user);
        return $this->success($order, 'Order created successfully');
    }

    public function fetchWallet(Request $request, WalletService $walletService)
    {
        $request->validate([
            'razorpay_order_id' => 'required|exists:razorpays,order_id',
        ]);
        sleep(5); // Wait for 5 seconds to ensure the webhook has processed the payment
        $razorpayOrder = Razorpay::where('order_id', $request->razorpay_order_id)->where('type', 'wallet')->first();
        if ($razorpayOrder) {
            if ($razorpayOrder?->status == 'created') {
                $response = $walletService->fetchWallet($razorpayOrder);
            } else if ($razorpayOrder->status == 'captured') {
                $response = [
                    'success' => true,
                    'status'  => 'success',
                    'message' => 'Order has been placed successfully. Thank you!',
                ];
            } else if ($razorpayOrder->status == 'failed') {
                $response = [
                    'success' => false,
                    'status'  => 'failed',
                    'message' => 'Oops! Due to technical issue your payment get failed contact with administrator',
                ];
            } else {
                throw ValidationException::withMessages(['razorpay_order_id' => 'Invalid Razorpay order ID.']);
            }
            return $this->success($response, 'Wallet fetched successfully');
        }
    }

    public function walletTransactions(Request $request)
    {
        $userId  = auth('sanctum')->user()->id;
        $wallets = WalletTransaction::where('user_id', $userId)->orderBy('id', 'desc')->simplePaginate(10);
        return $this->success($wallets->items(), 'Wallet transactions fetched successfully');
    }
}
