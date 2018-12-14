<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(Request $request)
    {
        $users = User::latest()->paginate($request->input('page_size') ?? 10);

        return UserResource::collection($users);
    }

    /**
     * @param Request $request
     *
     * @return UserResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'     => 'required',
            'email'    => ['required', Rule::unique('users')],
            'mobile'   => ['required', Rule::unique('users')],
            'password' => 'required',
        ]);

        info('req', $request->all());
        $attributes = $request->only('name', 'email', 'mobile', 'bio', 'password');

        if ($request->has('avatar')) {
            $image = $request->avatar;
            $folder = base_path('public/images');
            $filename = date('Y_m_d', time()) . '_' . str_random(10) . '.' . $image->getClientOriginalExtension();

            if (! app()->environment('testing')) {
                $image->move($folder, $filename);
            }

            $attributes['avatar'] = (new \Laravel\Lumen\Routing\UrlGenerator(app()))->asset('images/' . $filename);
        }

        $user = User::create($attributes);

        return new UserResource($user);
    }

    /**
     * @param int $id
     *
     * @return UserResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function show(int $id)
    {
        return new UserResource(User::findOrFail($id, ['id', 'name', 'email', 'avatar', 'bio']));
    }

    /**
     * @param int     $id
     * @param Request $request
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @author duc <1025434218@qq.com>
     */
    public function update(int $id, Request $request)
    {
        $this->validate($request, [
            'name'     => 'string',
            'email'    => ['email', Rule::unique('users')->ignore($id)],
            'bio'      => 'string',
        ]);

        if (! \Auth::user()->isAdmin() && $id !== \Auth::id()) {
            abort(403, '你没有权限修改其他用户资料！');
        }

        User::findOrFail($id)->update($request->only('name', 'email', 'bio'));

        return response([], 204);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(int $id)
    {
        if ($id === 1) {
            return $this->fail('超级管理员不能删除', 403);
        }

        if (\Auth::user()->isAdmin()) {
            User::findOrFail($id)->delete();

            return response([], 204);
        } else {
            return $this->fail('你没有删除用户的权限', 403);
        }
    }
}
