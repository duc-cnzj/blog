<?php

namespace App\Services;

use App\Contracts\WhiteListImp;
use Illuminate\Support\Facades\Cache;

/**
 * Class WhiteListService
 * @package App\Services
 */
class WhiteListService implements WhiteListImp
{
    /**
     * @var string
     */
    protected $cacheKey = 'white_list';

    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function getCacheKey():string
    {
        return app()->environment('testing') ? 'testing_' . $this->cacheKey : $this->cacheKey;
    }

    /**
     * @param  string[]  $items
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function addItemToList(string ...$items): bool
    {
        $lists = Cache::get($this->getCacheKey(), []);
        array_push($lists, ...$items);
        $lists = array_unique($lists);

        return Cache::forever($this->getCacheKey(), $lists);
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getItemLists():array
    {
        return Cache::get($this->getCacheKey(), []);
    }

    /**
     * @param  string[]  $items
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function deleteItems(string ...$items): bool
    {
        $lists = Cache::get($this->getCacheKey(), []);

        return Cache::forever($this->getCacheKey(), array_diff($lists, array_values($items)));
    }
}
