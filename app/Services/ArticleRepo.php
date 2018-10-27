<?php

namespace App\Services;

use App\Article;
use App\Contracts\ArticleRepoImp;

class ArticleRepo implements ArticleRepoImp
{
    /**
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|static|static[]
     *
     * @author duc <1025434218@qq.com>
     */
    public function get(int $id)
    {
        return Article::with('category', 'tags', 'author')->findOrFail($id);
    }

    /**
     * @param array $ids
     *
     * @return \Illuminate\Database\Eloquent\Collection
     *
     * @author duc <1025434218@qq.com>
     */
    public function getMany(array $ids)
    {
        return Article::with('category', 'tags', 'author')->findMany($ids);
    }

    /**
     * @param $id
     *
     * @author duc <1025434218@qq.com>
     * @return mixed|void
     */
    public function removeBy(int $id)
    {
        // 没有缓存所以不需要 do anything
    }
}
