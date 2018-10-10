<?php

namespace App\Services;

use App\Article;
use App\Contracts\ArticleRepoImp;
use Illuminate\Support\Facades\Cache;

class ArticleRepo implements ArticleRepoImp
{
    // protected $cacheTime = 9 * 60; // min

    public function get($id)
    {
        return Cache::rememberForever($this->cacheKey($id), function () use ($id) {
            info('从数据库获取文章id: ' . $id);
            return Article::with('tags', 'author')->findOrFail($id);
        });
    }

    public function cacheKey($id)
    {
        return 'article:' . $id;
    }

    public function removeBy($id)
    {
        info('移除缓存文章id: ' . $id);
        Cache::forget($this->cacheKey($id));
    }

    public function getMany($ids)
    {
        $arr = [];
        if ($ids) {
            foreach ($ids as $id) {
                $arr[] = $this->get($id);
            }
        }

        return $arr;
    }
}
