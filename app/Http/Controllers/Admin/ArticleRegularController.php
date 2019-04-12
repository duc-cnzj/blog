<?php

namespace App\Http\Controllers\Admin;

use App\ArticleRegular;
use App\Rules\RuleFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleRegularResource;

/**
 * Class ArticleRegularController
 * @package App\Http\Controllers\Admin
 */
class ArticleRegularController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index()
    {
        $regulars = ArticleRegular::where('user_id', \Auth::id())
            ->get();

        return ArticleRegularResource::collection($regulars);
    }

    /**
     * @param Request $request
     * @return ArticleRegularResource
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'rule' => new RuleFormat,
        ]);

        $regular = ArticleRegular::create([
            'user_id' => \Auth::id(),
            'rule'    => $request->input('rule'),
            'status'  => $request->input('status', true),
        ]);

        return new ArticleRegularResource($regular);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(int $id)
    {
        $regular = ArticleRegular::findOrFail($id);

        $this->authorize('own', $regular);

        $regular->delete();

        return response('', 204);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @author duc <1025434218@qq.com>
     */
    public function test(Request $request)
    {
        $body = $request->input('body');

        return response()->json([
            'data' => [
                'body' => app()->makeWith('rule', [$body])->apply(),
            ],
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function changeStatus(Request $request)
    {
        $rule = ArticleRegular::findOrFail($request->input('id'));

        $this->authorize('own', $rule);

        $rule->update(['status' => ! $rule->status]);

        return response('', 204);
    }
}
