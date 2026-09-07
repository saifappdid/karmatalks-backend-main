<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    use ResponseTrait;
    public function getCategories(Request $request)
    {
        $request->validate([
            'type' => 'nullable|string|in:product,service,healing',
        ]);
        $categories = Category::active()->where('type', $request->type)->get();
        return $this->success(data: $categories, message: 'Categories fetched successfully');
    }
    public function getProducts(Request $request)
    {
        $request->validate([
            'category_id' => 'nullable|numeric|exists:categories,id',
            'type' => 'required|string|in:product,service,healing',
            'search' => 'nullable|string',
        ]);
        $category = Category::find($request->category_id);
        $products = Product::where('type', $request->type)
            ->when($category, function ($q) use ($category) {
                $q->where('category_id', $category->id);
            })
            ->with('variants')
            ->active()
            ->search(request(['search']))
            ->simplePaginate(20);
        // $products = $products->items();
        return $this->success(data: $products, message: 'Products/Services fetched successfully');
    }
    public function getProductDetail(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:products,id',
        ]);
        $product = Product::with(['category', 'variants', 'images'])->find($request->id);
        return $this->success(data: $product, message: 'Product/Services fetched successfully');
    }
}
