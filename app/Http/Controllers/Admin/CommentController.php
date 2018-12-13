<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $comments = Comment::with(['article:id,title', 'user'])
            ->whereHas('article', function ($q) {
                $q->where('author_id', \Auth::id());
            })
            ->where('user_id', '<>', \Auth::id())
            ->latest()
            ->select('id', 'content', 'created_at', 'article_id', 'visitor')
            ->paginate($request->input('page_size') ?? 10);

        return CommentResource::collection($comments);
    }

    public function store($articleId, Request $request)
    {
        $content = $request->input('content');
        $parsedown = new \Parsedown();
        $htmlContent = $parsedown->text($content);

        /** @var Article $article */
        $article = Article::findOrFail($articleId);

        $comment = $article->comments()->create([
            'visitor'    => $request->ip(),
            'content'    => $htmlContent,
            'comment_id' => $request->input('comment_id', 0),
            'user_id'    => \Auth::id(),
        ]);

        return new CommentResource($comment->load('user'));
    }

    public function show($id)
    {
        $comment = Comment::with('article.author')->findOrFail($id);
        $userComments = $comment->reply()
            ->where('user_id', \Auth::id())
            ->latest()
            ->get(['id', 'content']);

        return (new CommentResource($comment))->additional([
            'data' => [
                'my_comments' => $userComments,
            ],
        ]);
    }
}
