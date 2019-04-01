<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * Class ImageController
 * @package App\Http\Controllers\Admin
 */
class ImageController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|file',
        ]);

        $image = $request->image;

        $folder = base_path('public/images');
        $filename = date('Y_m_d', time()) . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();

        $image->move($folder, $filename);

        return response()->json([
            'data' => [
                'name' => $filename,
                'url'  => (new \Laravel\Lumen\Routing\UrlGenerator(app()))->asset('images/' . $filename),
            ],
        ], 200);
    }
}
