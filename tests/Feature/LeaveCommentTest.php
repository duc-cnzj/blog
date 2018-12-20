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
        $this->assertEquals(null, \App\Comment::first()->userable);
        $this->assertEquals(0, \App\Comment::first()->user_id);
    }

    /** @test */
    public function admin_can_leave_test_in_frontend()
    {
        $user = $this->newTestUser(['name' => 'duc', 'avatar' => 'http://localhost/avatar.jpg']);
        \Auth::shouldUse('socialite');
        $token = 'bearer ' . \Auth::fromSubject($user);

        $article = create(Article::class);
        $res = $this->post("/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ], [
            'Authorization' => $token,
        ]);
        $res->seeStatusCode(201);
        $res->seeJsonContains([
            'avatar' => 'http://localhost/avatar.jpg',
        ]);

        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->userable);
    }

    /** @test */
    public function socialite_user_also_can_leave_test_in_frontend()
    {
        $user = create(\App\SocialiteUser::class, ['name' => 'duc', 'avatar' => 'http://localhost/avatar.jpg']);
        \Auth::shouldUse('socialite');
        $token = 'bearer ' . \Auth::fromSubject($user);

        $article = create(Article::class);
        $res = $this->post("/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ], [
            'Authorization' => $token,
        ]);

        $res->seeStatusCode(201);
        $res->seeJsonContains([
            'avatar' => 'http://localhost/avatar.jpg',
        ]);

        $this->assertInstanceOf(\App\SocialiteUser::class, \App\Comment::first()->userable);
    }

    /** @test */
    public function guest_also_can_leave_test_in_frontend()
    {
        $article = create(Article::class);
        $res = $this->post("/articles/{$article->id}/comments", [
            'content' => 'this is a comment.',
        ]);

        $res->seeStatusCode(201);
        $res->seeJsonContains([
            'avatar' => '',
        ]);

        $this->assertNull(\App\Comment::first()->userable);
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
        $this->assertInstanceOf(\App\User::class, \App\Comment::first()->userable);
        $this->assertEquals($user->id, \App\Comment::first()->userable->id);
        $this->assertEquals(1, $user->comments->count());
    }
}
