<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //index
    public function index(Request $request)
    {
        $products = Product::when($request->keyword, function ($query) use ($request) {
            $query->where('name', 'like', "%{$request->keyword}%")
                ->orWhere('description', 'like', "%{$request->keyword}%");
        })->orderBy('id', 'desc')->paginate(10);
        return view('pages.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('pages.products.create', compact('categories'));
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'image' => 'required',
            'status' => 'required',
            'criteria' => 'required',
            'favorite' => 'required',
        ]);

        // insertImage
        $product = new Product;
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;

        $product->status = $request->status;
        $product->criteria = $request->criteria;
        $product->favorite = $request->favorite;
        $product->save();

        if ($request->file('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->extension());
            $product->image = 'products/' . $product->id . '.' . $image->extension();
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    // Edit
    public function edit(Product $product)
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        return view('pages.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;

        $product->status = $request->status;
        $product->criteria = $request->criteria;
        $product->favorite = $request->favorite;
        $product->save();

        if ($request->image) {
            $image = $request->file('image');
            $image->storeAs('public/products', $product->id . '.' . $image->extension());
            $product->image = 'products/' . $product->id . '.' . $image->extension();
            $product->save();
        }

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    // Delete
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
