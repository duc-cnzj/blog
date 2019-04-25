<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Article;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class CommentController
 * @package App\Http\Controllers\Admin
 */
class CommentController extends Controller
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
        $comments = Comment::with(['article:id,title', 'userable'])
            ->whereHas('article', function (Builder $q) {
                $q->where('author_id', \Auth::id());
            })
            ->where('userable_type', '<>', User::class)
            ->latest()
            ->select('id', 'content', 'created_at', 'article_id', 'visitor')
            ->paginate($request->input('page_size') ?? 10);

        return CommentResource::collection($comments);
    }

    /**
     * @param int $articleId
     * @param Request $request
     * @return CommentResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(int $articleId, Request $request)
    {
        $content = $request->input('content');
        $parsedown = new \Parsedown();
        $htmlContent = $parsedown->text($content);

        /** @var Article $article */
        $article = Article::query()->findOrFail($articleId);

        $comment = $article->comments()->create([
            'visitor'          => $request->ip(),
            'content'          => $htmlContent,
            'comment_id'       => $request->input('comment_id', 0),
            'userable_id'      => \Auth::id(),
            'userable_type'    => User::class,
        ]);

        return new CommentResource($comment->load('userable'));
    }

    /**
     * @param int $id
     *
     * @return CommentResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function show(int $id)
    {
        $comment = Comment::with('article.author')->findOrFail($id);
        $userComments = $comment->replies()
            ->where('userable_id', \Auth::id())
            ->where('userable_type', '=', User::class)
            ->latest()
            ->get(['id', 'content']);

        return (new CommentResource($comment))->additional([
            'data' => [
                'my_comments' => $userComments,
            ],
        ]);
    }
}
