<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Contracts\TopArticleImp;

trait TopArticleTrait
{
    public static function bootTopArticleTrait()
    {
        static::created(function ($model) {
            if ($model->top_at !== null) {
                app(TopArticleImp::class)->addTopArticle($model);
            }
        });

        static::updated(function ($model) {
            if ($model->isDirty('top_at')) {
                $model->getDirty()['top_at'] !== null
                    ? app(TopArticleImp::class)->addTopArticle($model)
                    : app(TopArticleImp::class)->removeTopArticle($model);
            }
        });

        static::deleted(function ($model) {
            app(TopArticleImp::class)->removeTopArticle($model);
        });
    }

    public function setTop()
    {
        $this->update(['top_at' => Carbon::now()]);
    }

    public function cancelSetTop()
    {
        $this->update(['top_at' => null]);
    }

    public function getIsTopAttribute()
    {
        return $this->top_at !== null;
    }
}
