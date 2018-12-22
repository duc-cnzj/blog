<?php

namespace Tests\Feature;

use App\Comment;
use App\SocialiteUser;
use TestCase;
use Laravel\Lumen\Testing\DatabaseMigrations;

class SocialiteUserTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function socialite_user_has_comments()
    {
        $user = create(SocialiteUser::class, ['name'=>'duc']);
        $comment = create(Comment::class, ['userable_id' => $user->id, 'userable_type'=>get_class($user)]);

        $this->assertInstanceOf(SocialiteUser::class, $comment->userable);
        $this->assertEquals('duc', $comment->userable->name);
    }
}
