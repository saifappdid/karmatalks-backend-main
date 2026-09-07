<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;


class LanguageController extends Controller
{
    public function index()
    {
        return view('content.tables.languages.index');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:languages,name',
        ]);
        $data = new Language;
        $data->name = $request->name;
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'language added successfully',
            'table' => 'language-table',
        ]);
    }
    public function edit($id)
    {
        $data = Language::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:languages,id',
            'name' => 'required|unique:languages,name,' . $request->id,
        ]);
        $data = Language::findOrFail($request->id);
        $data->name = $request->name;
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'language updated successfully',
            'table' => 'language-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:languages,id',
            'status' => 'required|in:active,blocked',
        ]);

        Language::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'language status updated successfully',
            'table' => 'language-table',
        ]);
    }

    public function destroy($id)
    {
        Language::findOrFail($id)->delete();
        return response([
            'header' => 'Success!',
            'message' => 'language deleted successfully',
            'table' => 'language-table',
        ]);
    }
}
