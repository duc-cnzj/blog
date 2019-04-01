<?php

namespace App\Http\Controllers;

use App\Article;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\CommentResource;

/**
 * Class CommentController
 * @package App\Http\Controllers
 */
class CommentController extends Controller
{
    /**
     * @param         $id
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @author duc <1025434218@qq.com>
     */
    public function index($id, Request $request)
    {
        $comments = Comment::where('article_id', $id)
            ->get([
                'visitor',
                'content',
                'comment_id',
                'created_at',
                'id',
                'userable_id',
                'userable_type',
            ]);

        return response()->json([
            'data' => array_reverse(
                c(CommentResource::collection($comments)->toArray($request))
            ),
        ], 200);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return CommentResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(Request $request, int $id)
    {
        $article = Article::visible()->findOrFail($id);
        $content = $request->input('content');
        $parsedown = new \Parsedown();
        $htmlContent = $parsedown->text($content);
        $user = getAuthUser();

        /** @var Article $article */
        $comment = $article->comments()->create([
            'visitor'          => $request->ip(),
            'content'          => $htmlContent,
            'comment_id'       => $request->input('comment_id', 0),
            'userable_id'      => is_null($user) ? 0 : $user->id,
            'userable_type'    => is_null($user) ? '' : get_class($user),
        ]);

        return (new CommentResource($comment->load('userable')))
            ->additional([
                'data' => [
                    'replies' => [],
                ],
            ]);
    }
}
