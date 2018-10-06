<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $appends = ['recommend_articles'];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getRecommendArticlesAttribute($value='')
    {
        return [
            [
              "id" =>  3,
              "category" =>  "Linux",
              "title" =>  "1"
            ],
    ];
    }
}
