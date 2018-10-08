<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // return \Auth::user();
        $articles = Article::where('author_id', \Auth::id())
            ->latest()
            ->select('id', 'title', 'created_at', 'updated_at')
            ->paginate($request->page_size ?? 10);

        return ArticleResource::collection($articles);
    }
}
