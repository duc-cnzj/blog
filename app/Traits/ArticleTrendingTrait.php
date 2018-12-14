<?php

namespace App\Traits;

use App\Trending;

trait ArticleTrendingTrait
{
    public static function bootArticleTrendingTrait()
    {
        static::created(function ($model) {
            $model->display
                ? app(Trending::class)->removeInvisible($model->id)
                : app(Trending::class)->addInvisible($model->id);
        });

        static::updated(function ($model) {
            if ($model->isDirty('display')) {
                $model->getDirty()['display']
                    ? app(Trending::class)->removeInvisible($model->id)
                    : app(Trending::class)->addInvisible($model->id);
            }
        });

        static::deleted(function ($model) {
            app(Trending::class)->remove($model->id);
        });
    }
}
