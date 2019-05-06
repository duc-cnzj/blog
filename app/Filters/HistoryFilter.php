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
    protected $filters = [
        'ip',
        'method',
        'status_code',
        'address',
        'url',
        //        'content',
        'response',
        'visit_time_after',
        'visit_time_before',
        'user_id',
        'user_type',
        'only_see',
    ];

    /**
     * @param string $ip
     *
     * @author duc <1025434218@qq.com>
     */
    public function ip(string $ip)
    {
        $this->builder->where('ip', 'LIKE', "%{$ip}%");
    }

    /**
     * @param string $method
     *
     * @author duc <1025434218@qq.com>
     */
    public function method(string $method)
    {
        $this->builder->where('method', 'LIKE', "%{$method}%");
    }

    /**
     * @param string $url
     *
     * @author duc <1025434218@qq.com>
     */
    public function url(string $url)
    {
        $this->builder->where('url', 'LIKE', "%{$url}%");
    }

    /**
     * @param int $code
     *
     * @author duc <1025434218@qq.com>
     */
    public function statusCode(int $code)
    {
        $this->builder->where('status_code', $code);
    }

    /**
     * @param string $address
     *
     * @author duc <1025434218@qq.com>
     */
    public function address(string $address)
    {
        $this->builder->where('address', 'LIKE', "%{$address}%");
    }

//    /**
//     * @param string $value
//     *
//     * @author duc <1025434218@qq.com>
//     */
//    public function content(string $value)
//    {
//        $this->builder->whereJsonContains('content', "%{$value}%");
//    }

    /**
     * @param string $value
     *
     * @author duc <1025434218@qq.com>
     */
    public function response(string $value)
    {
        $this->builder->where('response', 'LIKE', "%{$value}%");
    }

    /**
     * @param string $date
     *
     * @author duc <1025434218@qq.com>
     */
    public function visitTimeAfter(string $date)
    {
        try {
            $carbon = Carbon::parse($date);

            $this->builder->where('visited_at', '>', $carbon);
        } catch (\Exception $e) {
        }
    }

    /**
     * @param string $date
     *
     * @author duc <1025434218@qq.com>
     */
    public function visitTimeBefore(string $date)
    {
        try {
            $carbon = Carbon::parse($date);

            $this->builder->where('visited_at', '<', $carbon);
        } catch (\Exception $e) {
        }
    }

    /**
     * @param int $id
     *
     * @author duc <1025434218@qq.com>
     */
    public function userId(int $id)
    {
        $this->builder->where('userable_id', $id);
    }

    /**
     * @param string $type
     *
     * @author duc <1025434218@qq.com>
     */
    public function userType(string $type)
    {
        if (in_array($type, ['admin', 'frontend'])) {
            $realType = ($type == 'admin')
                ? 'App\User'
                : 'App\SocialiteUser';

            $this->builder->where('userable_type', $realType);
        }
    }

    /**
     * @param string $type
     *
     * @author duc <1025434218@qq.com>
     */
    public function onlySee(string $type)
    {
        $this->builder->onlySee($type);
    }
}
