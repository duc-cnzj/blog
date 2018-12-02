<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function store(Request $request)
    {
        info('req', $request->all());
        $attributes = $request->only('name', 'email', 'mobile', 'bio', 'password');

        if ($request->has('avatar')) {
            $image = $request->avatar;
            $folder = base_path('public/images');
            $filename = date('Y_m_d', time()) . '_' . str_random(10) . '.' . $image->getClientOriginalExtension();

            if (app()->environment() !== 'testing') {
                $image->move($folder, $filename);
            }

            $attributes['avatar'] = (new \Laravel\Lumen\Routing\UrlGenerator(app()))->asset('images/' . $filename);
        }

        $user = User::create($attributes);

        return new UserResource($user);
    }
}
