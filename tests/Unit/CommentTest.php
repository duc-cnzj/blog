<?php

namespace Tests\Feature;

use App\User;
use TestCase;
use App\Article;
use App\Comment;
use Laravel\Lumen\Testing\DatabaseMigrations;

class CommentTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_has_article()
    {
        $comment = create(Comment::class);

        $this->assertInstanceOf(Article::class, $comment->article);
    }

    /** @test */
    public function it_has_user()
    {
        $comment = create(Comment::class);

        $this->assertEquals(null, $comment->user);
    }

    /** @test */
    public function it_has_many_replies()
    {
        $comment1 = create(Comment::class);
        create(Comment::class, ['comment_id' => $comment1->id]);

        $this->assertInstanceOf(Comment::class, $comment1->replies()->first());
    }

    /** @test */
    public function it_has_parent_reply()
    {
        $comment1 = create(Comment::class);
        $comment2 = create(Comment::class, ['comment_id' => $comment1->id]);

        $this->assertInstanceOf(Comment::class, $comment2->parentReply);
    }
}
