<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\InstantConsultationHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;


class InstantConsultationHistoryController extends Controller
{
    public function index()
    {
        return view('content.tables.instant-consultation.index');
    }
    public function store(Request $request)
    {
        $data = new InstantConsultationHistory;

        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'instantconsultationhistory added successfully',
            'table' => 'instantconsultationhistory-table',
        ]);
    }
    public function edit($id)
    {
        $data = InstantConsultationHistory::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $data = InstantConsultationHistory::findOrFail($request->id);
        $data->save();
        return response([
            'header' => 'Success!',
            'message' => 'instantconsultationhistory updated successfully',
            'table' => 'instantconsultationhistory-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:instantconsultationhistorys,id',
            'status' => 'required|in:active,blocked',
        ]);

        InstantConsultationHistory::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'instantconsultationhistory status updated successfully',
            'table' => 'instantconsultationhistory-table',
        ]);
    }

    public function destroy($id)
    {
        InstantConsultationHistory::findOrFail($id)->delete();
        return response([
            'header' => 'Success!',
            'message' => 'instantconsultationhistory deleted successfully',
            'table' => 'instantconsultationhistory-table',
        ]);
    }

    public function chatHistory($id)
    {
        $pageConfigs = ['has_icons' => true];
        $history = InstantConsultationHistory::with(['user', 'astro', 'chats'])->where('id', $id)->first();
        return view('content.tables.instant-consultation.chat', compact('history', 'pageConfigs'));
    }
}
