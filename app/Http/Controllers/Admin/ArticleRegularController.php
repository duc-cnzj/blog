<?php

namespace App\Http\Controllers\Admin;

use App\ArticleRegular;
use App\Rules\RuleFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleRegularResource;

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
     *
     * @return ArticleRegularResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(Request $request)
    {
        info('input', $request->input());
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
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(int $id)
    {
        ArticleRegular::findOrFail($id)->delete();

        return response([], 204);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @author duc <1025434218@qq.com>
     */
    public function test(Request $request)
    {
        $body = $request->input('body');

        return response([
            'data' => [
                'body' => app()->makeWith('rule', [$body])->apply(),
            ],
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @author duc <1025434218@qq.com>
     */
    public function changeStatus(Request $request)
    {
        $rule = ArticleRegular::findOrFail($request->input('id'));

        $rule->update([
            'status' => ! $rule->status,
        ]);

        return response([], 204);
    }
}
