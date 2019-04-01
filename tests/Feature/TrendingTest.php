<?php

use App\Article;
use App\Trending;
use Laravel\Lumen\Testing\DatabaseMigrations;

class TrendingTest extends TestCase
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
    public function when_article_update_display_false_trending_visible_will_also_update()
    {
        $article = create(Article::class, ['display' => true]);
        $article->update(['display' => false]);

        $this->assertTrue(in_array($article->id, $this->trending->getInvisibleIds()));
    }

    /** @test */
    public function when_article_update_display_true_trending_visible_will_also_update()
    {
        $article = create(Article::class, ['display' => false]);

        $this->assertTrue(in_array($article->id, $this->trending->getInvisibleIds()));

        $article->update(['display' => true]);

        $this->assertFalse(in_array($article->id, $this->trending->getInvisibleIds()));
    }
}
