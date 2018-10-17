<?php

namespace App\Http\Controllers\Admin;

use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(Request $request)
    {
        $category = Category::where('name', 'LIKE', "%{$request->q}%")->get();

        return CategoryResource::collection($category);
    }
}
