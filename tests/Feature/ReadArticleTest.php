<?php

use App\Article;
use App\Trending;
use App\Contracts\ArticleRepoImp;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ReadArticleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var ArticleRepoImp
     */
    protected $articleRepoImp;
    /**
     * @var Trending
     */
    protected $trending;

    public function setUp()
    {
        parent::setUp();

        $this->articleRepoImp = app(ArticleRepoImp::class);

        $this->trending = new Trending();
        $this->trending->reset();
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
        $r->seeJson(['title' => 'article 1'])
            ->dontSeeJson(['title' => 'article 2']);
        DB::disableQueryLog();
        $this->assertEquals(4, count(DB::getQueryLog()));

        DB::flushQueryLog();

        DB::enableQueryLog();
        $this->json('GET', '/articles/1')->seeStatusCode(200);
        DB::disableQueryLog();
        $this->assertEquals(0, count(DB::getQueryLog()));
    }

    /** @test */
    public function after_article_created_it_will_broadcast_everyone()
    {
        $this->expectsEvents(\App\Events\ArticleCreated::class);

        $this->signIn();

        $article = make(Article::class, ['title' => 'test', 'content' => 'content']);
        $tag = create(\App\Tag::class);
        $category = create(\App\Category::class);

        $this->json('POST', '/admin/articles', array_merge($article->toArray(), [
            'tags'     => [$tag->name],
            'category' => $category->name,
        ]))->seeStatusCode(201);

        $this->seeInDatabase('articles', ['title' => 'test']);
        $this->assertEquals(1, Article::count());
    }

    /** @test */
    public function after_article_deleted_cache_also_deleted()
    {
        $this->signIn();
        $article = create(Article::class);

        $this->json('GET', "/articles/{$article->id}")->seeStatusCode(200);
        $this->assertTrue($this->articleRepoImp->hasArticleCacheById($article->id));

        $this->json('DELETE', "/admin/articles/{$article->id}")->seeStatusCode(204);
        $this->assertFalse($this->articleRepoImp->hasArticleCacheById($article->id));

        $count = count((new Trending)->get());
        $this->assertEquals(0, $count);
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
    public function guest_can_leave_comments()
    {
        $article = create(Article::class);
        $res = $this->post("/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ]);
        $res->seeStatusCode(201);
        $res->seeJsonContains([
           'avatar' => '',
        ]);

        $this->assertEquals(1, $article->comments->count());
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->user);
        $this->assertEquals(0, \App\Comment::first()->user_id);
    }

    /** @test */
    public function user_can_leave_comments_in_front()
    {
        $user = $this->signIn();
        $article = create(Article::class);
        $this->post("/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ])->seeStatusCode(201);

        $this->assertEquals(1, $article->comments->count());
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->user);
        $this->assertEquals($user->id, \App\Comment::first()->user->id);
    }

    /** @test */
    public function user_can_leave_comments_in_background()
    {
        $user = $this->signIn();
        $article = create(Article::class);
        $res = $this->post("/admin/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ]);
        $res->seeStatusCode(201);
        $res->seeJsonContains([
           'name'   => $user->name,
           'avatar' => $user->avatar,
        ]);

        $this->assertEquals(1, $article->comments->count());
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->user);
        $this->assertEquals($user->id, \App\Comment::first()->user->id);
        $this->assertEquals(1, $user->comments->count());
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
        $this->assertFalse($this->articleRepoImp->hasArticleCacheById($article->id));
        $this->assertEquals('title1', $this->articleRepoImp->get($article->id)->title);
        $r = $this->json('GET', '/articles/' . $article->id);
        $r->seeStatusCode(200);

        $this->assertTrue($this->articleRepoImp->hasArticleCacheById($article->id));

        $article->update(['title' => 'title2']);
        $this->assertFalse($this->articleRepoImp->hasArticleCacheById($article->id));
        $this->assertEquals('title2', $this->articleRepoImp->get($article->id)->title);
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
        $this->assertEquals($article->id, Article::findOrFail($article->id)->id);
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
        $this->assertTrue($this->articleRepoImp->hasArticleCacheById($article->id));

        $article->update(['display' => false]);
        $this->assertFalse($this->articleRepoImp->hasArticleCacheById($article->id));
        $r = $this->json('GET', '/articles/' . $article->id);
        $r->seeStatusCode(404);
    }
//
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
        $this->assertTrue($this->articleRepoImp->hasArticleCacheById($article->id));
        $this->assertTrue($this->trending->hasKey($article->id));

        $ids = $this->trending->get();
        $this->assertSame(['2', '1'], $ids);

        $article->update(['display' => false]);
        $this->assertFalse($this->articleRepoImp->hasArticleCacheById($article->id));
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
}
