<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get(): array
    {
        $all = $this->allKeys();
        $invisible = $this->getInvisibleIds();
        $ids = array_diff($all, $invisible);

        return array_slice($ids, 0, 12);
    }

    public function push($article): int
    {
        return Redis::zincrby($this->cacheKey(), 1, (int) $article->id);
    }

    public function remove(int $id): int
    {
        return Redis::zrem($this->cacheKey(), $id);
    }

    public function cacheKey(): string
    {
        return app()->environment('testing') ? 'testing_trending_articles' : 'trending_articles';
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
        Redis::del($this->invisibleKey());
    }

    public function hasKey(int $id)
    {
        return Redis::ZRANK($this->cacheKey(), $id) !== null;
    }

    public function getInvisibleIds()
    {
        return Redis::SMEMBERS($this->invisibleKey());
    }

    public function addInvisible(int $id)
    {
        info('add invisible: ' . $id);
        return Redis::SADD($this->invisibleKey(), $id);
    }

    public function removeInvisible(int $id)
    {
        info('remove invisible: ' . $id);
        return Redis::SREM($this->invisibleKey(), $id);
    }

    public function invisibleKey()
    {
        return app()->environment('testing') ? 'testing_invisible_articles' : 'invisible_articles';
    }

    private function allKeys(): array
    {
        return Redis::zrevrange($this->cacheKey(), 0, -1);
    }
}
