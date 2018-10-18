<?php

namespace Tests\Feature;

use App\Tag;
use App\User;
use TestCase;
use App\Article;
use App\Comment;
use App\Category;
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
        $comments = create(Comment::class, ['article_id' => $article->id], 30);
        $this->assertInstanceOf(Comment::class, $article->comments()->first());
        $this->assertEquals(30, $article->comments()->count());
    }

    /** @test */
    public function it_has_recommend_articles()
    {
        $category = create(Category::class);
        $articles = create(Article::class, ['category_id' => $category->id], 30);
        DB::enableQueryLog();
        $recomdendArticles = Article::first()->getRecommendArticles();
        DB::disableQueryLog();
        $this->assertCount(3, $recomdendArticles);
        $this->assertLessThanOrEqual(3, count(DB::getQueryLog()));
        $this->assertContains(array_random(array_pluck($recomdendArticles, 'id')), $category->articles()->pluck('id')->toArray());
    }

    /** @test */
    public function it_has_content_html()
    {
        $content = json_encode([
            'html' => 'content html',
            'md'   => 'content md',
        ]);

        $article = create(Article::class, ['content' => $content]);
        $this->assertEquals('content html', $article->content_html);
        $this->assertNotEquals('content html bla..', $article->content_html);
    }

    /** @test */
    public function it_has_content_md()
    {
        $content = json_encode([
            'html' => 'content html',
            'md'   => 'content md',
        ]);

        $article = create(Article::class, ['content' => $content]);
        $this->assertEquals('content md', $article->content_md);
        $this->assertNotEquals('content html bla..', $article->content_md);
    }
}
