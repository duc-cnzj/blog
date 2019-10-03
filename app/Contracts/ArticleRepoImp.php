<?php

namespace App\Contracts;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface ArticleRepoImp
 * @package App\Contracts
 */
interface ArticleRepoImp
{
    /**
     * 根据文章 id 获取文章
     *
     * @param $id
     *
     * @return Model
     *
     * @author duc <1025434218@qq.com>
     */
    public function get(int $id);

    /**
     * @param int $id
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function hasArticleCacheById(int $id): bool;

    /**
     * 获取多篇文章
     *
     * @param array $ids
     *
     * @return Collection
     *
     * @author duc <1025434218@qq.com>
     */
    public function getMany(array $ids): Collection;

    /**
     * 移除文章
     *
     * @param $id
     *
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function removeBy(int $id);

    /**
     * @param User $user
     *
     * @return void
     *
     * @author duc <1025434218@qq.com>
     */
    public function resetArticleCacheByUser(User $user);
}
