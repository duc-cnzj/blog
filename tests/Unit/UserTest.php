<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_articles()
    {
        $user = $this->newTestUser();
        $articles = create(\App\Article::class, ['author_id' => $user->id], 3);

        $this->assertEquals(3, $user->articles()->count());
        $this->assertInstanceOf(\App\User::class, $articles->random()->author);
    }
}
