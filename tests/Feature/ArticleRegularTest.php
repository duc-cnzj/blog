<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class ArticleRegularTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_add_own_regular()
    {
        $this->signIn();
        $rule = '^\d+';
        $res = $this->json('post', '/admin/article_regulars', [
            'rule'   => ['express' => $rule, 'replace' => 'duc'],
            'status' => 1,
        ]);

        $res->seeStatusCode(201);

        $this->assertEquals(
            (new \App\Services\HandleRule($rule))->apply(),
            data_get(json_decode($res->response->content()), 'data.rule.express')
        );
    }

    /** @test */
    public function rule_has_it_own_format()
    {
        $this->signIn();
        $rule = '^\d+';
        $this->json('post', '/admin/article_regulars', [
            'rule'   => ['express' => $rule],
            'status' => 1,
        ])->seeStatusCode(422)->seeJson([
            'message' => 'rule 格式不正确！',
        ]);
    }

    /** @test */
    public function user_can_delete_it()
    {
        $user = $this->signIn();
        $regular = create(\App\ArticleRegular::class, ['user_id' => $user->id]);

        $this->json('DELETE', '/admin/article_regulars/' . $regular->id)->seeStatusCode(204);
    }

    /** @test */
    public function user_can_change_status()
    {
        $user = $this->signIn();
        $regular = create(\App\ArticleRegular::class, ['user_id' => $user->id, 'status' => true]);

        $this->assertEquals(true, $regular->status);
        $this->json('POST', '/admin/article_regulars/change_status', [
            'id' => $regular->id,
        ])->seeStatusCode(204);
    }
}
