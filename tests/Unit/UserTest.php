<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_articles()
    {
        $user = create(\App\User::class);
        $articles = create(\App\Article::class, ['author_id' => $user->id], 3);

        $this->assertEquals(3, $user->articles()->count());
        $this->assertInstanceOf(\App\User::class, $articles->random()->author);
    }

    /** @test */
    public function it_has_histories()
    {
        $user = create(\App\User::class);
        create(\App\History::class, ['userable_id' => $user->id, 'userable_type' => get_class($user)], 3);

        $this->assertEquals(3, $user->histories->count());
    }
}
