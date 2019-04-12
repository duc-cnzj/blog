<?php

namespace App\Traits;

use App\History;

/**
 * Trait HasHistory
 * @package App\Traits
 */
trait HasHistory
{
    /**
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function histories()
    {
        return $this->morphMany(History::class, 'userable');
    }
}
