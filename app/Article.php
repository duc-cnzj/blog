<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function getRecommendArticles()
    {
        return static::where('category_id', $this->category_id)->inRandomOrder()->take(3)->get(['id', 'title'])->toArray();
    }

    public function getContentHtmlAttribute()
    {
        $arr = json_decode($this->content);

        if (is_object($arr)) {
            return $arr->html;
        } else {
            return null;
        }

    }

    public function getContentMdAttribute()
    {
        $arr = json_decode($this->content);

        if (is_object($arr)) {
            return $arr->md;
        } else {
            return null;
        }
    }
}
