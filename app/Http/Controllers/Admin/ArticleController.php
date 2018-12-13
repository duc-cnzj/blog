<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use App\Article;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;

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
            ->select('id', 'title', 'created_at', 'updated_at', 'category_id', 'display')
            ->paginate($request->input('page_size') ?? 10);

        return ArticleResource::collection($articles);
    }

    /**
     * @param Request $request
     *
     * @return ArticleResource
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

        list($category, $tagIds) = $this->dealRequest($request);

        /** @var Article $article */
        $article = Article::create([
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
     * @param int            $id
     * @param Request        $request
     *
     * @return ArticleResource
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

        list($category, $tagIds) = $this->dealRequest($request);

        /** @var Article $article */
        $article = Article::findOrFail($id);

        if (! \Auth::user()->isAdmin() && $article->author_id !== \Auth::id()) {
            abort(403, '你没有权限修改此文章！');
        }

        $article->update([
            'head_image'  => $request->input('head_image'),
            'title'       => $request->input('title'),
            'desc'        => $request->input('desc'),
            'content'     => $request->input('content'),
            'category_id' => $category->id,
        ]);

        $article->tags()->sync($tagIds);

        return new ArticleResource($article);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     *
     * @author duc <1025434218@qq.com>
     */
    public function destroy(int $id)
    {
        $article = Article::findOrFail($id);

        if ($article->author_id !== \Auth::id() && ! \Auth::user()->isAdmin()) {
            return $this->fail('这篇文章不是你的，不能删除！', 403);
        }

        $article->delete();

        return response([], 204);
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
            $tag = Tag::firstOrCreate([
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
        $category = Category::firstOrCreate(
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

    public function search(Request $request)
    {
        $query = $request->query('q');

        if (! is_null($query)) {
            $q = Article::search($query)
                ->rule(\App\ES\ArticleRule::class);
            $q->limit = 10000;
            $articles = $q->select(['id', 'author_id', 'category_id', 'desc', 'title', 'head_image', 'created_at'])
                ->when(! ! $request->input('all'), function ($q) {
                    info('amdin search all');

                    return $q;
                }, function ($q) {
                    info('amdin search not all');

                    return $q->where('author.id', \Auth::id());
                })
                ->get()
                ->load('author', 'tags', 'category');

            return ArticleResource::collection($articles);
        } else {
            return ArticleResource::collection([]);
        }
    }

    public function changeDisplay(int $id)
    {
        $article = Article::findOrFail($id);

        if ($article->author_id !== \Auth::id() && ! \Auth::user()->isAdmin()) {
            return $this->fail('这篇文章不是你的，不能修改！', 403);
        }

        $article->update(['display' => ! $article->display]);

        return response([], 204);
    }
}
