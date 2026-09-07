<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $services = Service::active()->get();
        $products = Product::active()->get();
        return view('content.tables.banners.banners', compact('services', 'products'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'image'       => 'required|image|mimes:jpeg,png,jpg|max:250',
            'title'       => 'nullable|string',
            'description' => 'nullable',
            'type'        => 'required|in:global,service,wallet,product',
            'service'     => 'nullable|exists:services,id',
            'product'     => 'nullable|exists:products,id',
        ]);
        $data = new Banner;
        if ($request->image) {
            $data->image = FileUploader::optimizeUploadFile($request->image, 'images/banners');
        }
        $data->title       = $request->title;
        $data->description = $request->description;
        $data->type        = $request->type;
        $data->service_id  = $request->service;
        $data->product_id  = $request->product;
        $data->astro_id    = $request->astro_id;
        $data->save();
        return response([
            'header'  => 'Added!',
            'message' => 'banner Added successfully',
            'table'   => 'banner-table',
        ]);
    }
    public function edit($id)
    {
        $data = Banner::findOrFail($id);
        return response($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'image'       => 'nullable|image|mimes:jpeg,png,jpg|max:250',
            'title'       => 'nullable|string',
            'description' => 'nullable',
            'type'        => 'required|in:global,service,wallet,product',
            'service'  => 'nullable|exists:services,id',
            'product'  => 'nullable|exists:products,id',
        ]);
        $data = Banner::findOrFail($request->id);
        if ($request->image) {
            $data->image = FileUploader::optimizeUploadFile($request->image, '/images/banners');
        }
        $data->title       = $request->title;
        $data->description = $request->description;
        $data->type        = $request->type;
        $data->service_id  = $request->service ?? $data->service_id;
        $data->product_id  = $request->product ?? $data->product_id;
        $data->astro_id    = $request->astro_id ?? $data->astro_id;
        $data->save();
        return response([
            'header'  => 'Updated!',
            'message' => 'banner Updated successfully',
            'table'   => 'banner-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id'     => 'required|numeric|exists:banners,id',
            'status' => 'required|in:active,blocked',
        ]);

        Banner::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'banner status updated successfully',
            'table'   => 'banner-table',
        ]);
    }

    public function destroy($id)
    {
        Banner::findOrFail($id)->delete();
        return response([
            'header'  => 'Deleted!',
            'message' => 'banner deleted successfully',
            'table'   => 'banner-table',
        ]);
    }
}
