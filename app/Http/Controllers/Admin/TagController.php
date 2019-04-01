<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;

/**
 * Class TagController
 * @package App\Http\Controllers\Admin
 */
class TagController extends Controller
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
        $tag = Tag::where('name', 'LIKE', "%{$request->input('q')}%")->get();

        return TagResource::collection($tag);
    }
}
