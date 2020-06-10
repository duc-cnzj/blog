<?php

namespace App\Services\Decorators;

use App\Services\ArticleRepoCache;
use Illuminate\Support\Facades\Cache;

class ArticleRepoCacheInGolang extends ArticleRepoCache
{
    public function removeBy(int $id)
    {
        parent::removeBy($id);

        $goCachePrefix = config('duc.go_cache_prefix', 'go_');
        Cache::forget($goCachePrefix . $this->cacheKey($id));
    }
}
