<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class SocialiteUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_can_not_see_socialite_user_lists()
    {
        create(\App\SocialiteUser::class, [], 3);
        $this->get('/admin/socialite_users')->seeStatusCode(401);
    }

    /** @test */
    public function admin_can_see_socialite_user_lists()
    {
        $this->signIn();
        create(\App\SocialiteUser::class, [], 3);
        $res = $this->get('/admin/socialite_users');

        $res->seeJsonStructure([
            'data' => [
                '*' => [
                    'name', 'id', 'avatar', 'last_login_at', 'created_at', 'identity_type',
                ],
            ],
            'meta',
            'links',
        ]);
    }

    /** @test */
    public function admin_can_filter_socialite_user_lists_by_id()
    {
        $this->signIn();
        $users = create(\App\SocialiteUser::class, [], 3);
        $res = $this->get('/admin/socialite_users?socialite_user_id=' . $users->first()->id);

        $this->assertEquals(1, count(json_decode($res->response->content(), true)['data']));

        $res = $this->get('/admin/socialite_users?socialite_user_id=');

        $this->assertEquals(3, count(json_decode($res->response->content(), true)['data']));
    }

    /** @test */
    public function admin_can_filter_socialite_user_lists_by_name()
    {
        $this->signIn();
        create(\App\SocialiteUser::class, ['name' => 'duc']);
        create(\App\SocialiteUser::class, [], 3);

        $res = $this->get('/admin/socialite_users?socialite_user_name=duc');
        $this->assertEquals(1, count(json_decode($res->response->content(), true)['data']));

        $res = $this->get('/admin/socialite_users?socialite_user_name=not_exists');
        $this->assertEquals(0, count(json_decode($res->response->content(), true)['data']));

        $res = $this->get('/admin/socialite_users?socialite_user_name=');
        $this->assertEquals(4, count(json_decode($res->response->content(), true)['data']));
    }

    /** @test */
    public function admin_can_filter_socialite_user_lists_by_identity_type()
    {
        $this->signIn();
        create(\App\SocialiteUser::class, ['identity_type' => 'github']);
        create(\App\SocialiteUser::class, ['identity_type' => 'wechat'], 3);

        $res = $this->get('/admin/socialite_users?socialite_user_identity_type=github');
        $this->assertEquals(1, count(json_decode($res->response->content(), true)['data']));

        $res = $this->get('/admin/socialite_users?socialite_user_identity_type=wechat');
        $this->assertEquals(3, count(json_decode($res->response->content(), true)['data']));
    }
}
