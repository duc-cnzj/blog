<?php

use App\Tag;
use App\User;
use App\Article;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TagTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_creator()
    {
        $tag = create(Tag::class);

        $this->assertInstanceOf(User::class, $tag->creator);
    }

    /** @test */
    public function it_has_articles()
    {
        $articles = create(Article::class, [], 2);
        $tag = create(Tag::class);
        $articles->each(function (Article $item) use ($tag) {
            $item->tags()->sync([$tag->id]);
        });
        $this->assertCount(2, $tag->articles);
    }
}
