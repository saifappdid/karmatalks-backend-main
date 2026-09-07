<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;

class BusinessSettingController extends Controller
{
    public function index()
    {
        $data = BusinessSetting::pluck('value', 'key');
        return view('content.pages.business-settings', compact('data'));
    }

    public function store(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            BusinessSetting::updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ]
            );
        }
        return response([
            'header' => 'Updated!',
            'message' => 'Settings Updated Successfully'
        ]);
    }
}
