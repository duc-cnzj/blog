<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseMigrations;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function login_test()
    {
        create(\App\User::class, [
            'name'     => 'duc',
            'password' => '111222',
            'mobile'   => '123456',
        ]);

        dump($this->post('/auth/login', [
            'mobile'   => '123456',
            'password' => '111222',
        ])->response->content());
        $this->post('/auth/login', [
            'mobile'   => '123456',
            'password' => '111222',
        ])->seeStatusCode(200);
    }

    /** @test */
    public function user_can_get_info()
    {
        $this->signIn(['name'=>'duc', 'email' => '1025434128@qq.com']);

        $this->post('/auth/me')->seeJson(['name' => 'duc', 'email' => '1025434128@qq.com']);
    }

    /** @test */
    public function user_can_refresh_token()
    {
        $user = create(\App\User::class);

        $token = Auth::fromUser($user);
        Auth::setToken($token)->setUser($user);

        $this->post('/auth/refresh', [], ['Authorization' => 'bearer' . $token])
            ->seeStatusCode(200);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = create(\App\User::class);

        $token = Auth::fromUser($user);
        Auth::setToken($token)->setUser($user);

        $this->post('/auth/logout', [], ['Authorization' => 'Bearer ' . $token])
            ->seeStatusCode(200);
    }
}
