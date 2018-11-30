<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validate($request, [
            'mobile'   => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only(['mobile', 'password']);

        if (! $token = \Auth::attempt($credentials)) {
            return response()->json([
                'error' => [
                    'code'    => 401,
                    'message' => 'Unauthorized.',
                ],
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return UserResource
     */
    public function me()
    {
        return new UserResource(\Auth::user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        \Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => \Auth::factory()->getTTL() * 60,
        ]);
    }

    public function updateInfo(Request $request)
    {
        info('更新用户信息，user_id：' . \Auth::id());
        if ($request->has('avatar')) {
            $image = $request->file('avatar');
            $folder = base_path('public/images');
            $filename = date('Y_m_d', time()) . '_' . str_random(10) . '.' . $image->getClientOriginalExtension();

            $image->move($folder, $filename);
            $attributes = ['avatar' => (new \Laravel\Lumen\Routing\UrlGenerator(app()))->asset('images/' . $filename)];
        } else {
            $attributes = $request->only('bio', 'email');
        }

        \Auth::user()->update($attributes);

        return response()->json([
            'data' => new UserResource(\Auth::user()),
        ], 201);
    }
}
