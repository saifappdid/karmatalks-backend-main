<?php

namespace App\Http\Controllers\Admin;

use Hash;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Excel;
use App\Exports\GeneralExport;
use App\Helpers\FileUploader;

class CategoryController extends Controller
{
    public function index()
    {
        return view('content.tables.products.categorys');
    }
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'type' => 'required|string|max:191',
            'title' => 'required|string|max:191',
            'image' => 'nullable',
        ]);
        $data = new Category;
        $data->type = $request->type;
        $data->title = $request->title;
        if ($request->image) {
            $data->image = FileUploader::uploadFile($request->image, 'category');
        }
        // dd($data);
        $data->save();
        return response([
            'header' => 'Added!',
            'message' => 'category Added successfully',
            'table' => 'category-table',
        ]);
    }
    public function edit($id)
    {
        $data = Category::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:191',
            'title' => 'required|string|max:191',
            'image' => 'nullable',
        ]);

        $data = Category::findOrFail($request->id);
        $data->type = $request->type;
        $data->title = $request->title;
        if ($request->hasFile('image')) {
            $data->image = FileUploader::uploadFile($request->image, 'category');
        }
        $data->save();
        return response([
            'header' => 'Updated!',
            'message' => 'category Updated successfully',
            'table' => 'category-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:categories,id',
            'status' => 'required|in:active,blocked',
        ]);

        Category::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'category status updated successfully',
            'table' => 'category-table',
        ]);
    }

    public function destroy($id)
    {
        Category::findOrFail($id)->delete();
        return response([
            'header' => 'Deleted!',
            'message' => 'category deleted successfully',
            'table' => 'category-table',
        ]);
    }
}
