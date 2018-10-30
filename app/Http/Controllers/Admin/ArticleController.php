<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use App\Article;
use App\Category;
use Emojione\Client;
use Emojione\Ruleset;
use Illuminate\Http\Request;
use App\Contracts\ArticleRepoImp;
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
            ->where('author_id', \Auth::id())
            ->latest()
            ->select('id', 'title', 'created_at', 'updated_at', 'category_id')
            ->paginate($request->page_size ?? 10);

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
        list($processContent, $category, $tagIds) = $this->dealRequest($request);

        /** @var Article $article */
        $article = Article::create([
            'author_id'   => \Auth::id(),
            'head_image'  => $request->head_image,
            'title'       => $request->title,
            'desc'        => $request->desc,
            'content'     => $processContent,
            'category_id' => $category->id,
        ]);

        $article->tags()->sync($tagIds);

        return new ArticleResource($article);
    }

    /**
     * @param int            $id
     * @param ArticleRepoImp $repo
     *
     * @return ArticleResource
     * @author duc <1025434218@qq.com>
     */
    public function show(int $id, ArticleRepoImp $repo)
    {
        $article = $repo->get($id);

        return new ArticleResource($article);
    }

    /**
     * @param int            $id
     * @param Request        $request
     * @param ArticleRepoImp $repo
     *
     * @return ArticleResource
     * @author duc <1025434218@qq.com>
     */
    public function update(int $id, Request $request, ArticleRepoImp $repo)
    {
        list($processContent, $category, $tagIds) = $this->dealRequest($request);

        /** @var Article $article */
        $article = Article::findOrFail($id);

        $article->update([
            'author_id'   => \Auth::id(),
            'head_image'  => $request->head_image,
            'title'       => $request->title,
            'desc'        => $request->desc,
            'content'     => $processContent,
            'category_id' => $category->id,
        ]);

        $article->tags()->sync($tagIds);

        $repo->removeBy($id);

        return new ArticleResource($article);
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\Response|\Laravel\Lumen\Http\ResponseFactory
     * @author duc <1025434218@qq.com>
     */
    public function destroy(int $id)
    {
        Article::findOrFail($id)->delete();

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
        $content = $request->input('content');
        $client = new Client(new Ruleset());
        $client->ascii = true;
        $content = $client->shortnameToImage($content);

        $parsedown = new \Parsedown();

        $mdContent = $parsedown->text($content);

        $processContent = json_encode(
            [
                'html' => $mdContent,
                'md'   => $content,
            ]
        );

        $category = Category::firstOrCreate(
            [
                'name' => $request->category, // string 'php'
            ],
            [
            'user_id' => \Auth::id(),
        ]);

        $tagNames = $request->tags; // array ['php', 'js']
        $tagIds = $this->getTagIdsBy($tagNames);

        return [$processContent, $category, $tagIds];
    }
}
