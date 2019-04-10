<?php

namespace App\Traits;

use App\Contracts\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Trait HasFilter
 *
 * @method static Builder filter($filter)
 *
 * @package App\Traits
 */
trait HasFilter
{
    public function scopeFilter($query, Filter $filters)
    {
        return $filters->apply($query);
    }
}

