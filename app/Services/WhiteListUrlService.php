<?php

namespace App\Services;

use App\Traits\hasCache;
use App\Contracts\WhiteListUrlImp;

/**
 * Class WhiteListService
 * @package App\Services
 */
class WhiteListUrlService implements WhiteListUrlImp
{
    use hasCache;

    /**
     * @var array
     */
    protected $defaultWhiteLists = ['admin/histories*'];

    /**
     * @var string
     */
    protected $cacheKey = 'url_white_list';

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getTreatedListItems(): array
    {
        $whiteList = array_merge($this->defaultWhiteLists, $this->getItemLists());

        return array_map(function ($except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            return $except;
        }, $whiteList);
    }
}
