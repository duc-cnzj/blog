<?php

use Illuminate\Support\Facades\Auth;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Contracts\Auth\Authenticatable;

class HistoryLogTest extends TestCase
{
    use DatabaseMigrations;

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
                        'ip',
                        'url',
                        'content',
                        'method',
                        'status_code',
                        'response',
                        'user_agent',
                        'visited_at',
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
                    'ip',
                    'url',
                    'content',
                    'method',
                    'status_code',
                    'response',
                    'user_agent',
                    'visited_at',
                    'user' => ['name'],
                ],
            ],
            $res->response->content()
        );

        $res->seeJson(['response' => 'response'])
            ->seeJson(['name' => 'SocialiteUser']);
    }
}
