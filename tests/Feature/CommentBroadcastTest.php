<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class CommentBroadcastTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function after_article_created_it_will_broadcast_everyone()
    {
        $this->expectsEvents(\App\Events\CommentCreated::class);
        create(\App\Comment::class);
    }
}
