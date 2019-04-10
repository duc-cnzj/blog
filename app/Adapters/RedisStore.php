<?php

namespace App\Adapters;

use DucCnzj\Ip\CacheStore;
use Illuminate\Support\Facades\Redis;

class RedisStore extends CacheStore
{
    protected $cacheKey = 'ip_cache';

    public function get($key)
    {
        return json_decode(Redis::connection('cache')->hget($this->cacheKey, $key), JSON_OBJECT_AS_ARRAY);
    }

    public function put($key, $value)
    {
        return Redis::connection('cache')->hset($this->cacheKey, $key, json_encode($value));
    }

    public function forget($key)
    {
        return Redis::connection('cache')->hdel($this->cacheKey, [$key]);
    }

    public function flush()
    {
        return Redis::connection('cache')->del([$this->cacheKey]);
    }

    public function count()
    {
        return Redis::connection('cache')->hlen($this->cacheKey);
    }
}
