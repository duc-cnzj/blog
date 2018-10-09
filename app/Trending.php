<?php


namespace App;


use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get()
    {
        return array_map('json_decode', Redis::zrevrange($this->cacheKey(), 0, 12));
    }

    public function push($article)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'id'  => $article->id,
            'title' => $article->title,
            'headImage' => $article->head_image,
            'created_at' => $article->created_at->toDateTimeString(),
            'author' => [
                'id' => $article->author->id,
                'name' => $article->author->name
            ]
        ]));
    }

    public function cacheKey()
    {
        return 'popular_articles';
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }
}
