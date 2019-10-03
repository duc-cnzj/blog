<?php

namespace Tests\Feature;

use App\Tag;
use App\User;
use TestCase;
use App\Article;
use App\Comment;
use App\Category;
use Illuminate\Support\Arr;
use App\Events\ArticleCreated;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ArticleTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_author()
    {
        $article = create(Article::class);

        $this->assertInstanceOf(User::class, $article->author);
    }

    /** @test */
    public function it_has_category()
    {
        $article = create(Article::class);

        $this->assertInstanceOf(Category::class, $article->category);
    }

    /** @test */
    public function it_has_tags()
    {
        $tagOne = create(Tag::class, [
            'name' => 'php',
        ]);

        $tagTwo = create(Tag::class, [
            'name' => 'linux',
        ]);

        $article = create(Article::class);

        $article->tags()->sync([$tagOne->id, $tagTwo->id]);

        $this->assertEquals(2, $article->tags()->count());
        $this->assertContains('php', $article->tags()->pluck('name'));
        $this->assertContains('linux', $article->tags()->pluck('name'));
    }

    /** @test */
    public function it_has_comments()
    {
        $article = create(Article::class);
        create(Comment::class, ['article_id' => $article->id], 30);
        $this->assertInstanceOf(Comment::class, $article->comments()->first());
        $this->assertEquals(30, $article->comments()->count());
    }

    /** @test */
    public function it_has_recommend_articles()
    {
        $category = create(Category::class);
        create(Article::class, ['category_id' => $category->id], 6);
        DB::enableQueryLog();
        $recommendArticles = Article::query()->first()->getRecommendArticles();
        DB::disableQueryLog();
        $this->assertCount(3, $recommendArticles);
        $this->assertLessThanOrEqual(3, count(DB::getQueryLog()));
        $this->assertContains(Arr::random(Arr::pluck($recommendArticles, 'id')), $category->articles()->pluck('id')->toArray());
    }

    /** @test */
    public function it_has_content_html()
    {
        $content = 'content';

        $article = create(Article::class, ['content' => $content]);
        $this->assertEquals('<p>content</p>', $article->content_html);
        $this->assertNotEquals('content html bla..', $article->content_html);
    }

    /** @test */
    public function it_has_content_md()
    {
        $content = '# content';

        $article = create(Article::class, ['content' => $content]);
        $this->assertEquals('# content', $article->content_md);
        $this->assertEquals('<h1>content</h1>', $article->content_html);
        $this->assertNotEquals('content html bla..', $article->content_md);
    }

    /** @test */
    public function when_it_created_will_broadcast_everyone()
    {
        $this->expectsEvents(ArticleCreated::class);
        create(Article::class);
    }

    /** @test */
    public function it_can_remove_attribute()
    {
        $article = create(Article::class);
        $this->assertNotNull($article->content);

        $article->removeAttribute('content');
        $this->assertNull($article->content);
    }

    /** @test */
    public function it_can_remove_multiple_attribute()
    {
        $article = create(Article::class);
        $this->assertNotNull($article->content);

        $article->removeAttribute('content', 'id');
        $this->assertNull($article->content);
        $this->assertNull($article->id);
    }
}
