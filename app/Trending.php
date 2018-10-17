<?php


namespace App;


use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get(): array
    {
        return Redis::zrevrange($this->cacheKey(), 0, 12);
    }

    public function push($article)
    {
        Redis::zincrby($this->cacheKey(), 1, $article->id);
    }

    public function cacheKey(): string
    {
        return 'trending_articles';
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}
