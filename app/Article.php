<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = [];
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

    public function getContentAttribute($value)
    {
        $arr = json_decode($value);

        return $arr->html;
    }

    public function getContentHtmlAttribute($value)
    {
        $arr = json_decode($this->content);

        return $arr->md;
    }
}
