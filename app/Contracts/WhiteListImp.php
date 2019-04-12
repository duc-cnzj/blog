<?php

namespace App\Contracts;

/**
 * Interface WhiteListImp
 * @package App\Contracts
 */
interface WhiteListImp
{
    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function getCacheKey(): string;

    /**
     * @param  string[]  $items
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function addItemToList(string ...$items): bool;

    /**
     * @param  string[]  $items
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function deleteItems(string ...$items): bool;

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getItemLists(): array;
}
