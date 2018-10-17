<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
