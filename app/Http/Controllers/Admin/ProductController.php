<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\FileUploader;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    public function index()
    {
        $pageConfigs = ['has_icon' => true];
        return view('content.tables.products.products', compact('pageConfigs'));
    }
    public function store(Request $request)
    {
        $rules = [
            'type'        => 'required|string|max:191',
            'category_id' => 'nullable|numeric|exists:categories,id',
            'title'       => 'required|string|max:191',
            'image'       => 'nullable',
            'description' => 'nullable|string',
            'images'      => 'nullable|array|min:1',
            'images.*'    => 'nullable|image|max:1024|mimes:jpeg,png,jpg,gif,webp',
        ];
        if ($request->type == 'service') {
            $rules['price'] = 'required|numeric|min:0';
        } elseif ($request->type == 'product') {
            $rules['variants']                    = 'required|array|min:1';
            $rules['variants.*.title']            = 'required|string|max:191';
            $rules['variants.*.mrp_price']        = 'required|numeric|min:0';
            $rules['variants.*.price']            = 'required|numeric|min:0';
            $rules['variants.*.mrp_price_dollar'] = 'required|numeric|min:0';
            $rules['variants.*.price_dollar']     = 'required|numeric|min:0';
        }
        $validatedData = $request->validate($rules);
        DB::beginTransaction();
        $product = Product::create([
            'type'        => $request->type,
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'image'       => $request->hasFile('image')
                ? FileUploader::uploadFile($request->file('image'), 'product')
                : null,
            'description' => $request->description,
            'price_dollar'       => $request->price_dollar,
            'sku'         => $request->sku ?? null,
        ]);

        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'title'            => $variant['title'],
                    'mrp_price'        => $variant['mrp_price'],
                    'price'            => $variant['price'],
                    'mrp_price_dollar' => $variant['mrp_price_dollar'],
                    'price_dollar'     => $variant['price_dollar'],
                    'product_id'       => $product->id,
                ]);
            }
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => FileUploader::uploadFile($image, 'product'),
                ]);
            }
        }
        DB::commit();
        return response([
            'header'  => 'Added!',
            'message' => 'product Added successfully',
            'table'   => 'product-table',
        ]);
    }
    public function edit($id)
    {
        $pageConfigs = ['has_icon' => true];
        $data        = Product::with(['category', 'variants', 'images'])->findOrFail($id);

        return view('content.tables.products.edit', compact('data', 'pageConfigs'));
        // return response($data);
    }

    public function update(Request $request)
    {
        $rules = [
            'id'          => 'required|exists:products,id',
            'type'        => 'required|string|max:191',
            'category_id' => 'nullable|numeric|exists:categories,id',
            'title'       => 'required|string|max:191',
            'image'       => 'nullable',
            'description' => 'nullable|string',
        ];

        if ($request->type == 'service') {
            $rules['price'] = 'required|numeric|min:0';
        } elseif ($request->type == 'product') {
            $rules['variants']         = 'required|array|min:1';
            $rules['variants.*.title'] = 'required|string|max:191';
            $rules['variants.*.mrp_price']        = 'required|numeric|min:0';
            $rules['variants.*.price']            = 'required|numeric|min:0';
            $rules['variants.*.mrp_price_dollar'] = 'required|numeric|min:0';
            $rules['variants.*.price_dollar']     = 'required|numeric|min:0';
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        $product = Product::findOrFail($request->id);
        $product->update([
            'type'        => $request->type,
            'category_id' => $request->category_id,
            'title'       => $request->title,
            'image'       => isset($request->image)
                ? FileUploader::uploadFile($request->image, 'product')
                : $product->image,
            'description' => $request->description,
            'price'       => $request->type == 'service' ? $request->price : $product->price,
            'price_dollar'       => $request->type == 'service' ? $request->price_dollar : $product->price_dollar,
            'sku'         => $request->sku ?? $product->sku,
        ]);
        if ($request->type == 'product') {
            ProductVariant::where('product_id', $product->id)->delete();
            foreach ($request->variants as $variant) {
                ProductVariant::create([
                    'title'      => $variant['title'],
                    'mrp_price'        => $variant['mrp_price'],
                    'price'            => $variant['price'],
                    'mrp_price_dollar' => $variant['mrp_price_dollar'],
                    'price_dollar'     => $variant['price_dollar'],
                    'product_id' => $product->id,
                ]);
            }
        } else {
            ProductVariant::where('product_id', $product->id)->delete();
        }

        if ($request->has('images')) {
            foreach ($request->images as $key => $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image'      => FileUploader::uploadFile($image, 'product'),
                ]);
            }
        }

        DB::commit();

        return response([
            'header'  => 'Updated!',
            'message' => 'Product updated successfully',
            'table'   => 'product-table',
        ]);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id'     => 'required|numeric|exists:products,id',
            'status' => 'required|in:active,blocked',
        ]);

        Product::findOrFail($request->id)->update(['status' => $request->status]);

        return response([
            'message' => 'product status updated successfully',
            'table'   => 'product-table',
        ]);
    }

    public function destroy($id)
    {
        Product::findOrFail($id)->delete();
        return response([
            'header'  => 'Deleted!',
            'message' => 'product deleted successfully',
            'table'   => 'product-table',
        ]);
    }

    public function getCategory(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string|in:product,service,healing',
        ]);
        $categories = Category::active()->where('type', $request->type)->get();
        return response($categories);
    }

    public function deleteImage($id)
    {
        $image = ProductImage::where('id', $id)->first();
        if ($image) {
            $image->delete();
            return response([
                'header'  => 'Deleted!',
                'message' => 'Image deleted successfully',
                'table'   => 'product-table',
            ]);
        } else {
            throw ValidationException::withMessages(['message' => 'Image not found']);
        }
    }
}
