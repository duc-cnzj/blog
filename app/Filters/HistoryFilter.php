<?php

namespace App\Filters;

use Carbon\Carbon;

/**
 * Class HistoryFilter
 * @package App\Filters
 */
class HistoryFilter extends Filters
{
    /**
     * @var string
     */
    protected $prefix = 'history';

    /**
     * @var array
     */
    private $onlySeePathsOfAdmin = ['/auth', '/admin'];

    /**
     * @var array
     */
    protected $filters = [
        'ip',
        'method',
        'status_code',
        'address',
//        'content',
        'response',
        'visit_time_after',
        'visit_time_before',
        'user_id',
        'user_type',
        'only_see',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function ip()
    {
        return $this->builder->where('ip', 'LIKE', "%{$this->getValueBy(__FUNCTION__)}%");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function method()
    {
        return $this->builder->where('method', 'LIKE', "%{$this->getValueBy(__FUNCTION__)}%");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function statusCode()
    {
        return $this->builder->where('status_code', (int) $this->getValueBy(__FUNCTION__));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function address()
    {
        return $this->builder->where('address', 'LIKE', "%{$this->getValueBy(__FUNCTION__)}%");
    }

//    /**
//     * @return \Illuminate\Database\Eloquent\Builder
//     *
//     * @author duc <1025434218@qq.com>
//     */
//    public function content()
//    {
//        return $this->builder->whereJsonContains('content', "%{$this->getValueBy(__FUNCTION__)}%");
//    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function response()
    {
        return $this->builder->where('response', 'LIKE', "%{$this->getValueBy(__FUNCTION__)}%");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function visitTimeAfter()
    {
        try {
            $carbon = Carbon::parse($this->getValueBy(__FUNCTION__));

            return $this->builder->where('visited_at', '>', $carbon);
        } catch (\Exception $e) {
            return $this->builder;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function visitTimeBefore()
    {
        try {
            $carbon = Carbon::parse($this->getValueBy(__FUNCTION__));

            return $this->builder->where('visited_at', '<', $carbon);
        } catch (\Exception $e) {
            return $this->builder;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function userId()
    {
        return $this->builder->where('userable_id', (int) $this->getValueBy(__FUNCTION__));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function userType()
    {
        $type = $this->getValueBy(__FUNCTION__);

        if (in_array($type, ['admin', 'frontend'])) {
            $realType = ($type == 'admin')
                ? 'App\User'
                : 'App\SocialiteUser';

            return $this->builder->where('userable_type', $realType);
        } else {
            return $this->builder;
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function onlySee()
    {
        $type = $this->getValueBy(__FUNCTION__);

        if (in_array($type, ['admin', 'frontend'])) {
            $operator = ($type == 'admin')
                ? 'LIKE'
                : 'NOT LIKE';

            foreach ($this->onlySeePathsOfAdmin as $index => $path) {
                $path = '/' . ltrim($path, '/');

                $method = ($operator != 'LIKE' || $index == 0)
                    ? 'where'
                    : 'orWhere';

                $this->builder->{$method}('url', $operator, "{$path}%");
            }

            return $this->builder;
        } else {
            return $this->builder;
        }
    }
}
