<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * Interface Filter
 * @package App\Contracts
 */
interface Filter
{
    /**
     * @param Builder $builder
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function apply(Builder $builder);
}

