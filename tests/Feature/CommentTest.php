<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class CommentTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_see_his_article_comment()
    {
        $user = $this->signIn();
        $article = create(\App\Article::class, ['author_id' => $user->id]);
        create(\App\Comment::class, ['article_id' => $article->id, 'content' => 'duc']);

        $this->get('/admin/comments')->seeStatusCode(200)->seeJson([
            'body' => 'duc'
        ]);

    }
}
