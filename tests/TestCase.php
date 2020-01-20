<?php

define('APP_START', microtime(true));

use Illuminate\Support\Facades\Hash;
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
        $app = require __DIR__ . '/../bootstrap/app.php';
        Hash::setRounds(4);

        return $app;
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->clearTestingCache();

        $this->beforeApplicationDestroyed(function () {
            $this->clearTestingCache();
        });

        $this->withoutEvents();
        $this->withoutJobs();
    }

    public function signIn($custom = [], $user = null)
    {
        if (is_null($user)) {
            $user = create(App\User::class, $custom);
        }

        $this->actingAs($user, 'api');

        return $user;
    }

    protected function clearTestingCache(): void
    {
        $cacheTestingKeys = Redis::connection('cache')->keys('*testing*');
        if (count($cacheTestingKeys) > 0) {
            Redis::connection('cache')->del($cacheTestingKeys);
        }

        $testingKeys = Redis::connection('default')->keys('*testing*');

        if (count($testingKeys) > 0) {
            Redis::connection('default')->del($testingKeys);
        }
    }
}
