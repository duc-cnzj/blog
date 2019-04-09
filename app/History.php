<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * Class History
 * @package App
 */
class History extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'content' => 'array',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     *
     * @author duc <1025434218@qq.com>
     */
    public function userable()
    {
        return $this->morphTo();
    }
}
