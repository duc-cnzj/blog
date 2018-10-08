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

    public function store(Request $request)
    {
        $content = $request->content;
        $parsedown = new \Parsedown();

        $mdContent = $parsedown->text($content);

        $processContent = json_encode([
            'html' => $mdContent,
            'md' => $content
        ]);

        $article = Article::create([
            'author_id' => \Auth::id(),
            'head_image' => $request->head_image,
            'title' => $request->title,
            'desc' => $request->desc,
            'content' => $processContent,
        ]);

        return new ArticleResource($article);
    }
}
