<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\Skill;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;
use App\Helpers\FileUploader;

class SkillController extends Controller
{
    public function index()
    {
        return view('content.tables.skills');
    }
    public function store(Request $request)
    {
        $data = new Skill;
        $data->name = $request->name;
        if ($request->icon) {
            $data->icon = FileUploader::uploadFile($request->icon, 'image/skills');
        }
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'skill added successfully',
            'table' => 'skill-table',
        ]);
    }
    public function edit($id)
    {
        $data = Skill::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $data = Skill::findOrFail($request->id);
        $data->name = $request->name;
        if ($request->icon) {
            $data->icon = FileUploader::uploadFile($request->icon, 'image/skills');
        }
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'skill updated successfully',
            'table' => 'skill-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:skills,id',
            'status' => 'required|in:active,blocked',
        ]);

        Skill::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'skill status updated successfully',
            'table' => 'skill-table',
        ]);
    }

    public function destroy($id)
    {
        Skill::findOrFail($id)->delete();
        return response([
            'header' => 'Success!',
            'message' => 'skill deleted successfully',
            'table' => 'skill-table',
        ]);
    }
}
