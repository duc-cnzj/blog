<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $category = Category::where('name', 'LIKE', "%{$request->q}%")->get();

        return CategoryResource::collection($category);
    }
}
