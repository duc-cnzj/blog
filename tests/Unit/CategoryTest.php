<?php

use App\User;
use App\Article;
use App\Category;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_creator()
    {
        $category = create(Category::class);

        $this->assertInstanceOf(User::class, $category->creator);
    }

    /** @test */
    public function it_has_articles()
    {
        $category = create(Category::class);
        $articles = create(Article::class, ['category_id' => $category->id], 20);
        $this->assertCount(20, $category->articles->toArray());
    }
}
