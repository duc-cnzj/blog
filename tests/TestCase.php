<?php


use Faker\Factory;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    public function newTestUser($custom = [])
    {
        $faker = Factory::create();

        return factory(App\User::class)->create(array_merge([
            'mobile'   => str_random(32),
            'avatar'   => $faker->imageUrl,
            'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
        ], $custom));
    }

    public function signIn($custom = [])
    {
        $user = $this->newTestUser($custom);
        $this->actingAs($user, 'api');

        return $user;
    }
}
