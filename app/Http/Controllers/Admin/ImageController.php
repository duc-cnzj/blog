<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ImageController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required',
        ]);

        $image = $request->image;

        $folder = base_path('public/images');
        $filename = date('Y_m_d', time()) . '_' . str_random(10) . '.' . $image->getClientOriginalExtension();

        $image->move($folder, $filename);

        return [
            'data' => [
                'name' => $filename,
                'url'  => (new \Laravel\Lumen\Routing\UrlGenerator(app()))->asset('images/' . $filename),
            ],
        ];
    }
}
