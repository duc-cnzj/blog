<?php

namespace App\Services;

use App\Traits\hasCache;
use App\Contracts\WhiteListIpImp;

/**
 * Class WhiteListService
 * @package App\Services
 */
class WhiteListIpService implements WhiteListIpImp
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
