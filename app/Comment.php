<?php

namespace App;

use App\Events\CommentCreated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Broadcasting\Factory;

/**
 * Class Comment
 * @package App
 */
class Comment extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $with = ['userable'];

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            app(Factory::class)->event(
                new CommentCreated(
                    $model->load(['article:id', 'userable'])
                )
            )->toOthers();
        });
    }

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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     *
     * @author duc <1025434218@qq.com>
     */
    public function userable()
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     *
     * @author duc <1025434218@qq.com>
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'comment_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author duc <1025434218@qq.com>
     */
    public function parentReply()
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }
}
