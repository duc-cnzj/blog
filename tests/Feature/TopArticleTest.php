<?php

use App\Article;
use App\Contracts\TopArticleImp;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TopArticleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var TopArticleImp
     */
    protected $topArticle;

    public function setUp()
    {
        parent::setUp();

        $this->topArticle = app(TopArticleImp::class);
        $this->topArticle->reset();
    }

    /** @test */
    public function when_top_article_created_it_has_cache()
    {
        create(Article::class, ['top_at' => \Carbon\Carbon::now()]);
        create(Article::class, ['top_at' => \Carbon\Carbon::now()]);
        create(Article::class, ['top_at' => \Carbon\Carbon::now()]);
        create(Article::class, ['top_at' => \Carbon\Carbon::now()]);
        $this->assertEquals(4, count($this->topArticle->getTopArticles()));
    }

    /** @test */
    public function guest_can_see_top_articles()
    {
        $article1 = create(\App\Article::class);
        create(\App\Article::class, ['top_at' => \Carbon\Carbon::now()]);

        $res = $this->json('GET', '/top_articles');
        $data = json_decode($res->response->content());
        //dd($data);
        $this->assertEquals(1, count(data_get($data, 'data')));

        $article1->setTop();
        $res = $this->json('GET', '/top_articles');
        $data = json_decode($res->response->content());
        $this->assertEquals(2, count(data_get($data, 'data')));

        $article1->cancelSetTop();
        $res = $this->json('GET', '/top_articles');
        $data = json_decode($res->response->content());
        $this->assertEquals(1, count(data_get($data, 'data')));
    }

    /** @test */
    public function article_can_set_top()
    {
        $user = $this->signIn();
        $article = create(Article::class, ['author_id' => $user->id]);
        $article->setTop();

        $this->assertNotNull($article->fresh()->top_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $article->fresh()->top_at);
    }

    /** @test */
    public function article_can_cancel_set_top()
    {
        $user = $this->signIn();
        $article = create(Article::class, ['author_id' => $user->id]);
        $article->setTop();

        $this->assertNotNull($article->fresh()->top_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $article->fresh()->top_at);

        $article->cancelSetTop();
        $this->assertNull($article->fresh()->top_at);
    }
}
