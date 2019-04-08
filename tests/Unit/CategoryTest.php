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
    public function category_has_articles()
    {
        $category = create(Category::class);
        create(Article::class, ['category_id' => $category->id], 2);
        $this->assertCount(2, $category->articles->toArray());
    }
}
