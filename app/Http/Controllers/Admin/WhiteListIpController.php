<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Contracts\WhiteListIpImp;
use App\Http\Controllers\Controller;

/**
 * Class WhiteListController
 * @package App\Http\Controllers\Admin
 */
class WhiteListIpController extends Controller
{
    /**
     * @param  WhiteListIpImp  $service
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(WhiteListIpImp $service)
    {
        return response()->json([
            'data' => $service->getItemLists(),
        ], 200);
    }

    /**
     * @param  WhiteListIpImp  $service
     * @param  Request  $request
     * @return bool
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(WhiteListIpImp $service, Request $request)
    {
        $this->validate($request, [
            'item' => 'required|ip',
        ]);

        if ($service->addItemToList($request->input('item', ''))) {
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
     * @param  WhiteListIpImp  $service
     * @param  Request  $request
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(WhiteListIpImp $service, Request $request)
    {
        $this->validate($request, [
            'item' => 'string|required|ip',
        ]);

        $service->deleteItems(($request->input('item', '')));

        return response('', 204);
    }
}
