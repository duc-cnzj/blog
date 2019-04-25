<?php

namespace App;

use Illuminate\Support\Facades\Redis;

/**
 * Class Trending
 * @package App
 */
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
        return Redis::connection()->zincrby($this->cacheKey(), 1, (int) $article->id);
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
        return Redis::connection()->zrem($this->cacheKey(), $id);
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
        Redis::connection()->del([$this->cacheKey(), $this->invisibleKey()]);
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
        return Redis::connection()->zrank($this->cacheKey(), $id) !== null;
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getInvisibleIds(): array
    {
        return Redis::connection()->smembers($this->invisibleKey());
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
        return Redis::connection()->sadd($this->invisibleKey(), [$id]);
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
        return Redis::connection()->srem($this->invisibleKey(), $id);
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
        return Redis::connection()->zrevrange($this->cacheKey(), 0, -1);
    }
}
