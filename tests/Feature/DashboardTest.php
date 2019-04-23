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
            ],
        ]);
    }

    /** @test */
    public function user_get_dashboard_data()
    {
        $user = $this->signIn();

        $c = create(\App\Category::class, ['user_id' => $user->id]);
        $article = create(\App\Article::class, ['title' => 'aaa', 'author_id' => $user->id, 'category_id' => $c->id]);

        $this->seeInDatabase('articles', ['title' => 'aaa']);
        $this->json('GET', '/admin/dashboard')->seeJsonEquals([
            'data' => [
                'article_count' => 1,
                'cache_rate'    => 0,
                'comment_count' => 0,
                'author_count'  => 1,
            ],
        ]);

        $this->get('/articles/' . $article->id)->seeStatusCode(200);

        $this->assertTrue(app(\App\Contracts\ArticleRepoImp::class)
            ->hasArticleCacheById($article->id));

        $this->json('GET', '/admin/dashboard')->seeJsonEquals([
            'data' => [
                'article_count' => 1,
                'cache_rate'    => 100,
                'comment_count' => 0,
                'author_count'  => 1,
            ],
        ]);
    }

    /** @test */
    public function user_can_get_history_data()
    {
        $this->signIn();

        $this->json('GET', '/admin/history_data')->assertResponseOk();
    }

    /** @test */
    public function user_can_pass_params()
    {
        $this->signIn();

        $this->json('GET', '/admin/history_data', [
            'section' => 'qwe',
        ])->seeStatusCode(422);
        $this->json('GET', '/admin/history_data', [
            'unit' => 123,
        ])->seeStatusCode(422);
        $this->json('GET', '/admin/history_data', [
            'sub_week' => 'qw',
        ])->seeStatusCode(422);
        $this->json('GET', '/admin/history_data', [
            'from' => '1ed',
        ])->seeStatusCode(422);
    }

    /** @test */
    public function user_can_see_data_structure()
    {
        $this->signIn();

        create(\App\History::class, ['visited_at' => \Carbon\Carbon::now()], 3);
        $res = $this->json('GET', '/admin/history_data');

        $res->seeJsonStructure([
            'data' => [
                'times',
                'total',
                'detail' => [
                    '*' => [
                        'count',
                        'time',
                        'data',
                    ],
                ],
                'total_visits',
            ],
        ])->seeJsonContains(['total' => 3]);
    }

    /** @test */
    public function user_can_see_use_section()
    {
        $this->signIn();

        $res1 = $this->json('GET', '/admin/history_data', ['section' => 3]);

        $this->assertEquals(3, count(json_decode($res1->response->content(), true)['data']['times']));

        $res2 = $this->json('GET', '/admin/history_data', ['section' => 12]);

        $this->assertEquals(12, count(json_decode($res2->response->content(), true)['data']['times']));
    }

    /** @test */
    public function user_can_see_use_unit_and_sub_week()
    {
        $this->signIn();

        $res1 = $this->json('GET', '/admin/history_data', ['unit' => 'week']);

        $this->assertEquals(7, count(json_decode($res1->response->content(), true)['data']['times']));

        $res2 = $this->json('GET', '/admin/history_data', ['unit' => 'week', 'sub_week' => 2]);

        $time = \Carbon\Carbon::now()->subWeeks(2)->startOfWeek();

        $res2->seeJsonContains(['time' => "{$time->format('Y-m-d')}"]);
    }

    /** @test */
    public function user_can_see_use_from()
    {
        $this->signIn();

        create(\App\History::class, ['url' => '/admin', 'visited_at' => \Carbon\Carbon::now()]);
        create(\App\History::class, ['url' => '/auth', 'visited_at' => \Carbon\Carbon::now()]);
        create(\App\History::class, ['url' => '/articles/1', 'visited_at' => \Carbon\Carbon::now()]);
        $res1 = $this->json('GET', '/admin/history_data', ['from' => 'admin']);

        $res1->dontSeeJson(['url' => ['/articles/1' => 1]])
            ->seeJson(['url' => ['/admin' => 1]])
            ->seeJson(['url' => ['/auth' => 1]]);
    }
}
