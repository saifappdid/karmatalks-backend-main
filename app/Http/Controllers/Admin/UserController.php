<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        return view('content.tables.appusers.users');
    }
    public function store(Request $request)
    {
        $data = new User;
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->save();

        return response([
            'header' => 'Added!',
            'message' => 'users added successfully',
            'table' => 'users-table',
        ]);
    }
    public function edit($id)
    {
        $data = User::findOrFail($id);
        // dd($data);
        return response($data);
    }

    public function update(Request $request)
    {
        $data = User::findOrFail($request->id);
        $data->name = $request->name;
        $data->phone = $request->phone;
        $data->email = $request->email;
        $data->save();

        return response([
            'header' => 'Updated!',
            'message' => 'users updated successfully',
            'table' => 'users-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:users,id',
            'status' => 'required|in:active,blocked',
        ]);

        User::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'users status updated successfully',
            'table' => 'users-table',
        ]);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'users deleted successfully',
            'table' => 'users-table',
        ]);
    }

    public function profile($id)
    {
        $pageConfigs = ['has_icon' => true];
        $user = User::with('walletTransaction', 'instantConsultationHistory')->findOrFail($id);
        // return $user;
        return view('content.tables.appusers.profile', compact('user', 'pageConfigs'));
    }
}
