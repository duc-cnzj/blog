<?php

use App\Article;
use App\Trending;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ArticleWithoutCacheTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Trending
     */
    protected $trending;

    public function setUp()
    {
        parent::setUp();

        $this->trending = new Trending();

        config(['duc.article_use_cache' => false]);
    }

    /** @test */
    public function guest_can_see_article_from_db()
    {
        create(Article::class, ['title' => 'article 1']);

        DB::enableQueryLog();
        $this->get('/articles/1');
        DB::disableQueryLog();
        $this->assertEquals(4, count(DB::getQueryLog()));

        DB::flushQueryLog();
        $this->assertEquals(0, count(DB::getQueryLog()));

        DB::enableQueryLog();
        $this->json('GET', '/articles/1')->seeStatusCode(200);
        DB::disableQueryLog();
        $this->assertEquals(4, count(DB::getQueryLog()));
    }

    /** @test */
    public function user_can_get_trending_articles_from_db()
    {
        create(\App\Article::class, [], 10);

        $i = 10;
        while ($i > 0) {
            $this->json('GET', "/articles/{$i}");
            $i--;
        }

        $this->assertEquals(10, count($this->trending->get()));

        DB::enableQueryLog();
        $res = $this->json('GET', '/trending_articles');
        DB::disableQueryLog();
        $res->seeStatusCode(200);
        $data = json_decode($res->response->content());
        $this->assertEquals(10, count(data_get($data, 'data')));
        $this->assertEquals(4, count(DB::getQueryLog()));
    }

    /** @test */
    public function user_can_delete_article()
    {
        $user = $this->newTestUser();
        $this->signIn([], $user);
        $article = create(Article::class, ['author_id' => $user->id]);
        $this->json('delete', '/admin/articles/' . $article->id)
            ->seeStatusCode(204);
    }

    /** @test */
    public function article_doesnt_has_cache()
    {
        $user = $this->newTestUser();
        $this->signIn([], $user);
        $article = create(Article::class, ['author_id' => $user->id]);
        $this->json('GET', '/articles/' . $article->id)
            ->seeStatusCode(200);
        $this->assertFalse(app(\App\Contracts\ArticleRepoImp::class)->hasArticleCacheById($article->id));
    }
}
