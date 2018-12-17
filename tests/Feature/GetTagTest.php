<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class GetTagTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_can_not_search_tag()
    {
        $this->json('GET', '/admin/tags')->seeStatusCode(401);
    }

    /** @test */
    public function user_can_search_tag()
    {
        create(\App\Tag::class, ['name' => 'duc']);
        $this->signIn();

        $this->json('GET', '/admin/tags')->seeJson(['name' => 'duc']);
        $this->json('GET', '/admin/tags', [
            'q' => 'abc',
        ])->dontSeeJson(['name' => 'duc']);
    }
}
