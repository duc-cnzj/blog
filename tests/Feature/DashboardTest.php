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
}
