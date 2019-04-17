<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class WhiteListMethodTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function get_not_logged()
    {
        $mock = Mockery::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);
        $mock->shouldReceive('dispatch')->never()
            ->with(Mockery::type(\App\Jobs\RecordUser::class));

        $this->app->instance(
            'Illuminate\Contracts\Bus\Dispatcher',
            $mock
        );

        $this->call('HEAD', '/');
    }

    /** @test */
    public function options_not_logged()
    {
        $mock = Mockery::mock('Illuminate\Bus\Dispatcher[dispatch]', [$this->app]);
        $mock->shouldReceive('dispatch')->never()
            ->with(Mockery::type(\App\Jobs\RecordUser::class));

        $this->app->instance(
            'Illuminate\Contracts\Bus\Dispatcher',
            $mock
        );

        $this->call('OPTIONS', '/');
    }
}
