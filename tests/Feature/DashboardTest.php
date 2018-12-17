<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class DashboardTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_get_dashboard_data()
    {
        $this->signIn();

        $this->json('GET', '/admin/dashboard')->seeJsonEquals([
            'data' => [
                'article_count' => 0,
                'cache_rate'    => 0,
                'comment_count' => 0,
                'author_count'  => 1,
            ]
        ]);
    }
}
