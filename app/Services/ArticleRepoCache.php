<?php

namespace App\Services;

use App\User;
use App\Contracts\ArticleRepoImp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class ArticleRepoCache
 * @package App\Services
 */
class ArticleRepoCache implements ArticleRepoImp
{
    /**
     * @var ArticleRepoImp
     */
    protected $article;

    /**
     * ArticleRepoCache constructor.
     *
     * @param ArticleRepoImp $repo
     */
    public function __construct(ArticleRepoImp $repo)
    {
        $this->article = $repo;
    }

    /**
     * @param int $id
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function cacheKey(int $id)
    {
        return app()->environment('testing') ? 'testing_article:' . $id : 'article:' . $id;
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function get(int $id)
    {
        return Cache::rememberForever($this->cacheKey($id), function () use ($id) {
            return $this->article->get($id);
        });
    }

    /**
     * @param int $id
     *
     * @author duc <1025434218@qq.com>
     * @return mixed|void
     */
    public function removeBy(int $id)
    {
        Cache::forget($this->cacheKey($id));
    }

    /**
     * 为什么会考虑这样取文章呢
     * 这个方法被用在获取最火的文章上面
     * 那么既然最火，所以肯定被读过了对吧
     * 被读过了的话这篇文章肯定就在缓存里面了
     * 所以直接从缓存里面那，就不用去查询数据库
     *
     * @param array $ids
     *
     * @return Collection
     *
     * @author duc <1025434218@qq.com>
     */
    public function getMany(array $ids): Collection
    {
        $arr = [];
        if ($ids) {
            foreach ($ids as $id) {
                $arr[] = $this->get($id);
            }
        }

        return collect($arr);
    }

    /**
     * @param User $user
     *
     * @author duc <1025434218@qq.com>
     */
    public function resetArticleCacheByUser(User $user)
    {
        $articleIds = $user->articles()->pluck('id');

        foreach ($articleIds as $id) {
            $this->removeBy($id);
        }
    }

    /**
     * @param int $id
     * @return bool
     *
     * @author duc <1025434218@qq.com>
     */
    public function hasArticleCacheById(int $id): bool
    {
        return Cache::has($this->cacheKey($id));
    }
}
