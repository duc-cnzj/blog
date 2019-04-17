<?php

use App\Contracts\WhiteListUrlImp;
use Laravel\Lumen\Testing\DatabaseMigrations;

class WhiteListUrlTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var WhiteListUrlImp
     */
    protected $handler;

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->handler = app(WhiteListUrlImp::class);
    }

    /** @test */
    public function guest_can_not_see_white_list_items()
    {
        $this->get('/admin/url_white_lists')->seeStatusCode(401);
    }

    /** @test */
    public function admin_can_see_white_list_items()
    {
        $this->signIn();
        $this->get('/admin/url_white_lists')->seeJson(['data' => []]);
    }

    /** @test */
    public function admin_can_add_white_list_items()
    {
        $this->signIn();
        $this->post('/admin/url_white_lists', [
            'items' => ['/user', 'admin'],
        ]);

        $this->assertEquals(['/user', 'admin'], $this->handler->getItemLists());

        $this->post('/admin/url_white_lists', [
            'items' => 'admin',
        ])->seeStatusCode(422);
    }

    /** @test */
    public function can_not_delete_empty_string()
    {
        $this->signIn();
        $this->delete('/admin/url_white_lists', [
            'item' => '',
        ])->seeStatusCode(422);
    }

    /** @test */
    public function can_not_add_empty_string()
    {
        $this->signIn();
        $this->post('/admin/url_white_lists', [
            'items' => [],
        ])->seeStatusCode(422);
    }

    /** @test */
    public function admin_can_delete_white_list_items()
    {
        $this->signIn();
        $this->post('/admin/url_white_lists', [
            'items' => ['/user', 'admin'],
        ])->seeStatusCode(201);

        $this->assertEquals(['/user', 'admin'], $this->handler->getItemLists());
        $this->delete('/admin/url_white_lists', [
            'item' => 'admin',
        ])->seeStatusCode(204);

        $this->assertEquals(['/user'], $this->handler->getItemLists());

        $this->delete('/admin/url_white_lists', [
            'item' => ['admin'],
        ])->assertResponseStatus(422);
    }

    /** @test */
    public function request_those_which_not_in_white_lists_will_logged()
    {
        $mock = Mockery::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);
        $mock->shouldReceive('dispatch')->never()
            ->with(Mockery::type(\App\Jobs\RecordUser::class));

        $this->app->instance(
            'Illuminate\Contracts\Bus\Dispatcher',
            $mock
        );

        $this->handler->addItemToList('user', 'admin');

        $this->assertEquals(['user', 'admin'], $this->handler->getItemLists());

        $this->get('/user');
        $this->get('/admin');
    }
}
