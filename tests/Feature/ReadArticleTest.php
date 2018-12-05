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
        $this->assertTrue(\Illuminate\Support\Facades\Cache::has("article:{$article->id}"));

        $this->json('DELETE', "/admin/articles/{$article->id}")->seeStatusCode(204);
        $this->assertFalse(\Illuminate\Support\Facades\Cache::has("article:{$article->id}"));

        $count = count((new Trending)->get());
        $this->assertEquals(0, $count);
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
           'avatar' => ''
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
           'name' => $user->name,
           'avatar' => $user->avatar,
        ]);

        $this->assertEquals(1, $article->comments->count());
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->user);
        $this->assertEquals($user->id, \App\Comment::first()->user->id);
        $this->assertEquals(1, $user->comments->count());
    }
}
