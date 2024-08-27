<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //index
    public function index(Request $request)
    {
        $categories = Category::orderBy('name', 'ASC')->get();
        return response([
            'status' => 'success',
            'data' => $categories,
        ], 200);
    }
}
