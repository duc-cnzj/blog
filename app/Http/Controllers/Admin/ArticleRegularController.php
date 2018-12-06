<?php

namespace App\Http\Controllers\Admin;

use App\ArticleRegular;
use App\Rules\RuleFormat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleRegularResource;

class ArticleRegularController extends Controller
{
    public function index()
    {
        $regulars = ArticleRegular::where('user_id', \Auth::id())
            ->paginate($request->page_size ?? 10);

        return ArticleRegularResource::collection($regulars);
    }

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

    public function destroy(int $id)
    {
        ArticleRegular::findOrFail($id)->delete();

        return response([], 204);
    }

    public function test(Request $request)
    {
        $body = $request->input('body');

        return response([
            'data' => [
                'body' => app()->makeWith('rule', [$body])->apply(),
            ],
        ], 200);
    }

    public function changeStatus(Request $request)
    {
        $rule = ArticleRegular::findOrFail($request->input('id'));

        $rule->update([
            'status' => !$rule->status
        ]);

        return response([], 204);
    }
}
