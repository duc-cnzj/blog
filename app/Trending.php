<?php

namespace App;

use Illuminate\Support\Facades\Redis;

class Trending
{
    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function get(): array
    {
        $all = $this->allKeys();
        $invisible = $this->getInvisibleIds();
        $ids = array_diff($all, $invisible);

        return array_slice($ids, 0, 12);
    }

    /**
     * @param Article $article
     *
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function push(Article $article): int
    {
        return Redis::zincrby($this->cacheKey(), 1, (int) $article->id);
    }

    /**
     * @param int $id
     *
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function remove(int $id): int
    {
        return Redis::zrem($this->cacheKey(), $id);
    }

    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function cacheKey(): string
    {
        return app()->environment('testing') ? 'testing_trending_articles' : 'trending_articles';
    }

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function reset()
    {
        Redis::del($this->cacheKey());
        Redis::del($this->invisibleKey());
    }

    /**
     * @param int $id
     *
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function hasKey(int $id)
    {
        return Redis::ZRANK($this->cacheKey(), $id) !== null;
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getInvisibleIds(): array
    {
        return Redis::SMEMBERS($this->invisibleKey());
    }

    /**
     * @param int $id
     *
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function addInvisible(int $id): bool
    {
        info('add invisible: ' . $id);

        return Redis::SADD($this->invisibleKey(), $id);
    }

    /**
     * @param int $id
     *
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function removeInvisible(int $id): bool
    {
        info('remove invisible: ' . $id);

        return Redis::SREM($this->invisibleKey(), $id);
    }

    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function invisibleKey()
    {
        return app()->environment('testing') ? 'testing_invisible_articles' : 'invisible_articles';
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    private function allKeys(): array
    {
        return Redis::zrevrange($this->cacheKey(), 0, -1);
    }
}
