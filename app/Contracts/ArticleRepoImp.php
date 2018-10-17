<?php

namespace App\Contracts;

interface ArticleRepoImp
{
    /**
     * 根据文章 id 获取文章
     *
     * @param $id
     *
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function get(int $id);

    /**
     * 获取多篇文章
     *
     * @param array $ids
     *
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function getMany(Array $ids);

    /**
     * 移除文章
     *
     * @param $id
     *
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function removeBy($id);
}
