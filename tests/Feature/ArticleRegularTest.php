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

        $this->assertEquals((new \App\Services\HandleRule($rule))->apply(),
            data_get(json_decode($res->response->content()), 'data.rule.express'));
    }
}
