<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class LoginTest extends TestCase
{
    use DatabaseMigrations;

//    /** @test */
//    public function login_test()
//    {
//        $this->newTestUser([
//            'name'     => 'duc',
//            'password' => '111222',
//            'mobile'   => '123456',
//        ]);
//
//        $this->post('/auth/login', [
//            'mobile'   => '123456',
//            'password' => '111222',
//        ])->seeStatusCode(200);
//    }

    /** @test */
    public function user_can_get_info()
    {
        $this->signIn(['name'=>'duc', 'email' => '1025434128@qq.com']);

        $this->post('/auth/me')->seeJson(['name' => 'duc', 'email' => '1025434128@qq.com']);
    }
//
//    /** @test */
//    public function user_can_refresh_token()
//    {
//        $this->newTestUser([
//            'name'     => 'duc',
//            'password' => '111222',
//            'mobile'   => '123456',
//        ]);
//
//        $data = $this->post('/auth/login', [
//            'mobile'   => '123456',
//            'password' => '111222',
//        ])->response->content();
//
//        $this->post('/auth/refresh', [], ['Authorization' => 'bearer' . data_get($data, 'data.access_token')])
//            ->seeStatusCode(200);
//    }
//
//    /** @test */
//    public function user_can_logout()
//    {
//        $this->newTestUser([
//            'name'     => 'duc',
//            'password' => '111222',
//            'mobile'   => '123456',
//        ]);
//
//        $data = $this->post('/auth/login', [
//            'mobile'   => '123456',
//            'password' => '111222',
//        ])->response->content();
//
//        $this->post('/auth/logout', [], ['Authorization' => 'bearer' . data_get($data, 'data.access_token')])
//            ->seeStatusCode(200);
//    }
}
