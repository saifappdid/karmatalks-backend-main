<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\WalletPackage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;


class WalletPackageController extends Controller
{
    public function index()
    {
        return view('content.tables.walletpackages');
    }
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:national,international',
            'amount' => 'numeric|required_without:amount_dollar',
            'bonus' => 'nullable|numeric',
            'amount_dollar' => 'numeric|required_without:amount',
            'bonus_dollar' => 'nullable|numeric',
            'remark' => 'nullable|string',
            'for_new_user' => 'nullable|in:yes,no',
        ]);
        $data = new WalletPackage;
        $data->type = $request->type;
        $data->amount = $request->amount;
        $data->bonus = $request->bonus;
        $data->amount_dollar = $request->amount_dollar;
        $data->bonus_dollar = $request->bonus_dollar;
        $data->remark = $request->remark;
        $data->for_new_user = $request->for_new_user;
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'walletpackage added successfully',
            'table' => 'walletpackage-table',
        ]);
    }
    public function edit($id)
    {
        $data = WalletPackage::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:wallet_packages,id',
            'type' => 'required|in:national,international',
            'amount' => 'numeric|required_without:amount_dollar',
            'bonus' => 'nullable|numeric',
            'amount_dollar' => 'numeric|required_without:amount',
            'bonus_dollar' => 'nullable|numeric',
            'remark' => 'nullable|string',
            'for_new_user' => 'nullable|in:yes,no',
        ]);
        $data = WalletPackage::findOrFail($request->id);
        $data->type = $request->type;
        $data->amount = $request->amount;
        $data->bonus = $request->bonus;
        $data->amount_dollar = $request->amount_dollar;
        $data->bonus_dollar = $request->bonus_dollar;
        $data->remark = $request->remark;
        $data->for_new_user = $request->for_new_user;
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'walletpackage updated successfully',
            'table' => 'walletpackage-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:wallet_packages,id',
            'status' => 'required|in:active,blocked',
        ]);

        WalletPackage::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'walletpackage status updated successfully',
            'table' => 'walletpackage-table',
        ]);
    }

    public function destroy($id)
    {
        WalletPackage::findOrFail($id)->delete();
        return response([
            'header' => 'Success!',
            'message' => 'walletpackage deleted successfully',
            'table' => 'walletpackage-table',
        ]);
    }

    public function forNewUser(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:wallet_packages,id',
            'status' => 'required|in:active,blocked',
        ]);

        WalletPackage::findOrFail($request->id)->update(['for_new_user' => $request->status == 'active' ? 'yes' : 'no']);

        return response([
            'message' => 'wallet package for new user status updated successfully',
            'table' => 'walletpackage-table',
        ]);
    }
}
