<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function reply()
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }
}
