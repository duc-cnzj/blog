<?php

use App\Contracts\ArticleRepoImp;
use Illuminate\Http\UploadedFile;
use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function an_authenticated_user_can_update_info()
    {
        //        'name', 'email', 'avatar', 'mobile', 'bio'
        $user = $this->signIn(
            [
                'name'   => 'duc',
                'email'  => 'a.com',
                'bio'    => 'bio',
                'mobile' => '123456789',
                'avatar' => 'http://example.com/avatar.png',
            ]
        );

        $newBio = 'foobar...';
        $this->json('POST', '/admin/update_info', [
            'bio' => $newBio,
        ])->seeStatusCode(201);
        $this->assertEquals($user->fresh()->bio, $newBio);
        $this->assertNotEquals($user->fresh()->bio, 'blabla...');

        $newEmail = '1025434218@qq.com';
        $this->json('POST', '/admin/update_info', [
            'email' => $newEmail,
        ])->seeStatusCode(201);
        $this->assertEquals($user->fresh()->email, $newEmail);

        $newName = 'Mr. duc';
        $this->json('POST', '/admin/update_info', [
            'name' => $newName,
        ])->seeStatusCode(201);
        $this->assertEquals($user->fresh()->name, $newName);
        $this->assertNotEquals($user->fresh()->name, 'duc');

        $this->post('/admin/update_info', [
            'email'  => '1025434218@qq.com',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertEquals($user->fresh()->email, '1025434218@qq.com');
        $this->assertNotEquals($user->fresh()->avatar, 'http://example.com/avatar.png');
    }

    /** @test */
    public function all_articles_cache_of_authenticated_user_can_be_reset()
    {
        $user = $this->signIn(
            [
                'name'   => 'duc',
                'email'  => 'a.com',
                'bio'    => 'bio',
                'mobile' => '123456789',
                'avatar' => 'http://example.com/avatar.png',
            ]
        );

        create(\App\Article::class, ['author_id' => $user->id], 10);

        $i = 10;
        while ($i > 0) {
            $this->json('GET', "/articles/{$i}");
            $i--;
        }

        DB::enableQueryLog();
        app(ArticleRepoImp::class)->getMany(range(1, 10));
        DB::disableQueryLog();
        $this->assertEquals(0, count(DB::getQueryLog()));

        $this->post('/admin/update_info', [
            'email'  => '1025434218@qq.com',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ])->seeStatusCode(201);

        DB::enableQueryLog();
        app(ArticleRepoImp::class)->getMany(range(1, 10));
        DB::disableQueryLog();
        $this->assertNotEquals(0, count(DB::getQueryLog()));
        $this->assertEquals($user->fresh()->email, '1025434218@qq.com');
        $this->assertNotEquals($user->fresh()->avatar, 'http://example.com/avatar.png');
    }
}
