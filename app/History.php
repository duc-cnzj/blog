<?php

namespace App;

use App\Traits\HasFilter;
use Illuminate\Database\Eloquent\Model;

/**
 *
 * Class History
 * @package App
 */
class History extends Model
{
    use HasFilter;

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
