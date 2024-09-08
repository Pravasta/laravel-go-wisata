<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        $products = Product::with('category')->when($request->status, function ($query) use ($request) {
            $query->where('status', 'like', "%{$request->status}%");
        })->orderBy('favorite', 'desc')->get();
        return response([
            'status' => 'success',
            'data' => $products,
        ], 200);
    }
    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            // 'image' => 'required',
            // 'status' => 'required',
            'criteria' => 'required',
            // 'favorite' => 'required',
        ]);

        // insertImage
        $product = new Product;
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->description = '';
        $product->price = $request->price;
        $product->stock = 0;

        $product->status = 'published';
        $product->criteria = $request->criteria;
        $product->favorite = false;
        $product->save();

        if ($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->extension());
            $product->image = 'products/' . $product->id . '.' . $image->extension();
            $product->save();
        }

        $product = Product::with('category')->find($product->id);

        return response([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }

    // SHOW
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        return response([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        // $product->category_id = $request->category_id;
        $product->name = $request->name;
        // $product->description = $request->description;
        $product->price = $request->price;
        // $product->stock = $request->stock;
        // $product->status = $request->status;
        // $product->criteria = $request->criteria;
        // $product->favorite = $request->favorite;
        $product->save();

        // if ($request->image) {
        //     $image = $request->file('image');
        //     $image->storeAs('public/products', $product->id . '.' . $image->extension());
        //     $product->image = 'products/' . $product->id . '.' . $image->extension();
        //     $product->save();
        // }

        $product = Product::with('category')->find($product->id);

        return response([
            'status' => 'success',
            'data' => $product,
        ], 200);
    }

    // Delete
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response([
                'status' => 'error',
                'message' => 'Product not found',
            ], 404);
        }

        $product->delete();
        return response([
            'status' => 'success',
            'message' => 'Product Deleted',
        ], 200);
    }
}
