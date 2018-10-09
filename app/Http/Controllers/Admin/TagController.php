<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use Illuminate\Http\Request;
use App\Http\Resources\TagResource;
use App\Http\Controllers\Controller;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $tag = Tag::where('name', 'LIKE', "%{$request->q}%")->get();

        return TagResource::collection($tag);
    }
}
