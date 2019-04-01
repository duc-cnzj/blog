<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

/**
 * Class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController
{
    /**
     * @param string $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     *
     * @author duc <1025434218@qq.com>
     */
    public function fail(string $message, int $code)
    {
        return response()->json([
            'error' => [
                'code'    => $code,
                'message' => $message,
            ],
        ], $code);
    }
}
