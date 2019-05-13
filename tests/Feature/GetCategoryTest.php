<?php

use App\Article;
use App\Category;
use Laravel\Lumen\Testing\DatabaseMigrations;

class GetCategoryTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function anyone_can_get_all_categories()
    {
        $this->get('/categories')->seeStatusCode(200);
    }

    /** @test */
    public function anyone_can_get_all_categories_which_has_articles()
    {
        create(Category::class, [], 3);
        create(Article::class, ['category_id' => 1]);
        $res = $this->get('/categories')
            ->seeStatusCode(200)
            ->seeJson(['id' => 1])
            ->response
            ->content();
        $this->assertEquals(1, count(json_decode($res, true)['data']));
    }

    /** @test */
    public function guest_can_not_search_category()
    {
        $this->json('GET', '/admin/categories')->seeStatusCode(401);
    }

    /** @test */
    public function user_can_search_category()
    {
        create(Category::class, ['name' => 'duc']);
        $this->signIn();

        $this->json('GET', '/admin/categories')->seeJson(['name' => 'duc']);
        $this->json('GET', '/admin/categories', [
            'q' => 'abc',
        ])->dontSeeJson(['name' => 'duc']);
    }
}
