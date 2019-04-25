<?php

use App\SocialiteUser;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Laravel\Socialite\Facades\Socialite;
use GuzzleHttp\Exception\RequestException;
use Laravel\Lumen\Testing\DatabaseMigrations;

class AuthControllerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function user_can_use_github_login()
    {
        $this->get('/login/github')
            ->seeStatusCode(302);
    }

    /** @test */
    public function user_can_get_socialite_info()
    {
        $user = create(\App\SocialiteUser::class, ['name' => 'duc']);
        $this->actingAs($user, 'socialite');
        $this->json('post', '/me')->seeJsonContains([
            'name' => 'duc',
        ])->seeJsonStructure([
            'data' => [
                'id',
                'name',
                'avatar',
                'last_login_at',
            ],
        ]);
    }

    /** @test */
    public function get_socialite_user_info_from_callback()
    {
        $githubUser = [
                'id'             => 1,
                'avatar'         => 'avatar',
                'user'           => [
                    'url' => 'http://duc.com/avatar',
                ],
                'nickname'          => 'nickname',
        ];

        (new Socialite())->shouldReceive('driver->stateless->user')->andReturn((object)$githubUser);

        $this->get('/login/github/callback');
        $this->seeInDatabase('socialite_users', ['name' => 'nickname']);
        $this->assertEquals(1, SocialiteUser::query()->count());
        $this->get('/login/github/callback');
        $this->assertEquals(1, SocialiteUser::query()->count());
    }

    /** @test */
    public function can_not_get_info_from_callback_and_get_401()
    {
        $githubUser = [
                'id'             => 1,
                'avatar'         => 'avatar',
                'user'           => [
                    'url' => 'http://duc.com/avatar',
                ],
                'nickname'          => 'nickname',
        ];

        $response = Mockery::mock(ResponseInterface::class);
        $request = Mockery::mock(RequestInterface::class);
        $response->shouldReceive('getStatusCode')->andReturn(401);
        $e = Mockery::mock(new RequestException('', $request, $response));

        (new Socialite())->shouldReceive('driver->stateless->user')->once()->andReturn((object) $githubUser);
        (new Socialite())->shouldReceive('driver->stateless->user')->andThrow($e);

        $this->get('/login/github/callback')
            ->seeInDatabase('socialite_users', ['name' => 'nickname']);

        $this->assertEquals(1, SocialiteUser::query()->count());

        $this->get('/login/github/callback')->seeStatusCode(401);
    }

    /** @test */
    public function can_not_get_info_from_callback_and_get_500()
    {
        $githubUser = [
                'id'             => 1,
                'avatar'         => 'avatar',
                'user'           => [
                    'url' => 'http://duc.com/avatar',
                ],
                'nickname'          => 'nickname',
        ];

        $e = Mockery::mock(new \Exception());
        (new Socialite())->shouldReceive('driver->stateless->user')->once()->andReturn((object) $githubUser);
        (new Socialite())->shouldReceive('driver->stateless->user')->andThrow($e);

        $this->get('/login/github/callback')
            ->seeInDatabase('socialite_users', ['name' => 'nickname']);
        $this->assertEquals(1, SocialiteUser::query()->count());

        $this->get('/login/github/callback')->seeStatusCode(500);
    }
}
