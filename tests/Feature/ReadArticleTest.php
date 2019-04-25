<?php

use App\Article;
use App\Trending;
use App\Contracts\TopArticleImp;
use App\Contracts\ArticleRepoImp;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ReadArticleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Trending
     */
    protected $trending;

    /**
     * @var TopArticleImp
     */
    protected $topArticle;

    public function setUp(): void
    {
        parent::setUp();

        $this->trending = new Trending();

        $this->topArticle = app(TopArticleImp::class);
    }

    /** @test */
    public function anyone_can_see_home_articles()
    {
        $this->get('/home_articles')->seeStatusCode(200);
    }

    /** @test */
    public function anyone_can_see_newest_articles()
    {
        $this->get('/newest_articles')->seeStatusCode(200);
    }

    /** @test */
    public function anyone_can_see_popular_articles()
    {
        $this->get('/popular_articles')->seeStatusCode(200);
    }

    /** @test */
    public function anyone_can_see_trending_articles()
    {
        $this->get('/trending_articles')->seeStatusCode(200);
    }

    /** @test */
    public function anyone_can_see_top_articles()
    {
        $this->get('/top_articles')->seeStatusCode(200);
    }

    /** @test */
    public function guest_can_get_articles()
    {
        create(Article::class, ['desc' => 'this is articles test 1']);
        create(Article::class, ['desc' => 'this is articles test 2']);

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
        create(Article::class, ['title' => 'article 1']);
        create(Article::class, ['title' => 'article 2']);

        DB::enableQueryLog();
        $r = $this->get('/articles/1');
        $this->assertTrue($this->trending->hasKey(1));
        $r->seeJson(['title' => 'article 1'])
            ->dontSeeJson(['title' => 'article 2']);
        DB::disableQueryLog();
        $this->assertEquals(4, count(DB::getQueryLog()));
    }

    /** @test */
    public function user_can_get_trending_articles_from_cache()
    {
        $articles = create(\App\Article::class, [], 3);

        foreach ($articles as $article) {
            $this->json('GET', "/articles/{$article->id}");
        }

        $this->assertEquals(3, count($this->trending->get()));

        DB::enableQueryLog();
        $res = $this->json('GET', '/trending_articles');
        DB::disableQueryLog();
        $res->seeStatusCode(200);
        $data = json_decode($res->response->content());
        $this->assertEquals(3, count(data_get($data, 'data')));
        $this->assertEquals(0, count(DB::getQueryLog()));
    }

    /** @test */
    public function guest_can_see_article_from_cache()
    {
        $article = create(Article::class, ['title' => 'article 1']);

        DB::enableQueryLog();
        $this->get('/articles/' . $article->id);
        DB::disableQueryLog();
        $this->assertEquals(4, count(DB::getQueryLog()));
        $this->assertTrue($this->trending->hasKey(1));

        DB::flushQueryLog();
        $this->assertEquals(0, count(DB::getQueryLog()));
        $this->assertTrue($this->trending->hasKey(1));

        DB::enableQueryLog();
        $this->json('GET', '/articles/1')->seeStatusCode(200);
        DB::disableQueryLog();
        $this->assertEquals(0, count(DB::getQueryLog()));
    }

    /** @test */
    public function article_content_always_has_md_and_html()
    {
        $content = '# h1';
        $parsedown = new \Parsedown();

        $mdContent = $parsedown->text($content);
        $article = create(Article::class, ['content' => $content]);
        $this->assertEquals(json_encode([
            'html' => $mdContent,
            'md'   => $content,
        ]), $article->content);
    }

    /** @test */
    public function can_not_see_invisible_article()
    {
        $article = create(Article::class, ['display' => false]);
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Article::visible()->findOrFail($article->id);
    }

    /** @test */
    public function can_see_visible_article()
    {
        $article = create(Article::class, ['display' => true]);
        Article::visible()->findOrFail($article->id);
        $this->assertEquals($article->id, Article::query()->findOrFail($article->id)->id);
    }

    /** @test */
    public function user_can_not_see_not_displayed_article()
    {
        $article = create(Article::class, ['display' => false]);

        $r = $this->json('GET', '/articles');
        $this->assertEquals(0, count(data_get(json_decode($r->response->content()), 'data')));
        $r->seeStatusCode(200);

        $article->update(['display' => true]);
        $r = $this->json('GET', '/articles');
        $this->assertEquals(1, count(data_get(json_decode($r->response->content()), 'data')));
        $r->seeStatusCode(200);
    }

    /** @test */
    public function user_can_not_see_not_display_article_in_detail_page()
    {
        $article = create(Article::class);

        $r = $this->json('GET', '/articles/' . $article->id);
        $r->seeStatusCode(200);
        $this->assertTrue(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));

        $article->update(['display' => false]);
        $this->assertFalse(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));
        $r = $this->json('GET', '/articles/' . $article->id);
        $r->seeStatusCode(404);
    }


//    /** @test */
//    public function user_can_search_visible_article()
//    {
//        create(Article::class, ['title' => 'duc']);
//
//        $r = $this->get('/search_articles?q=duc');
//
//        $r->seeJsonContains([
//            'title' => 'duc',
//        ]);
//    }
//
//    /** @test */
//    public function user_can_not_search_invisible_article()
//    {
//        $article = create(Article::class, ['title' => 'duc', 'display' => false]);
//
//        $r = $this->get('/search_articles?q=duc');
//
//        $a = data_get(json_decode($r->response->content()), 'data');
//        $this->assertEquals(0, count($a));
//        $r->dontSeeJson([
//            'title' => 'duc',
//        ]);
//
//        $article->update(['display' => true]);
//
//        $r = $this->get('/search_articles?q=duc');
//        $a = data_get(json_decode($r->response->content()), 'data');
//
//        $this->assertEquals(1, count($a));
//
//        $r->seeJsonContains([
//            'title' => 'duc',
//        ]);
//    }

    /** @test */
    public function user_can_not_see_invisible_article_in_trending_articles()
    {
        $article = create(Article::class, ['title' => 'duc']);
        $article2 = create(Article::class, ['title' => 'duc2']);

        $this->json('GET', '/articles/' . $article->id);
        $this->json('GET', '/articles/' . $article2->id);
        $this->json('GET', '/articles/' . $article2->id);
        $this->assertTrue(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));
        $this->assertTrue($this->trending->hasKey($article->id));

        $ids = $this->trending->get();
        $this->assertSame(['2', '1'], $ids);

        $article->update(['display' => false]);
        $this->assertFalse(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));
        $this->assertTrue($this->trending->hasKey($article->id));
        $ids = $this->trending->get();

        $this->assertSame(['2'], $ids);
        $article->update(['display' => true]);
        $ids = $this->trending->get();
        $this->assertSame(['2', '1'], $ids);
    }

    /** @test */
    public function admin_can_see_invisible_article()
    {
        $user = $this->signIn();
        $article = create(Article::class, ['title' => 'duc', 'display' => false, 'author_id' => $user->id]);

        $r = $this->json('GET', '/admin/articles/' . $article->id);
        $r->seeStatusCode(200);
        $r = $this->json('GET', '/admin/articles');

        $this->assertEquals(1, count(data_get(json_decode($r->response->content()), 'data')));
    }

    /** @test */
    public function admin_can_see_all_articles()
    {
        $user = $this->signIn();
        create(Article::class, ['title' => 'duc', 'author_id' => $user->id]);
        create(Article::class, ['title' => 'duc']);
        $this->assertEquals(2, Article::query()->count());

        $r = $this->json('GET', '/admin/articles', ['all' => 1]);

        $this->assertEquals(2, count(data_get(json_decode($r->response->content()), 'data')));
    }
}
