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
     * @param int $id
     *
     * @author duc <1025434218@qq.com>
     */
    public function id(int $id)
    {
        $this->builder->where('id', $id);
    }

    /**
     * @param string $name
     *
     * @author duc <1025434218@qq.com>
     */
    public function name(string $name)
    {
        $this->builder->where('name', 'LIKE', "%{$name}%");
    }

    /**
     * @param string $type
     *
     * @author duc <1025434218@qq.com>
     */
    public function identityType(string $type)
    {
        $this->builder->where('identity_type', $type);
    }
}
