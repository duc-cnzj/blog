<?php

namespace App\Traits;

use App\Contracts\ArticleRepoImp;

trait ArticleCacheTrait
{
    public static function bootArticleCacheTrait()
    {
        static::updated(function ($model) {
            app(ArticleRepoImp::class)->removeBy($model->id);
        });

        static::deleted(function ($model) {
            app(ArticleRepoImp::class)->removeBy($model->id);
        });
    }
}
