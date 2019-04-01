<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;

/**
 * Class CategoryController
 * @package App\Http\Controllers
 */
class CategoryController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index()
    {
        return CategoryResource::collection(Category::all(['name', 'id']));
    }
}
