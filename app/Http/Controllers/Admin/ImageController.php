<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Laravel\Lumen\Routing\UrlGenerator;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required'
        ]);

        $image = $request->image;

        $folder = base_path('public/images');
        $filename = date('Y_m_d',time()) . '_'. str_random(10) . '.' . $image->getClientOriginalExtension();

        $path = $image->move($folder, $filename);

        return [
            'data' => [
                'name' => $filename,
                'url' => (new \Laravel\Lumen\Routing\UrlGenerator(app()))->asset('images/' . $filename)
            ]
        ];
    }
}
