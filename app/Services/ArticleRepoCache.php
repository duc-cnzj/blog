<?php

namespace App\Services;

use App\Contracts\ArticleRepoImp;
use Illuminate\Support\Facades\Cache;

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
     * @param $id
     *
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    public function cacheKey($id)
    {
        return 'article:' . $id;
    }

    /**
     * @param int $id
     *
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function get($id)
    {
        return Cache::rememberForever($this->cacheKey($id), function () use ($id) {
            info('从数据库获取文章id: ' . $id);

            return $this->article->get($id);
        });
    }

    /**
     * @param $id
     *
     * @author duc <1025434218@qq.com>
     * @return mixed|void
     */
    public function removeBy($id)
    {
        info('移除缓存文章id: ' . $id);
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
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getMany(array $ids)
    {
        $arr = [];
        if ($ids) {
            foreach ($ids as $id) {
                $arr[] = $this->get($id);
            }
        }

        return $arr;
    }
}
