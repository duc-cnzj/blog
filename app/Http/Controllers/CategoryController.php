<?php

namespace App\Http\Controllers;

use App\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\Builder;

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
        return CategoryResource::collection(
            Category::query()
                ->whereHas('articles', function (Builder $q) {
                    $q->visible();
                })
                ->get(['name', 'id'])
        );
    }
}
