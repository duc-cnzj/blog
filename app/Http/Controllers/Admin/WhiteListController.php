<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Rules\CheckEmptyArray;
use App\Contracts\WhiteListImp;
use App\Http\Controllers\Controller;

/**
 * Class WhiteListController
 * @package App\Http\Controllers\Admin
 */
class WhiteListController extends Controller
{
    /**
     * @param  WhiteListImp  $service
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(WhiteListImp $service)
    {
        return response()->json([
            'data' => $service->getItemLists(),
        ], 200);
    }

    /**
     * @param  WhiteListImp  $service
     * @param  Request  $request
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(WhiteListImp $service, Request $request)
    {
        $this->validate($request, [
            'items' => ['bail', 'array', new CheckEmptyArray()],
        ]);

        if ($service->addItemToList(...$request->input('items', []))) {
            return response('添加成功', 201);
        } else {
            return response([
                'error' => [
                    'code'    => 400,
                    'message' => '添加失败',
                ],
            ], 400);
        }
    }

    /**
     * @param  WhiteListImp  $service
     * @param  Request  $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(WhiteListImp $service, Request $request)
    {
        $this->validate($request, [
            'item' => 'string|required',
        ]);

        $service->deleteItems(($request->input('item', '')));

        return response('', 204);
    }
}
