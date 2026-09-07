<?php

namespace App\Http\Controllers\API\v1;

use App\Models\User;
use App\Models\Order;
use App\Models\Booking;
use App\Models\Razorpay;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Services\OrderService;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    use ResponseTrait;
    public function createOrder(Request $request, OrderService $orderService)
    {
        $request->validate([
            'product_id' => 'required|numeric|exists:products,id',
            'variation_id' => 'nullable|numeric|exists:product_variants,id',
            'address_id' => 'required|numeric|exists:addresses,id',
            'promocode' => 'nullable|string',
            'payment_mode' => 'required|string|in:online,cod',
            'wallet_used' => 'nullable|in:yes,no',
        ]);
        $user = auth('sanctum')->user();
        $order = $orderService->createOrder($request, $user);
        return $this->success($order, 'Order created successfully');
    }

    public function fetchOrder(Request $request, OrderService $orderService)
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);
        $user = User::findOrFail(auth('sanctum')->user()->id);
        $razorpayOrder = Razorpay::where('order_id', $request->order_id)
            ->where('user_id', $user->id)
            ->where('status', 'created')->first();
        if (!$razorpayOrder) {
            return response([
                'message' => 'Oops! No Order found!',
            ], 404);
        }
        $fetchPayment = $orderService->fetchOrder($razorpayOrder);
        return $fetchPayment;
    }

    public function myOrders()
    {
        $user = auth('sanctum')->user();
        $orders = Order::with(['orderDetails', 'transaction'])->where(['user_id' => $user->id])->orderBy('id', 'desc')->get();
        $orders = OrderResource::collection($orders);
        return $this->success($orders, 'Orders fetched successfully');
    }
    public function orderDetails(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
        ]);
        $order = Order::with(['orderDetails', 'transaction'])->where(['id' => $request->id, 'user_id' => auth('sanctum')->id()])->first();
        if (!$order) {
            return response(['success' => false, 'message' => 'No order found'], 400);
        }
        $order = new OrderResource($order);
        return $this->success($order, 'Order detail fetched successfully');
    }
}
