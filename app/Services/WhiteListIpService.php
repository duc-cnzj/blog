<?php

namespace App\Services;

use App\Traits\hasCache;
use App\Contracts\WhiteListUrlImp;

/**
 * Class WhiteListService
 * @package App\Services
 */
class WhiteListIpService implements WhiteListUrlImp
{
    use hasCache;

    /**
     * @var string
     */
    protected $cacheKey = 'ip_white_list';

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getTreatedListItems(): array
    {
        return $this->getItemLists();
    }
}
