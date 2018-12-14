<?php

use App\Article;
use Laravel\Lumen\Testing\DatabaseMigrations;

class LeaveCommentTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_can_leave_comments()
    {
        $article = create(Article::class);
        $res = $this->post("/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ]);
        $res->seeStatusCode(201);
        $res->seeJsonContains([
            'avatar' => '',
        ]);

        $this->assertEquals(1, $article->comments->count());
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->user);
        $this->assertEquals(0, \App\Comment::first()->user_id);
    }

    /** @test */
    public function user_can_leave_comments_in_front()
    {
        $user = $this->signIn();
        $article = create(Article::class);
        $this->post("/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ])->seeStatusCode(201);

        $this->assertEquals(1, $article->comments->count());
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->user);
        $this->assertEquals($user->id, \App\Comment::first()->user->id);
    }

    /** @test */
    public function user_can_leave_comments_in_background()
    {
        $user = $this->signIn();
        $article = create(Article::class);
        $res = $this->post("/admin/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ]);
        $res->seeStatusCode(201);
        $res->seeJsonContains([
            'name'   => $user->name,
            'avatar' => $user->avatar,
        ]);

        $this->assertEquals(1, $article->comments->count());
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->user);
        $this->assertEquals($user->id, \App\Comment::first()->user->id);
        $this->assertEquals(1, $user->comments->count());
    }
}
