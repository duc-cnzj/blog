<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use App\Article;
use App\Category;
use Laravel\Scout\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;

/**
 * Class ArticleController
 * @package App\Http\Controllers\Admin
 */
class ArticleController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     *
     * @author duc <1025434218@qq.com>
     */
    public function index(Request $request)
    {
        $articles = Article::with('category', 'tags')
            ->whole(! ! $request->input('all'))
            ->latest()
            ->select('id', 'title', 'created_at', 'updated_at', 'category_id', 'display', 'top_at')
            ->paginate($request->input('page_size') ?? 10);

        return ArticleResource::collection($articles);
    }

    /**
     * @param Request $request
     * @return ArticleResource
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'head_image'  => 'required|string',
            'title'       => 'required|string',
            'desc'        => 'required|string|max:100',
            'content'     => 'required|string',
            'category'    => 'required|string',
            'tags'        => 'required|array',
            'display'     => 'required|boolean',
        ]);

        [$category, $tagIds] = $this->dealRequest($request);

        /** @var Article $article */
        $article = Article::query()->create([
            'author_id'   => \Auth::id(),
            'head_image'  => $request->input('head_image'),
            'title'       => $request->input('title'),
            'desc'        => $request->input('desc'),
            'content'     => $request->input('content'),
            'display'     => $request->input('display'),
            'category_id' => $category->id,
        ]);

        $article->tags()->sync($tagIds);

        return new ArticleResource($article);
    }

    /**
     * @param int            $id
     *
     * @return ArticleResource
     * @author duc <1025434218@qq.com>
     */
    public function show(int $id)
    {
        $article = Article::with('category', 'tags', 'author')->findOrFail($id);

        return new ArticleResource($article);
    }

    /**
     * @param int $id
     * @param Request $request
     * @return ArticleResource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function update(int $id, Request $request)
    {
        $this->validate($request, [
            'head_image'  => 'required|string',
            'title'       => 'required|string',
            'desc'        => 'required|string|max:100',
            'content'     => 'required|string',
            'category'    => 'required|string',
            'tags'        => 'required|array',
        ]);

        /** @var Article $article */
        $article = Article::query()->findOrFail($id);

        $this->authorize('own', $article);

        DB::transaction(function () use ($article, $request) {
            [$category, $tagIds] = $this->dealRequest($request);
            $article->update([
                'head_image'  => $request->input('head_image'),
                'title'       => $request->input('title'),
                'desc'        => $request->input('desc'),
                'content'     => $request->input('content'),
                'category_id' => $category->id,
            ]);

            $article->tags()->sync($tagIds);
        });

        return new ArticleResource($article);
    }

    /**
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(int $id)
    {
        $article = Article::query()->findOrFail($id);

        $this->authorize('own', $article);

        $article->delete();

        return response('', 204);
    }

    /**
     * @param array $names
     *
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getTagIdsBy(array $names): array
    {
        $ids = [];

        foreach ($names as $name) {
            $tag = Tag::query()->firstOrCreate([
                'name' => $name,
            ], [
                'user_id' => \Auth::id(),
            ]);

            $ids[] = $tag->id;
        }

        return $ids;
    }

    /**
     * @param Request $request
     *
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    private function dealRequest(Request $request): array
    {
        $category = Category::query()->firstOrCreate(
            [
                'name' => $request->input('category'), // string 'php'
            ],
            [
                'user_id' => \Auth::id(),
            ]
        );

        $tagNames = $request->input('tags'); // array ['php', 'js']
        $tagIds = $this->getTagIdsBy($tagNames);

        return [$category, $tagIds];
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
            $articles = Article::search($query)
                ->rule(\App\ES\ArticleRule::class);
            $articles->limit = 10000;
            $articles = $articles->select(['id', 'author_id', 'category_id', 'desc', 'title', 'head_image', 'created_at', 'display', 'top_at'])
                ->when(! ! $request->input('all'), function ($q) {
                    return $q;
                }, function (Builder $q) {
                    return $q->where('author.id', \Auth::id());
                })
                ->get()
                ->load('author', 'tags', 'category');

            return ArticleResource::collection($articles);
        } else {
            return ArticleResource::collection([]);
        }
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function changeDisplay(int $id)
    {
        $article = Article::query()->findOrFail($id);

        $this->authorize('own', $article);

        $article->update(['display' => ! $article->display]);

        return response('', 204);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function setTop(int $id)
    {
        $article = Article::query()->findOrFail($id);

        $this->authorize('own', $article);

        /** @var Article $article */
        $article->setTop();

        return response('', 204);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @author duc <1025434218@qq.com>
     */
    public function cancelSetTop(int $id)
    {
        $article = Article::query()->findOrFail($id);

        $this->authorize('own', $article);

        /** @var Article $article */
        $article->cancelSetTop();

        return response('', 204);
    }
}
