<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('content.tables.orders.index');
    }

    public function show($id)
    {
        $configs = ['has_icons' => true];
        $order = Order::with(['user', 'orderDetails'])->where('id', $id)->first();
        return view('content.tables.orders.show', compact('order', 'configs'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:orders,id',
            'status' => 'required|in:confirmed,shipped,delivered,cancelled',
        ]);

        $order = Order::find($request->id);
        $order->status = $request->status;
        if ($request->status == 'delivered') {
            $order->delivered_at = now();
        }
        if ($request->status == 'cancelled') {
            $order->cancelled_at = now();
        }
        if ($request->status == 'shipped') {
            $order->shipped_at = now();
        }
        $order->save();

        return response([
            'header' => 'Success!',
            'message' => 'Status updated successfully',
        ]);
    }
}
