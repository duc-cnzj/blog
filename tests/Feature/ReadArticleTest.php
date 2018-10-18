<?php

use App\Article;
use App\Trending;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ReadArticleTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $trending = new Trending();
        $trending->reset();
    }

    /** @test */
    public function guest_can_get_articles()
    {
        create(Article::class, ['desc' => 'this is articles test 1']);
        create(Article::class, ['desc' => 'this is articles test 2']);
        create(Article::class, [], 20);

        DB::enableQueryLog();
        $r = $this->json('GET', '/articles');
        DB::disableQueryLog();
        $this->assertEquals(3, count(DB::getQueryLog()));
        $r->assertResponseStatus(200);
        $r->seeJson(['desc' => 'this is articles test 1'])
            ->seeJson(['desc' => 'this is articles test 2'])
            ->dontSeeJson(['desc' => 'this bla is articles test']);
    }

    /** @test */
    public function guest_can_see_single_article()
    {
        $article = create(Article::class, ['title' => 'article 1']);
        create(Article::class, ['title' => 'article 2']);

        DB::enableQueryLog();
        $r = $this->get('/articles/1');
        $r->seeJson(['title' => 'article 1'])
            ->dontSeeJson(['title' => 'article 2']);
        DB::disableQueryLog();
        $this->assertEquals(4, count(DB::getQueryLog()));

        DB::flushQueryLog();

        DB::enableQueryLog();
        $r = $this->json('GET', '/articles/1');
        DB::disableQueryLog();
        $this->assertEquals(0, count(DB::getQueryLog()));
    }
}
