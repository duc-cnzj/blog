<?php

namespace App;

use Carbon\Carbon;
use App\Contracts\TopArticleImp;
use Illuminate\Support\Facades\Redis;

/**
 * Class TopArticle
 * @package App
 */
class TopArticle implements TopArticleImp
{
    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getTopArticles(): array
    {
        $all = Redis::connection()->zrevrange($this->topArticleCacheKey(), 0, -1);
        $invisible = (new Trending())->getInvisibleIds();
        $ids = array_diff($all, $invisible);

        return array_slice($ids, 0, 12);
    }

    /**
     * @param Article $article
     *
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function addTopArticle(Article $article): bool
    {
        /** @var Carbon $time */
        $time = $article->top_at;

        return Redis::connection()->zadd($this->topArticleCacheKey(), [
            $article->id => $time->getTimestamp(),
        ]);
    }

    /**
     * @param Article $article
     *
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function removeTopArticle(Article $article): bool
    {
        return Redis::connection()->zrem($this->topArticleCacheKey(), $article->id);
    }

    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function topArticleCacheKey(): string
    {
        return app()->environment('testing') ? 'testing_top_articles' : 'top_articles';
    }

    /**
     * @return void
     *
     * @author duc <1025434218@qq.com>
     */
    public function reset(): void
    {
        Redis::connection()->del([$this->topArticleCacheKey()]);
    }
}
