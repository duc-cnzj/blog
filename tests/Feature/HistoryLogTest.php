<?php

use App\Contracts\WhiteListIpImp;
use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Contracts\Auth\Authenticatable;

class HistoryLogTest extends TestCase
{
    use DatabaseMigrations;

    protected $prefix = 'history';

    protected $filters = [
        'ip',
        'method',
        'status_code',
        'address',
//        'content',
        'response',
        'visit_time_after',
        'visit_time_before',
        'user_id',
        'user_type',
    ];

    /** @test */
    public function each_request_which_not_in_white_lists_will_logged()
    {
        $mock = Mockery::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);

        $mock->shouldReceive('dispatch')->times(3)
            ->with(Mockery::type(\App\Jobs\RecordUser::class));

        $this->app->instance(
            'Illuminate\Contracts\Bus\Dispatcher',
            $mock
        );

        $this->get('/');
        $this->get('/abc');
        $this->get('/test');
        $this->get('/admin/histories');
    }

    /** @test */
    public function unauthenticated_user_can_not_visit_histories()
    {
        $this->get('/admin/histories')->assertResponseStatus(401);
    }

    /** @test */
    public function request_in_white_lists_will_not_logged()
    {
        $mock = Mockery::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);

        $mock->shouldReceive('dispatch')->never()
            ->with(Mockery::type(\App\Jobs\RecordUser::class));

        $this->app->instance(
            'Illuminate\Contracts\Bus\Dispatcher',
            $mock
        );

        $this->signIn();
        $this->assertInstanceOf(Authenticatable::class, Auth::user());
        $this->get('/admin/histories');
        $this->get('/admin/histories/1');
        $this->get('/admin/histories/123');
    }

    /** @test */
    public function admin_can_see_histories()
    {
        $socialiteUser = create(\App\SocialiteUser::class, ['name' => 'SocialiteUser']);
        $adminUser = create(\App\User::class, ['name' => 'adminUser']);

        create(\App\History::class, ['response' => 'response']);
        create(\App\History::class, ['userable_id' => $socialiteUser->id, 'userable_type' => get_class($socialiteUser)]);
        create(\App\History::class, ['userable_id' => $adminUser->id, 'userable_type' => get_class($adminUser)]);

        $this->signIn();
        $res = $this->get('/admin/histories');
        $this->seeJsonStructure(
            [
                'data' => [
                    '*' => [
                        'id',
                        'ip',
                        'url',
                        'content',
                        'method',
                        'address',
                        'response',
                        'user_agent',
                        'visited_at',
                        'status_code',
                        'user' => ['name'],
                    ],
                ],
            ],
            $res->response->content()
        );

        $res->seeJson(['name' => ''])
            ->seeJson(['name' => 'SocialiteUser'])
            ->seeJson(['name' => 'adminUser'])
            ->dontSeeJson(['response' => 'response']);
    }

    /** @test */
    public function admin_can_see_history_detail()
    {
        $socialiteUser = create(\App\SocialiteUser::class, ['name' => 'SocialiteUser']);
        $history = create(\App\History::class, ['userable_id' => $socialiteUser->id, 'userable_type' => get_class($socialiteUser), 'response' => 'response']);

        $this->signIn();
        $res = $this->get('/admin/histories/' . $history->id);
        $this->seeJsonStructure(
            [
                'data' => [
                    'id',
                    'ip',
                    'url',
                    'content',
                    'method',
                    'address',
                    'response',
                    'user_agent',
                    'visited_at',
                    'status_code',
                    'user' => ['name'],
                ],
            ],
            $res->response->content()
        );

        $res->seeJson(['response' => 'response'])
            ->seeJson(['name' => 'SocialiteUser']);
    }

    /** @test */
    public function admin_can_filter_result_by_ip()
    {
        $cond1 = ['ip' => '127.0.0.1'];
        $cond2 = ['ip' => '192.168.127.1'];
        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        $params1 = "?{$this->prefix}_ip=127.0";
        $params2 = "?{$this->prefix}_ip=127";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson($cond1)
            ->dontSeeJson($cond2);
        $this->get('/admin/histories' . $params2)
            ->seeJson($cond1)
            ->SeeJson($cond2);
    }

    /** @test */
    public function admin_can_filter_result_by_method()
    {
        $cond1 = ['method' => 'GET'];
        $cond2 = ['method' => 'POST'];
        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        $params1 = "?{$this->prefix}_method=GET";
        $params2 = "?{$this->prefix}_method=POST";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson($cond1)
            ->dontSeeJson($cond2);
        $this->get('/admin/histories' . $params2)
            ->dontSeeJson($cond1)
            ->SeeJson($cond2);
    }

    /** @test */
    public function admin_can_filter_result_by_url()
    {
        $cond1 = ['url' => '/auth/user'];
        $cond2 = ['url' => '/name/duc'];
        $cond3 = ['url' => '/name/abc'];

        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        create(\App\History::class, $cond3);

        $params1 = "?{$this->prefix}_url=name";
        $params2 = "?{$this->prefix}_url=auth";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson($cond2)
            ->seeJson($cond3)
            ->dontSeeJson($cond1);
        $this->get('/admin/histories' . $params2)
            ->seeJson($cond1)
            ->dontSeeJson($cond2)
            ->dontSeeJson($cond3);
    }

    /** @test */
    public function admin_can_filter_result_by_status_code()
    {
        $cond1 = ['status_code' => 200];
        $cond2 = ['status_code' => 400];
        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        $params1 = "?{$this->prefix}_status_code=200";
        $params2 = "?{$this->prefix}_status_code=400";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson($cond1)
            ->dontSeeJson($cond2);
        $this->get('/admin/histories' . $params2)
            ->dontSeeJson($cond1)
            ->SeeJson($cond2);
    }

    /** @test */
    public function admin_can_filter_result_by_address()
    {
        $cond1 = ['address' => '绍兴qwerty'];
        $cond2 = ['address' => '杭州1234567'];

        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        $params1 = "?{$this->prefix}_address=rty";
        $params2 = "?{$this->prefix}_address=567";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson($cond1)
            ->dontSeeJson($cond2);
        $this->get('/admin/histories' . $params2)
            ->dontSeeJson($cond1)
            ->SeeJson($cond2);
    }

    /** @test */
    public function admin_can_filter_result_by_response()
    {
        $cond1 = ['response' => '绍兴12345'];
        $cond2 = ['response' => '杭州321456'];

        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        $params1 = "?{$this->prefix}_response=321";
        $params2 = "?{$this->prefix}_response=123";

        $this->signIn();
        $res1 = $this->get('/admin/histories' . $params1)->response->content();
        $res2 = $this->get('/admin/histories' . $params2)->response->content();
        $res3 = $this->get('/admin/histories')->response->content();

        $this->assertEquals(1, count(json_decode($res1, JSON_OBJECT_AS_ARRAY)['data']));
        $this->assertEquals(1, count(json_decode($res2, JSON_OBJECT_AS_ARRAY)['data']));
        $this->assertEquals(2, count(json_decode($res3, JSON_OBJECT_AS_ARRAY)['data']));
    }

    /** @test */
    public function admin_can_filter_result_by_visit_time_after_and_visit_time_before()
    {
        $cond1 = ['visited_at' => \Carbon\Carbon::parse('2015-2-1 12:00')];
        $cond2 = ['visited_at' => \Carbon\Carbon::parse('2017-2-1 12:00')];

        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);

        $this->signIn();

        $asserts = [
            "?{$this->prefix}_visit_time_after=2018-1-1"                                            => 0,
            "?{$this->prefix}_visit_time_after=123123qwe4"                                          => 2,
            "?{$this->prefix}_visit_time_after=2015-2-1 12:00"                                      => 2,
            "?{$this->prefix}_visit_time_after=2015-2-1 12:01"                                      => 1,
            "?{$this->prefix}_visit_time_before=2017-2-1 12:00"                                     => 2,
            "?{$this->prefix}_visit_time_before=2017-2-1 11:59"                                     => 1,
            "?{$this->prefix}_visit_time_after=2015-1-1&{$this->prefix}_visit_time_before=2016-1-1" => 1,
            "?{$this->prefix}_visit_time_after=2016-1-1&{$this->prefix}_visit_time_before=2016-3-1" => 0,
            "?{$this->prefix}_visit_time_after=2017-1-1&{$this->prefix}_visit_time_before=2019-3-1" => 1,
        ];

        foreach ($asserts as $query => $count) {
            $res = $this->get('/admin/histories' . $query)->response->content();
            $this->assertEquals($count, count(json_decode($res, JSON_OBJECT_AS_ARRAY)['data']));
        }
    }

    /** @test */
    public function admin_can_filter_result_by_user_id()
    {
        $user1 = create(\App\User::class, ['name' => 'admin']);
        $user2 = create(\App\SocialiteUser::class, ['name' => 'abc']);
        $cond1 = ['userable_id' => $user1->id, 'userable_type' => 'App\User'];
        $cond2 = ['userable_id' => $user2->id, 'userable_type' => 'App\SocialiteUser'];
        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);

        $this->assertEquals($user1->id, $user2->id);

        $params1 = "?{$this->prefix}_user_id={$user1->id}";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson(['name' => 'admin'])
            ->seeJson(['name' => 'abc']);

        $params2 = "?{$this->prefix}_user_type=admin";
        $this->get('/admin/histories' . $params2)
            ->seeJson(['name' => 'admin'])
            ->dontSeeJson(['name' => 'abc']);

        $params3 = "?{$this->prefix}_user_type=frontend";
        $this->get('/admin/histories' . $params3)
            ->dontSeeJson(['name' => 'admin'])
            ->seeJson(['name' => 'abc']);

        $params3 = "?{$this->prefix}_user_type=qwer";
        $this->get('/admin/histories' . $params3)
            ->seeJson(['name' => 'admin'])
            ->seeJson(['name' => 'abc']);
    }

    /** @test */
    public function admin_can_filter_result_by_only_see()
    {
        $cond1 = ['url' => '/'];
        $cond2 = ['url' => '/home_articles'];
        $cond3 = ['url' => '/auth/login'];
        $cond4 = ['url' => '/admin/article_regulars'];

        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        create(\App\History::class, $cond3);
        create(\App\History::class, $cond4);

        $params1 = "?{$this->prefix}_only_see=admin";
        $params2 = "?{$this->prefix}_only_see=frontend";
        $params3 = "?{$this->prefix}_only_see=qwert";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson($cond3)
            ->seeJson($cond4)
            ->dontSeeJson($cond1)
            ->dontSeeJson($cond2);
        $this->get('/admin/histories' . $params2)
            ->seeJson($cond1)
            ->seeJson($cond2)
            ->dontSeeJson($cond3)
            ->dontSeeJson($cond4);
        $this->get('/admin/histories' . $params3)
            ->seeJson($cond1)
            ->seeJson($cond2)
            ->seeJson($cond3)
            ->seeJson($cond4);
    }

    /** @test */
    public function admin_can_filter_result_by_only_see_and_other_filter()
    {
        $cond1 = ['method' => 'GET', 'url' => '/'];
        $cond2 = ['method' => 'POST', 'url' => '/home_articles'];
        $cond3 = ['method' => 'PUT', 'url' => '/auth/login'];
        $cond4 = ['method' => 'DELETE', 'url' => '/admin/article_regulars'];

        create(\App\History::class, $cond1);
        create(\App\History::class, $cond2);
        create(\App\History::class, $cond3);
        create(\App\History::class, $cond4);

        $params1 = "?{$this->prefix}_only_see=admin";
        $params2 = "?{$this->prefix}_method=GET";
        $params3 = "?{$this->prefix}_only_see=admin&{$this->prefix}_method=GET";
        $params4 = "?{$this->prefix}_only_see=frontend&{$this->prefix}_method=GET";

        $this->signIn();
        $this->get('/admin/histories' . $params1)
            ->seeJson($cond3)
            ->seeJson($cond4)
            ->dontSeeJson($cond1)
            ->dontSeeJson($cond2);
        $this->get('/admin/histories' . $params2)
            ->seeJson($cond1)
            ->dontSeeJson($cond2)
            ->dontSeeJson($cond3)
            ->dontSeeJson($cond4);
        $this->get('/admin/histories' . $params3)
            ->dontSeeJson($cond1)
            ->dontSeeJson($cond2)
            ->dontSeeJson($cond3)
            ->dontSeeJson($cond4);
        $this->get('/admin/histories' . $params4)
            ->seeJson($cond1)
            ->dontSeeJson($cond2)
            ->dontSeeJson($cond3)
            ->dontSeeJson($cond4);
    }

    /** @test */
    public function if_some_ip_add_to_white_list_it_will_disappeared()
    {
        $this->signIn();
        $ip = '192.168.99.100';
        create(\App\History::class, ['ip' => $ip], 3);
        $res = $this->get('/admin/histories')->response->content();
        $this->assertEquals(3, count(json_decode($res, JSON_OBJECT_AS_ARRAY)['data']));

        /** @var WhiteListIpImp $whiteList */
        $whiteList = app(WhiteListIpImp::class);
        $whiteList->addItemToList($ip);
        $res1 = $this->get('/admin/histories')->response->content();
        $this->assertEquals(0, count(json_decode($res1, JSON_OBJECT_AS_ARRAY)['data']));
    }
}
