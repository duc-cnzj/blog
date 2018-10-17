<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author duc <1025434218@qq.com>
     */
    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @author duc <1025434218@qq.com>
     */
    public function reply()
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }
}
