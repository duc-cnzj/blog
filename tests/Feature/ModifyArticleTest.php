<?php

use App\Article;
use App\Trending;
use App\Contracts\ArticleRepoImp;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ModifyArticleTest extends TestCase
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
    }

    /** @test */
    public function after_article_deleted_cache_also_deleted()
    {
        $this->signIn();
        $article = create(Article::class);

        $this->json('GET', "/articles/{$article->id}")->seeStatusCode(200);
        $this->assertTrue(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));

        $this->json('DELETE', "/admin/articles/{$article->id}")->seeStatusCode(204);
        $this->assertFalse(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));
    }

    /** @test */
    public function after_article_deleted_trending_also_deleted()
    {
        $this->signIn();
        $article = create(Article::class);

        $this->json('GET', "/articles/{$article->id}")->seeStatusCode(200);
        $this->assertTrue(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));

        $this->assertTrue($this->trending->hasKey($article->id));

        $this->json('DELETE', "/admin/articles/{$article->id}")->seeStatusCode(204);
        $this->assertFalse(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));

        $this->assertFalse($this->trending->hasKey($article->id));
    }

    /** @test */
    public function after_article_deleted_comments_also_deleted()
    {
        $this->signIn();
        $article = create(Article::class);

        $this->json('GET', "/articles/{$article->id}")->seeStatusCode(200);
        $this->post("/articles/{$article->id}/comments", [
            'content' => 'leave a reply.',
        ])->seeStatusCode(201);
        $this->assertEquals(1, $article->comments->count());
        $article->delete();
        $this->assertEquals(0, \App\Comment::where('article_id', $article->id)->count());
    }


    /** @test */
    public function user_create_article_always_has_md_and_html()
    {
        $content = '# h1';
        $user = $this->signIn();
        $res = $this->post('/admin/articles', [
            'head_image' => 'http://avatar.com/image.jpg',
            'title'      => 'article title',
            'desc'       => str_random(32),
            'content'    => $content,
            'category'   => 'php',
            'tags'       => ['php', 'js'],
            'display'    => true,
        ]);

        $res->seeStatusCode(201);
        //        exit;
        $parsedown = new \Parsedown();

        $mdContent = $parsedown->text($content);
        $this->assertEquals(json_encode([
            'html' => $mdContent,
            'md'   => $content,
        ]), $user->articles()->first()->content);
    }

    /** @test */
    public function user_update_article_always_has_md_and_html()
    {
        $content = '# h1';
        $user = $this->signIn();
        $res = $this->post('/admin/articles', [
            'head_image' => 'http://avatar.com/image.jpg',
            'title'      => 'article title',
            'desc'       => str_random(32),
            'content'    => $content,
            'category'   => 'php',
            'tags'       => ['php', 'js'],
            'display'    => true,
        ]);

        $res->seeStatusCode(201);
        $articleId = data_get(json_decode($res->response->content()), 'data.id');

        $newContent = '## h1';
        $r = $this->json('put', "/admin/articles/{$articleId}", [
            'content'    => $newContent,
            'head_image' => 'http://avatar.com/image.jpg',
            'title'      => 'article title',
            'desc'       => str_random(32),
            'category'   => 'php',
            'tags'       => ['php', 'js'],
        ]);

        $parsedown = new \Parsedown();

        $mdContent = $parsedown->text($newContent);

        $r->seeStatusCode(200);
        $this->assertEquals(json_encode([
            'html' => $mdContent,
            'md'   => $newContent,
        ]), $user->articles()->first()->content);
    }

    /** @test */
    public function when_article_updated_it_will_remove_cache()
    {
        $article = create(Article::class, ['title' => 'title1']);

        $this->assertEquals('title1', $article->title);
        $this->assertFalse(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));
        $this->assertEquals('title1', app(ArticleRepoImp::class)->get($article->id)->title);
        $r = $this->json('GET', '/articles/' . $article->id);
        $r->seeStatusCode(200);

        $this->assertTrue(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));

        $article->update(['title' => 'title2']);
        $this->assertFalse(app(ArticleRepoImp::class)->hasArticleCacheById($article->id));
        $this->assertEquals('title2', app(ArticleRepoImp::class)->get($article->id)->title);
    }


    /** @test */
    public function article_should_apply_rule()
    {
        // <h1>dadsaa↵</h1>
        $doc = '# a123b456c789';

        $user = $this->signIn();

        create(\App\ArticleRegular::class, ['user_id' => $user->id, 'status'=>true, 'rule' => [
            'express' => 'a',
            'replace' => 'A',
        ]]);

        create(\App\ArticleRegular::class, ['user_id' => $user->id, 'status'=>true, 'rule' => [
            'express' => 'b',
            'replace' => 'B',
        ]]);

        create(\App\ArticleRegular::class, ['user_id' => $user->id, 'status'=>true, 'rule' => [
            'express' => '[a-z]',
            'replace' => '*',
        ]]);

        $res = $this->post('/admin/articles', [
            'head_image' => 'http://avatar.com/image.jpg',
            'title'      => 'article title',
            'desc'       => str_random(32),
            'content'    => $doc,
            'category'   => 'php',
            'tags'       => ['php', 'js'],
            'display'    => true,
        ]);

        $data = data_get(json_decode($res->response->content()), 'data.content');
        $this->assertEquals('<h1>A123B456*789</h1>', $data);
    }
}
