<?php

namespace App\Traits;

use App\Contracts\ArticleRepoImp;

trait ArticleCacheTrait
{
    public static function bootArticleCacheTrait()
    {
//        static::created(function ($model) {
//            app(ArticleRepoImp::class)->get($model->id);
//        });

        static::updated(function ($model) {
            app(ArticleRepoImp::class)->removeBy($model->id);
//            app(ArticleRepoImp::class)->get($model->id);
        });

        static::deleted(function ($model) {
            app(ArticleRepoImp::class)->removeBy($model->id);
        });
    }
}
