<?php

use App\Contracts\WhiteListIpImp;
use Illuminate\Support\Facades\Request;
use Laravel\Lumen\Testing\DatabaseMigrations;

class WhiteListIpTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var WhiteListIpImp
     */
    protected $handler;

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->handler = app(WhiteListIpImp::class);
    }

    /** @test */
    public function guest_can_not_see_ip_white_list_items()
    {
        $this->get('/admin/ip_white_lists')->seeStatusCode(401);
    }

    /** @test */
    public function admin_can_see_ip_white_list_items()
    {
        $this->signIn();
        $this->get('/admin/ip_white_lists')->seeJson(['data' => []]);
    }

    /** @test */
    public function admin_can_add_white_list_items()
    {
        $ip = '192.168.11.1';
        $this->signIn();
        $this->post('/admin/ip_white_lists', [
            'item' => ['/user', 'admin'],
        ])->seeStatusCode(422);

        $this->post('/admin/ip_white_lists', [
            'item' => '1234567.567.678.1',
        ])->seeStatusCode(422);

        $this->post('/admin/ip_white_lists', [
            'item' => $ip,
        ])->seeStatusCode(201);

        $this->assertEquals([$ip], $this->handler->getItemLists());
    }

    /** @test */
    public function can_not_delete_empty_string()
    {
        $this->signIn();
        $this->delete('/admin/ip_white_lists', [
            'item' => '',
        ])->seeStatusCode(422);
    }

    /** @test */
    public function can_not_add_empty_string()
    {
        $this->signIn();
        $this->post('/admin/ip_white_lists', [
            'items' => [],
        ])->seeStatusCode(422);
    }

    /** @test */
    public function admin_can_delete_white_list_items()
    {
        $this->signIn();
        $ip = '192.168.1.1';
        $this->post('/admin/ip_white_lists', [
            'item' => $ip,
        ])->seeStatusCode(201);

        $this->assertEquals([$ip], $this->handler->getItemLists());
        $this->delete('/admin/ip_white_lists', [
            'item' => $ip,
        ])->seeStatusCode(204);

        $this->assertEquals([], $this->handler->getItemLists());

        $this->delete('/admin/ip_white_lists', [
            'item' => [$ip],
        ])->assertResponseStatus(422);
    }

    /** @test */
    public function request_those_which_not_in_ip_white_lists_will_logged()
    {
        $mock = Mockery::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);
        $mock->shouldReceive('dispatch')->twice()
            ->with(Mockery::type(\App\Jobs\RecordUser::class));

        $this->app->instance(
            'Illuminate\Contracts\Bus\Dispatcher',
            $mock
        );

        $this->handler->addItemToList('192.1.1.1');

        $this->assertEquals('127.0.0.1', Request::ip());
        $this->assertEquals(['192.1.1.1'], $this->handler->getTreatedListItems());

        $this->get('/user');
        $this->get('/admin');

        $this->handler->addItemToList('127.0.0.1');
        $this->assertEquals(['192.1.1.1', '127.0.0.1'], $this->handler->getTreatedListItems());

        $this->get('/user');
        $this->get('/admin');
    }
}
