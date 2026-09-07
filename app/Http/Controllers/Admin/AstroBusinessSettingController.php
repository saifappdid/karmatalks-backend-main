<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\AstroBusinessSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;
use App\Models\Astro;

class AstroBusinessSettingController extends Controller
{
    public function index()
    {
        $businessSettings = AstroBusinessSetting::pluck('value', 'key')->toArray();
        return view('content.pages.astrobusinesssettings', compact('businessSettings'));
    }
    public function store(Request $request)
    {
        $data = $request->all();

        foreach ($data as $key => $value) {
            AstroBusinessSetting::updateOrInsert(['key' => $key], ['value' => $value]);
        }
        return response([
            'header' => 'Success!',
            'message' => 'Astro business setting updated successfully',
            'table' => 'astrobusinesssetting-table',
        ]);
    }
    public function edit($id)
    {
        $data = AstroBusinessSetting::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $data = AstroBusinessSetting::findOrFail($request->id);
        $data->key = $request->key;
        $data->value = $request->value;
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'astrobusinesssetting updated successfully',
            'table' => 'astrobusinesssetting-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:astrobusinesssettings,id',
            'status' => 'required|in:active,blocked',
        ]);

        AstroBusinessSetting::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'astrobusinesssetting status updated successfully',
            'table' => 'astrobusinesssetting-table',
        ]);
    }

    public function destroy($id)
    {
        AstroBusinessSetting::findOrFail($id)->delete();
        return response([
            'header' => 'Success!',
            'message' => 'astrobusinesssetting deleted successfully',
            'table' => 'astrobusinesssetting-table',
        ]);
    }
}
