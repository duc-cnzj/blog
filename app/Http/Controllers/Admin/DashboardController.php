<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Article;
use App\Comment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;

class DashboardController extends Controller
{
    public function index()
    {
        // 文章总数
        $articleCount = $this->getArticleCount();

        // 文章缓存率
        $cacheRate = round($this->getArticleCacheCount() / $articleCount, 4) * 100;

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

    public function getArticleCacheCount(): int
    {
        $cacheKey = config('cache.prefix') . ':article:[0-9]*';

        $articleCacheCount = Redis::keys($cacheKey);

        return count($articleCacheCount);
    }

    /**
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    private function getArticleCount(): int
    {
        $articleCount = Article::count();

        return $articleCount;
    }

    /**
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function getCommentCount(): int
    {
        return Comment::count();
    }

    public function getAuthorCount(): int
    {
        return User::count();
    }
}
