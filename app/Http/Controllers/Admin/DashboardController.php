<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Article;
use App\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class DashboardController extends Controller
{
    public function index()
    {
        // 文章总数
        $articleCount = $this->getArticleCount();

        // 文章缓存率
        $cacheRate = $articleCount === 0 ? 0 : round($this->getArticleCacheCount() / $articleCount, 4) * 100;

        // 总评论条数
        $commentCount = $this->getCommentCount();

        // 总作者数
        $authorCount = $this->getAuthorCount();

        return response(
            [
                'data' => [
                    'article_count' => $articleCount,
                    'cache_rate'    => $cacheRate,
                    'comment_count' => $commentCount,
                    'author_count'  => $authorCount,
                ],
            ],
            200
        );
    }

    /**
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function getArticleCacheCount(): int
    {
        $key = app()->environment('testing') ? ':testing_article:[0-9]*' : ':article:[0-9]*';

        $cacheKey = config('cache.prefix') . $key;

        $articleCacheCount = Redis::connection()->keys($cacheKey);

        return count($articleCacheCount);
    }

    /**
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    private function getArticleCount(): int
    {
        $articleCount = Article::count();

        return $articleCount;
    }

    /**
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function getCommentCount(): int
    {
        return Comment::count();
    }

    /**
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function getAuthorCount(): int
    {
        return User::count();
    }
}
