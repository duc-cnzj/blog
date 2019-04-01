<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\SocialiteUser;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\RequestException;
use App\Http\Resources\SocialiteUserResource;

/**
 * Class AuthController
 * @package App\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct()
    {
        \Auth::shouldUse('socialite');

        $this->middleware('auth:socialite', ['only' => ['me']]);
    }

    /**
     * 重定向用户信息到 GitHub 认证页面。
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider()
    {
        return Socialite::driver('github')->stateless()->redirect();
    }

    /**
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @author duc <1025434218@qq.com>
     */
    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('github')->stateless()->user();
        } catch (\Exception $e) {
            if ($e instanceof RequestException && $e->getResponse()->getStatusCode() === 401) {
                return response('请不要重复刷新，每个 token 只能用一次', 401);
            }

            Log::error('github 登陆出现异常', [
                'exception' => $e->getMessage(),
            ]);

            return abort(500, 'github 登陆异常。');
        }

        $socialiteUser = SocialiteUser::firstOrNew(
            ['identifier' => $user->id],
            ['identity_type' => 'github']
        );

        if ($socialiteUser->exists) {
            $socialiteUser->last_login_at = Carbon::now();
            $socialiteUser->save();
        } else {
            $socialiteUser->fill([
                'identifier'    => $user->id,
                'avatar'        => $user->avatar,
                'url'           => $user->user['url'],
                'name'          => $user->nickname,
                'last_login_at' => Carbon::now(),
            ])->save();
        }

        $token = \Auth::fromSubject($socialiteUser);

        return view('home', ['token' => $token, 'domain' => env('FRONTEND_DOMAIN')]);
    }

    /**
     * @return SocialiteUserResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function me()
    {
        return new SocialiteUserResource(\Auth::user());
    }
}
