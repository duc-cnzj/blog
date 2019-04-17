<?php

namespace App\Providers;

use App\TopArticle;
use App\Services\ArticleRepo;
use App\Services\RegularRule;
use App\Contracts\TransformImp;
use App\Contracts\TopArticleImp;
use App\Contracts\ArticleRepoImp;
use App\Services\ArticleRepoCache;
use App\Services\ArticleTransformRule;
use Illuminate\Support\ServiceProvider;

/**
 * Class ArticleServiceProvider
 * @package App\Providers
 */
class ArticleServiceProvider extends ServiceProvider
{
    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function boot()
    {
        $this->app->singleton(TransformImp::class, function ($app, $params) {
            return new RegularRule(
                new ArticleTransformRule($params[0])
            );
        });

        $this->app->alias(TransformImp::class, 'rule');

        $this->app->singleton(
            TopArticleImp::class,
            function () {
                return new TopArticle();
            }
        );

        $this->app->singleton(
            ArticleRepoImp::class,
            function () {
                $useCache = config('duc.article_use_cache');

                if ($useCache) {
                    return new ArticleRepoCache(
                        new ArticleRepo
                    );
                } else {
                    return new ArticleRepo;
                }
            }
        );
    }
}
