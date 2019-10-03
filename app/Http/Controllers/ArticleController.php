<?php

namespace App\Http\Controllers;

use App\Article;
use App\Trending;
use App\ES\ArticleRule;
use Illuminate\Http\Request;
use App\Contracts\TopArticleImp;
use App\Contracts\ArticleRepoImp;
use App\Http\Resources\ArticleResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ArticleController
 * @package App\Http\Controllers
 */
class ArticleController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index()
    {
        return ArticleResource::collection(
            Article::with('author')
                ->visible()
                ->latest()
                ->select(['id', 'category_id', 'head_image', 'title', 'desc', 'created_at', 'author_id'])
                ->paginate()
        );
    }

    /**
     * @param int $id
     * @param Trending $trending
     * @param ArticleRepoImp $repo
     * @return ArticleResource
     *
     * @author duc <1025434218@qq.com>
     */
    public function show(int $id, Trending $trending, ArticleRepoImp $repo)
    {
        /** @var Article $article */
        $article = $repo->get($id);

        if (in_array($article->id, $trending->getInvisibleIds())) {
            throw new ModelNotFoundException();
        }

        $trending->push($article);

        return new ArticleResource($article);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function search(Request $request)
    {
        $query = $request->query('q');

        if (! is_null($query)) {
            $q = Article::search($query)
                ->rule(ArticleRule::class);
            $q->limit = 10000;
            $articles = $q->select(['id', 'author_id', 'category_id', 'desc', 'title', 'head_image', 'created_at', 'display'])
                ->get()
                ->load('author', 'tags', 'category')
                ->where('display', true);

            return ArticleResource::collection($articles);
        } else {
            return ArticleResource::collection([]);
        }
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function home()
    {
        $articles = Article::with('category:id,name')
            ->visible()
            ->latest()
            ->take(3)
            ->get(['id', 'category_id', 'head_image', 'title', 'created_at']);

        return ArticleResource::collection($articles);
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function newest()
    {
        return ArticleResource::collection(Article::with('author', 'category:id,name')
            ->visible()
            ->latest()
            ->take(13)
            ->get(['id', 'category_id', 'head_image', 'title', 'created_at', 'author_id']));
    }

    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function popular()
    {
        return ArticleResource::collection(Article::with('author', 'category:id,name')
            ->visible()
            ->inRandomOrder()
            ->take(8)
            ->get(['id', 'category_id', 'head_image', 'title', 'created_at', 'author_id']));
    }

    /**
     * @param Trending       $trending
     * @param ArticleRepoImp $repo
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function trending(Trending $trending, ArticleRepoImp $repo)
    {
        $articleIds = $trending->get();
        $articles = $repo->getMany($articleIds);

        return ArticleResource::collection(collect($articles)->map->removeAttribute('content'));
    }

    /**
     * @param TopArticleImp  $imp
     * @param ArticleRepoImp $repo
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function top(TopArticleImp $imp, ArticleRepoImp $repo)
    {
        $articleIds = $imp->getTopArticles();

        $articles = $repo->getMany($articleIds);

        return ArticleResource::collection(collect($articles)->map->removeAttribute('content'));
    }
}
