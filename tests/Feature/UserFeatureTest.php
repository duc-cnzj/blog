<?php

use Laravel\Lumen\Testing\DatabaseMigrations;

class UserFeatureTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function only_admin_can_update_other_user_profile()
    {
        $user = $this->signIn(['name'=>'admin']);
        $user2 = $this->newTestUser([
            'name'  => 'user2',
            'email' => '2@q.com',
        ]);

        $this->json('PUT', "/admin/users/{$user2->id}", [
           'name'  => 'user3',
           'email' => '1234..', // fail
        ])->seeStatusCode(422);

        $this->json('PUT', "/admin/users/{$user2->id}", [
           'name'  => 'user3',
           'email' => '7@q.com', // fail
        ])->seeStatusCode(204);
        $this->assertEquals('user3', $user2->fresh()->name);
        $this->assertEquals('7@q.com', $user2->fresh()->email);

        $this->actingAs($user2, 'api');
        $this->json('PUT', "/admin/users/{$user->id}", [
            'name' => 'admin1',
        ])->seeStatusCode(403);
        $this->assertEquals('admin', $user->fresh()->name);
    }

    /** @test */
    public function only_admin_can_delete_any_user_except_admin()
    {
        $user = $this->signIn();
        $user2 = $this->newTestUser();
        $user3 = $this->newTestUser();
        $this->assertEquals(3, \App\User::count());
        $this->json('DELETE', "/admin/users/{$user->id}")
            ->seeStatusCode(403)
            ->seeJsonContains(['message' => '超级管理员不能删除']);

        $this->json('DELETE', "/admin/users/{$user2->id}")->seeStatusCode(204);
        $this->assertEquals(2, \App\User::count());

        $this->actingAs($user3, 'api');
        $this->json('DELETE', "/admin/users/{$user->id}")->seeStatusCode(403);
        $this->assertEquals(2, \App\User::count());
    }

    /** @test */
    public function when_user_deleted_all_articles_also_deleted()
    {
        $user = $this->newTestUser();
        create(\App\Article::class, ['author_id' => $user->id], 5);

        $this->assertEquals(5, $user->articles()->count());
        $user->delete();
        $this->assertEquals(0, \App\Article::count());
    }

    /** @test */
    public function when_user_deleted_all_categories_set_zero()
    {
        $user = $this->newTestUser();
        create(\App\Category::class, ['user_id' => $user->id], 5);

        $this->assertEquals(5, $user->categories()->count());
        $user->delete();
        $this->assertEquals(0, \App\Category::where('user_id', $user->id)->count());
    }

    /** @test */
    public function when_user_deleted_all_tags_set_zero()
    {
        $user = $this->newTestUser();
        create(\App\Tag::class, ['user_id' => $user->id], 5);

        $this->assertEquals(5, $user->tags()->count());
        $user->delete();
        $this->assertEquals(0, \App\Tag::where('user_id', $user->id)->count());
    }

    /** @test */
    public function when_user_deleted_all_rules_also_deleted()
    {
        $user = $this->newTestUser();
        create(\App\ArticleRegular::class, ['user_id' => $user->id], 5);

        $this->assertEquals(5, $user->articleRules()->count());
        $user->delete();
        $this->assertEquals(0, \App\ArticleRegular::where('user_id', $user->id)->count());
    }
}
