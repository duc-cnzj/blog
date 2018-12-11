<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get(): array
    {
        return Redis::zrevrange($this->cacheKey(), 0, 12);
    }

    public function push($article): int
    {
        return Redis::zincrby($this->cacheKey(), 1, $article->id);
    }

    public function remove(int $id): int
    {
        return Redis::zrem($this->cacheKey(), $id);
    }

    public function cacheKey(): string
    {
        return app()->environment('testing') ? 'testing_trending_articles' : 'trending_articles';
    }

    public function reset(): int
    {
        return Redis::del($this->cacheKey());
    }
}
