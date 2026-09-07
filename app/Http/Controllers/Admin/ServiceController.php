<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;
use App\Helpers\FileUploader;

class ServiceController extends Controller
{
    public function index()
    {
        return view('content.tables.services.services');
    }
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|mimes:png,jpg,jpeg,webp,gif',
            'title' => 'required|string',
            'description' => 'required'
        ]);
        $data = new Service;

        $data->image = FileUploader::uploadFile($request->image, 'services');
        $data->title = $request->title;
        $data->description = $request->description;
        $data->save();
        return response([
            'header' => 'Added!',
            'message' => 'service Added successfully',
            'table' => 'service-table',
        ]);
    }
    public function edit($id)
    {
        $data = Service::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'image' => 'nullable|mimes:png,jpg,jpeg,webp,gif',
            'title' => 'required|string',
            'description' => 'required'
        ]);
        $data = Service::findOrFail($request->id);
        if ($request->image) {
            $data->image = FileUploader::uploadFile($request->image, 'services');
        }
        $data->title = $request->title;
        $data->description = $request->description;
        $data->save();
        return response([
            'header' => 'Updated!',
            'message' => 'service Updated successfully',
            'table' => 'service-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:services,id',
            'status' => 'required|in:active,blocked',
        ]);

        Service::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'service status updated successfully',
            'table' => 'service-table',
        ]);
    }

    public function destroy($id)
    {
        Service::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'service deleted successfully',
            'table' => 'service-table',
        ]);
    }

    public function rank(Request $request)
    {
        $services = Service::orderBy('rank', 'asc')->get();
        return view('content.tables.services.rank', compact('services'));
    }
    public function postRank(Request $request)
    {
        $data = $request->services;
        foreach ($data as $key => $value) {
            Service::where('id', $value['id'])->update([
                'rank' => $value['position']
            ]);
        }

        return response([
            'header' => 'Success!',
            'message' => 'Service rank updated!',
            'table' => 'service-table'
        ]);
    }
}
