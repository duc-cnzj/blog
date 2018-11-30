<?php

namespace App\Contracts;

use App\User;
use Illuminate\Database\Eloquent\Model;

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
     * 获取多篇文章
     *
     * @param array $ids
     *
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getMany(array $ids): array;

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
