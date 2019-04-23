<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Article;
use App\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\HistoryDataService;
use Illuminate\Support\Facades\Redis;

/**
 * Class DashboardController
 * @package App\Http\Controllers\Admin
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @author duc <1025434218@qq.com>
     */
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
     * @param  Request  $request
     * @param  HistoryDataService  $service
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author 神符 <1025434218@qq.com>
     */
    public function historyData(Request $request, HistoryDataService $service)
    {
        $this->validate($request, [
            'unit'     => 'nullable|in:day,week',
            'section'  => 'nullable|int',
            'sub_week' => 'nullable|int',
            'from'     => 'in:frontend,admin',
        ]);

        $unit = $request->unit ?? 'day';
        $section = $request->section ?? 6;
        $subWeeks = $request->sub_week ?? 0;
        $from = $request->from ?? 'frontend';

        return response()->json([
            'data' => $service->getData($unit, $section, $subWeeks, $from),
        ], 200);
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
        $articleCount = Article::query()->count();

        return $articleCount;
    }

    /**
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function getCommentCount(): int
    {
        return Comment::query()->count();
    }

    /**
     * @return int
     *
     * @author duc <1025434218@qq.com>
     */
    public function getAuthorCount(): int
    {
        return User::query()->count();
    }
}
