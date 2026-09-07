<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\Promocode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;


class PromocodeController extends Controller
{
    public function index()
    {
        return view('content.tables.promocode.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'promocode' => 'required',
            'message' => 'required',
            'minimum_order_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required',
            'max_discount_amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'discount_type' => 'required',
        ]);
        Promocode::create([
            'code' => strtoupper($request->promocode),
            'message' => $request->message,
            'start' => $request->start_date,
            'end' => $request->end_date,
            'minimum_order_amount' => $request->minimum_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
        ]);
        return response([
            'header' => 'Success!',
            'message' => 'promocode added successfully',
            'table' => 'promocode-table',
        ]);
    }
    public function edit($id)
    {
        $data = Promocode::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:promocodes,id',
            'promocode' => 'required',
            'message' => 'required',
            'minimum_order_amount' => 'required|numeric',
            'start_date' => 'required|date',
            'end_date' => 'required',
            'max_discount_amount' => 'required|numeric',
            'discount' => 'required|numeric',
            'discount_type' => 'required',
        ]);
        Promocode::findOrFail($request->id)->update([
            'code' => strtoupper($request->promocode),
            'message' => $request->message,
            'start' => $request->start_date,
            'end' => $request->end_date,
            'minimum_order_amount' => $request->minimum_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'discount' => $request->discount,
            'discount_type' => $request->discount_type,
        ]);
        return response([
            'header' => 'Success!',
            'message' => 'promocode updated successfully',
            'table' => 'promocode-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:promocodes,id',
            'status' => 'required|in:active,blocked',
        ]);

        Promocode::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'promocode status updated successfully',
            'table' => 'promocode-table',
        ]);
    }

    public function destroy($id)
    {
        Promocode::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'promocode deleted successfully',
            'table' => 'promocode-table',
        ]);
    }
}
