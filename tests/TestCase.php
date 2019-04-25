<?php

use App\User;
use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->withoutEvents();
        $this->withoutJobs();
    }

    protected function tearDown(): void
    {
        $cacheTestingKeys = Redis::connection('cache')->keys('*testing*');
        if (count($cacheTestingKeys) > 0) {
            Redis::connection('cache')->del($cacheTestingKeys);
        }

        $testingKeys = Redis::connection('default')->keys('*testing*');

        if (count($testingKeys) > 0) {
            Redis::connection('default')->del($testingKeys);
        }

        parent::tearDown();
    }

    public function signIn($custom = [], $user = null)
    {
        if (is_null($user)) {
            $user = create(App\User::class, $custom);
        }

        $this->actingAs($user, 'api');

        return $user;
    }
}
