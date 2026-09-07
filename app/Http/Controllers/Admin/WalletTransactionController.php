<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\WalletTransaction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class WalletTransactionController extends Controller
{
    public function index()
    {
        return view('content.tables.wallet.transactions');
    }

    public function update(Request $request)
    {
        $request->validate([
            'user' => 'required|numeric|exists:users,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);
        $user = User::findOrFail($request->user);
        if ($request->type == 'debit' && $request->amount > $user->wallet) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient wallet balance to debit.',
            ]);
        }
        $order = WalletTransaction::create([
            'type' => $request->type,
            'user_id' => $user->id,
            'amount' => $request->amount,
            'previous_balance' => $user->wallet,
            'updated_balance' => $request->type == 'credit' ? $user->wallet + $request->amount : $user->wallet - $request->amount,
            'remark' => $request->reason,
        ]);
        $request->type == 'credit' ? $user->increment('wallet', $request->amount) : $user->decrement('wallet', $request->amount);
        return response([
            'header' => 'Success!',
            'message' => 'Wallet updated',
            'reload' => 'true',
        ]);
    }
}
