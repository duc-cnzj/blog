<?php

namespace App\Filters;

/**
 * Class SocialiteUserFilter
 * @package App\Filters
 */
class SocialiteUserFilter extends Filters
{
    /**
     * @var string
     */
    protected $prefix = 'socialite_user';

    /**
     * @var array
     */
    protected $filters = [
        'name', 'identity_type', 'id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function id()
    {
        return $this->builder->where('id', (int) $this->getValueBy(__FUNCTION__));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function name()
    {
        return $this->builder->where('name', 'LIKE', "%{$this->getValueBy(__FUNCTION__)}%");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function identityType()
    {
        return $this->builder->where('identity_type', $this->getValueBy(__FUNCTION__));
    }
}
