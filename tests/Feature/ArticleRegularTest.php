<?php

use App\ArticleRegular;
use Laravel\Lumen\Testing\DatabaseMigrations;

class ArticleRegularTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_see_his_own_regulars()
    {
        $this->signIn();
        $user1 = $this->newTestUser();
        $rule = '^\d+';
        $this->json('post', '/admin/article_regulars', [
            'rule'   => ['express' => $rule, 'replace' => 'duc'],
            'status' => 1,
        ])->seeStatusCode(201);

        create(ArticleRegular::class, ['user_id' => $user1->id], 3);

        $res = $this->get('/admin/article_regulars')->seeStatusCode(200);

        $data = json_decode($res->response->content());

        $this->assertEquals(1, count(data_get($data, 'data')));
        $this->assertEquals(4, ArticleRegular::count());
    }

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
        $regular = create(ArticleRegular::class, ['user_id' => $user->id]);

        $this->json('DELETE', '/admin/article_regulars/' . $regular->id)->seeStatusCode(204);
    }

    /** @test */
    public function user_cannot_delete_others_regulars()
    {
        $user1 = $this->newTestUser();
        $user2 = $this->newTestUser();
        $authUser = $this->signIn([], $user2);

        $this->assertFalse($authUser->isAdmin());
        $regular = create(ArticleRegular::class, ['user_id' => $user1->id]);
        $this->assertNotEquals($regular->user_id, $authUser->id);

        $this->json('DELETE', '/admin/article_regulars/' . $regular->id)->seeStatusCode(403);
    }

    /** @test */
    public function admin_can_delete_others_regulars()
    {
        $user1 = $this->newTestUser();
        $user2 = $this->newTestUser();
        $authUser = $this->signIn([], $user1);

        $this->assertTrue($authUser->isAdmin());
        $regular = create(ArticleRegular::class, ['user_id' => $user2->id]);
        $this->assertNotEquals($regular->user_id, $authUser->id);

        $this->json('DELETE', '/admin/article_regulars/' . $regular->id)->seeStatusCode(204);
    }

    /** @test */
    public function user_can_test_own_regular()
    {
        $user = $this->signIn();
        create(ArticleRegular::class, [
            'user_id' => $user->id,
            'status'  => true,
            'rule'    => ['express' => '/^\d+/', 'replace' => 'a'],
        ]);
        $this->post('/admin/article_regulars/test', [
           'body' => '123456a',
        ])->seeJson([
            'data' => [
                'body' => 'aa',
            ],
        ]);
    }

    /** @test */
    public function user_can_change_status()
    {
        $user = $this->signIn();
        $regular = create(ArticleRegular::class, ['user_id' => $user->id, 'status' => true]);

        $this->assertEquals(true, $regular->status);
        $this->json('POST', '/admin/article_regulars/change_status', [
            'id' => $regular->id,
        ])->seeStatusCode(204);
    }
}
