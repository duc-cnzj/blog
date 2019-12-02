<?php

namespace App\Services;

use DucCnzj\Ip\CacheStore;
use Illuminate\Support\Facades\Redis;

/**
 * Class RedisStore
 * @package App\Adapters
 */
class IpRedisCacheStore extends CacheStore
{
    /**
     * @var string
     */
    protected $cacheKey = 'ip_cache';

    /**
     * @param  array|string  $key
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function get($key)
    {
        return json_decode(Redis::connection('cache')->hget($this->getCacheKey(), $key), JSON_OBJECT_AS_ARRAY);
    }

    /**
     * @param  string  $key
     * @param  mixed  $value
     * @return int|void
     *
     * @author duc <1025434218@qq.com>
     */
    public function put($key, $value)
    {
        return Redis::connection('cache')->hset($this->getCacheKey(), $key, json_encode($value));
    }

    /**
     * @param  string  $key
     * @return bool|int
     *
     * @author duc <1025434218@qq.com>
     */
    public function forget($key)
    {
        return Redis::connection('cache')->hdel($this->getCacheKey(), $key);
    }

    /**
     * @return bool|int
     *
     * @author duc <1025434218@qq.com>
     */
    public function flush()
    {
        return Redis::connection('cache')->del([$this->getCacheKey()]);
    }

    /**
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function count()
    {
        return Redis::connection('cache')->hlen($this->getCacheKey());
    }

    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function getCacheKey()
    {
        return app()->environment('testing') ? 'testing_' . $this->cacheKey : $this->cacheKey;
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getAllItems(): array
    {
        return array_map('json_decode', Redis::connection('cache')->hgetall($this->getCacheKey()));
    }
}
