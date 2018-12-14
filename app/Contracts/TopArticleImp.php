<?php

namespace App\Contracts;

use App\Article;

interface TopArticleImp
{
    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function topArticleCacheKey(): string ;

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getTopArticles(): array;

    /**
     * @param Article $article
     *
     * @return bool
     * @internal param array ...$id
     *
     * @author   duc <1025434218@qq.com>
     */
    public function addTopArticle(Article $article): bool;

    /**
     * @param Article $article
     *
     * @return bool
     * @internal param array ...$id
     *
     * @author   duc <1025434218@qq.com>
     */
    public function removeTopArticle(Article $article): bool;

    /**
     * @return void
     *
     * @author duc <1025434218@qq.com>
     */
    public function reset(): void;
}
