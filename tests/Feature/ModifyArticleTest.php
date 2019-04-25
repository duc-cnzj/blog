<?php

use App\Article;
use App\Trending;
use Illuminate\Support\Str;
use App\Contracts\ArticleRepoImp;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ModifyArticleTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @var Trending
     */
    protected $trending;

    public function setUp(): void
    {
        parent::setUp();

        $this->trending = new Trending();
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
        $this->assertEquals(0, \App\Comment::query()->where('article_id', $article->id)->count());
    }

    /** @test */
    public function user_create_article_always_has_md_and_html()
    {
        $content = '# h1';
        $user = $this->signIn();
        $res = $this->post('/admin/articles', [
            'head_image' => 'http://avatar.com/image.jpg',
            'title'      => 'article title',
            'desc'       => Str::random(32),
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
            'desc'       => Str::random(32),
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
            'desc'       => Str::random(32),
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
    public function user_can_not_update_article_if_not_own()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $this->signIn([], $user2);
        $article = create(Article::class, ['title' => 'title1', 'author_id' => $user1->id]);

        $this->json('PUT', '/admin/articles/' . $article->id, [
            'content'    => 'content',
            'head_image' => 'http://avatar.com/image.jpg',
            'title'      => 'article title',
            'desc'       => Str::random(32),
            'category'   => 'php',
            'tags'       => ['php', 'js'],
        ])->seeStatusCode(403);
    }

    /** @test */
    public function super_admin_can_update_article_if_not_own()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $this->signIn([], $user1);
        $article = create(Article::class, ['title' => 'title1', 'author_id' => $user2->id]);

        $this->json('PUT', '/admin/articles/' . $article->id, [
            'content'    => 'content',
            'head_image' => 'http://avatar.com/image.jpg',
            'title'      => 'article title',
            'desc'       => Str::random(32),
            'category'   => 'php',
            'tags'       => ['php', 'js'],
        ])->seeStatusCode(200);
        $this->assertTrue(\Auth::user()->isAdmin());
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
            'desc'       => Str::random(32),
            'content'    => $doc,
            'category'   => 'php',
            'tags'       => ['php', 'js'],
            'display'    => true,
        ]);

        $data = data_get(json_decode($res->response->content()), 'data.content');
        $this->assertEquals('<h1>A123B456*789</h1>', $data);
    }

    /** @test */
    public function user_who_has_it_can_change_display()
    {
        $user = $this->signIn();
        $article = create(Article::class, ['author_id' => $user->id]);
        $this->json('PUT', '/admin/article_change_display/' . $article->id)
            ->seeStatusCode(204);
    }

    /** @test */
    public function user_who_do_not_has_it_can_not_change_display()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $article = create(Article::class, ['author_id' => $user1->id]);
        $this->signIn([], $user2);
        $this->json('PUT', '/admin/article_change_display/' . $article->id)
            ->seeStatusCode(403);
        $this->assertFalse(\Auth::user()->isAdmin());
    }

    /** @test */
    public function super_admin_who_do_not_has_it_can_change_display()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $article = create(Article::class, ['author_id' => $user2->id]);
        $this->signIn([], $user1);
        $this->json('PUT', '/admin/article_change_display/' . $article->id)
            ->seeStatusCode(204);
        $this->assertTrue(\Auth::user()->isAdmin());
    }

    /** @test */
    public function user_can_set_top()
    {
        $user = $this->signIn();
        $article = create(Article::class, ['author_id' => $user->id]);
        $this->json('PUT', '/admin/article_set_top/' . $article->id)
            ->seeStatusCode(204);
    }

    /** @test */
    public function user_can_not_set_top_if_not_own()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $this->signIn([], $user2);
        $article = create(Article::class, ['author_id' => $user1->id]);
        $this->json('PUT', '/admin/article_set_top/' . $article->id)
            ->seeStatusCode(403);
    }

    /** @test */
    public function super_admin_can_set_top_if_not_own()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $this->signIn([], $user1);
        $article = create(Article::class, ['author_id' => $user2->id]);
        $this->json('PUT', '/admin/article_set_top/' . $article->id)
            ->seeStatusCode(204);
    }

    /** @test */
    public function user_can_cancel_top()
    {
        $user = $this->signIn();
        $article = create(Article::class, ['author_id' => $user->id]);
        $this->json('PUT', '/admin/article_cancel_set_top/' . $article->id)
            ->seeStatusCode(204);
    }

    /** @test */
    public function user_can_not_cancel_top_if_not_own()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $this->signIn([], $user2);
        $article = create(Article::class, ['author_id' => $user1->id]);
        $this->json('PUT', '/admin/article_cancel_set_top/' . $article->id)
            ->seeStatusCode(403);
    }

    /** @test */
    public function super_admin_can_cancel_top_if_not_own()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $this->signIn([], $user1);
        $article = create(Article::class, ['author_id' => $user2->id]);
        $this->json('PUT', '/admin/article_cancel_set_top/' . $article->id)
            ->seeStatusCode(204);
    }

    /** @test */
    public function user_can_not_delete_other_article()
    {
        $user1 = create(\App\User::class);
        $user2 = create(\App\User::class);
        $this->signIn([], $user2);
        $article = create(Article::class, ['author_id' => $user1->id]);
        $this->json('delete', '/admin/articles/' . $article->id)
            ->seeStatusCode(403);
    }
}
