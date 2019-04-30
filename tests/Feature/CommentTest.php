<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class CommentTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function guest_can_see_article_comments()
    {
        $article = create(\App\Article::class);
        $comment1 = create(\App\Comment::class, ['article_id' => $article->id]);
        create(\App\Comment::class, ['article_id' => $article->id, 'comment_id' => $comment1->id]);
        create(\App\Comment::class, ['article_id' => $article->id]);

        $res = $this->json('get', "/articles/{$article->id}/comments");
        $res->seeStatusCode(200);
        $res->seeJsonStructure([
            'data' => [
                '*' => [
                    'id', 'body', 'comment_id', 'created_at', 'author' => ['name', 'avatar'], 'article', 'replies' => [
                        '*' => ['id', 'body', 'comment_id', 'created_at', 'author' => ['name', 'avatar'], 'article'],
                    ],
                ],
            ],
        ]);
    }

    /** @test */
    public function user_can_see_his_article_comment()
    {
        $user = $this->signIn();
        $article = create(\App\Article::class, ['author_id' => $user->id]);
        create(\App\Comment::class, ['article_id' => $article->id, 'content' => 'duc']);

        $this->get('/admin/comments')->seeStatusCode(200)->seeJson([
            'body' => 'duc',
        ]);
    }

    /** @test */
    public function admin_can_see_single_comment_with_article_and_author()
    {
        $user = $this->signIn();
        $article = create(\App\Article::class, ['author_id' => $user->id]);
        $comment = create(\App\Comment::class, ['article_id' => $article->id, 'content' => 'duc']);

        $this->json('get', '/admin/comments/' . $comment->id)
            ->seeJsonStructure([
                'data' => [
                    'id',
                    'body',
                    'comment_id',
                    'created_at',
                    'author'     => [
                            'name',
                            'avatar',
                        ],
                    'article',
                    'my_comments',
                ],
            ])
            ->assertResponseOk();
    }

    /** @test */
    public function user_can_delete_self_article_comments()
    {
        create(\App\User::class);
        $user = create(\App\User::class);

        $this->signIn([], $user);
        $this->assertFalse($user->isAdmin());

        $article = create(\App\Article::class, ['author_id' => $user->id]);
        $comment = create(\App\Comment::class, ['article_id' => $article->id]);
        create(\App\Comment::class, ['comment_id' => $comment->id, 'article_id' => $comment->article_id], 3);

        $this->delete('/admin/comments/' . $comment->id)->seeStatusCode(204);
    }

    /** @test */
    public function user_can_not_delete_other_article_comments()
    {
        create(\App\User::class);
        $user1 = $this->signIn();
        $user2 = create(\App\User::class);

        $this->assertFalse($user1->isAdmin());

        $article = create(\App\Article::class, ['author_id' => $user2->id]);
        $comment = create(\App\Comment::class, ['article_id' => $article->id]);
        create(\App\Comment::class, ['comment_id' => $comment->id, 'article_id' => $comment->article_id], 3);

        $this->delete('/admin/comments/' . $comment->id)->seeStatusCode(403);
    }

    /** @test */
    public function admin_can_delete_all_comment()
    {
        $admin = $this->signIn();
        $user = create(\App\User::class);

        $this->assertTrue($admin->isAdmin());

        $article = create(\App\Article::class, ['author_id' => $user->id]);
        $comment = create(\App\Comment::class, ['article_id' => $article->id]);

        $this->delete('/admin/comments/' . $comment->id)->seeStatusCode(204);
    }

    /** @test */
    public function user_can_delete_nested_comments()
    {
        $this->signIn();

        $article = create(\App\Article::class);
        $comment = create(\App\Comment::class, ['article_id' => $article->id]);
        create(\App\Comment::class, ['comment_id' => $comment->id, 'article_id' => $comment->article_id], 3);

        $this->assertEquals(4, $comment->article->comments->count());

        $this->delete('/admin/comments/' . $comment->id)->seeStatusCode(204);

        $this->assertEquals(0, $comment->article->fresh()->comments->count());
    }
}
