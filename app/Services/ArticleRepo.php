<?php

namespace App\Services;

use App\User;
use App\Article;
use App\Contracts\ArticleRepoImp;
use Illuminate\Support\Collection;

class ArticleRepo implements ArticleRepoImp
{
    /**
     * @param int $id
     * @return Article|Article[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
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
     * @return Collection
     *
     * @author duc <1025434218@qq.com>
     */
    public function getMany(array $ids): Collection
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

    /**
     * @param User $user
     *
     * @author duc <1025434218@qq.com>
     */
    public function resetArticleCacheByUser(User $user): void
    {
    }

    public function hasArticleCacheById(int $id): bool
    {
        return false;
    }
}
