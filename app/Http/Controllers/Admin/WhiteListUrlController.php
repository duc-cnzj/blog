<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Rules\CheckEmptyArray;
use App\Contracts\WhiteListUrlImp;
use App\Http\Controllers\Controller;

/**
 * Class WhiteListController
 * @package App\Http\Controllers\Admin
 */
class WhiteListUrlController extends Controller
{
    /**
     * @param  WhiteListUrlImp  $service
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(WhiteListUrlImp $service)
    {
        return response()->json([
            'data' => $service->getItemLists(),
        ], 200);
    }

    /**
     * @param  WhiteListUrlImp  $service
     * @param  Request  $request
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(WhiteListUrlImp $service, Request $request)
    {
        $this->validate($request, [
            'items' => ['bail', 'array', new CheckEmptyArray()],
        ]);

        if ($service->addItemToList(...$request->input('items', []))) {
            return response()->json(['success' => true], 201);
        } else {
            return response([
                'error' => [
                    'code'    => 400,
                    'message' => 'fail',
                ],
            ], 400);
        }
    }

    /**
     * @param  WhiteListUrlImp  $service
     * @param  Request  $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(WhiteListUrlImp $service, Request $request)
    {
        $this->validate($request, [
            'item' => 'string|required',
        ]);

        $service->deleteItems(($request->input('item', '')));

        return response('', 204);
    }
}
