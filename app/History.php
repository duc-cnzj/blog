<?php

namespace App;

use App\Traits\HasFilter;
use App\Contracts\WhiteListIpImp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 * Class History
 * @package App
 *
 * @method static Builder|History onlySee(string $from)
 * @method static Builder|History removeWhiteListIps()
 */
class History extends Model
{
    use HasFilter;

    /**
     * @var array
     */
    private $onlySeePathsOfAdmin = ['/auth', '/admin'];

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
     * @var array
     */
    protected $dates = [
        'visited_at',
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

    /**
     * @param  Builder  $query
     * @param string $from
     * @return Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function scopeOnlySee(Builder $query, string $from)
    {
        if (! in_array($from, ['admin', 'frontend'])) {
            return $query;
        }

        $operator = ($from == 'admin')
                ? 'LIKE'
                : 'NOT LIKE';

        return $query->where(function ($q) use ($operator) {
            foreach ($this->onlySeePathsOfAdmin as $index => $path) {
                $path = '/' . ltrim($path, '/');

                $method = ($operator != 'LIKE' || $index == 0)
                        ? 'where'
                        : 'orWhere';
                $q->{$method}('url', $operator, "{$path}%");
            }
        });
    }

    /**
     * @param  Builder  $query
     * @return Builder|\Illuminate\Database\Query\Builder
     *
     * @author duc <1025434218@qq.com>
     */
    public function scopeRemoveWhiteListIps(Builder $query)
    {
        /** @var WhiteListIpImp $whiteList */
        $whiteList = app(WhiteListIpImp::class);

        return $query->whereNotIn('ip', $whiteList->getTreatedListItems());
    }
}
