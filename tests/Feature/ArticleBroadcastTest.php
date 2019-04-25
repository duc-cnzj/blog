<?php

use App\Article;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ArticleBroadcastTest extends TestCase
{
    use DatabaseMigrations;

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
        $this->assertEquals(1, Article::query()->count());
    }
}
