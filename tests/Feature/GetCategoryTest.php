<?php

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
    public function guest_can_not_search_category()
    {
        $this->json('GET', '/admin/categories')->seeStatusCode(401);
    }

    /** @test */
    public function user_can_search_category()
    {
        create(\App\Category::class, ['name' => 'duc']);
        $this->signIn();

        $this->json('GET', '/admin/categories')->seeJson(['name' => 'duc']);
        $this->json('GET', '/admin/categories', [
            'q' => 'abc',
        ])->dontSeeJson(['name' => 'duc']);
    }
}
