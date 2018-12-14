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
        $this->trending->reset();

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
}
