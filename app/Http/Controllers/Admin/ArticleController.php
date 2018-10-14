<?php

namespace App\Http\Controllers\Admin;

use App\Tag;
use App\Article;
use App\Category;
use Illuminate\Http\Request;
use App\Contracts\ArticleRepoImp;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('category', 'tags')
            ->where('author_id', \Auth::id())
            ->latest()
            ->select('id', 'title', 'created_at', 'updated_at', 'category_id')
            ->paginate($request->page_size ?? 10);

        return ArticleResource::collection($articles);
    }

    public function store(Request $request)
    {
        $content = $request->content;
        $parsedown = new \Parsedown();

        $mdContent = $parsedown->text($content);

        $processContent = json_encode([
            'html' => $mdContent,
            'md' => $content
        ]);


        $category = Category::firstOrCreate([
            'name' => $request->category, // string 'php'
        ], [
           'user_id' => \Auth::id()
        ]);

        $tagNames = $request->tags; // array ['php', 'js']
        $tagIds = $this->getTagIdsBy($tagNames);

        /** @var Article $article */
        $article = Article::create([
            'author_id' => \Auth::id(),
            'head_image' => $request->head_image,
            'title' => $request->title,
            'desc' => $request->desc,
            'content' => $processContent,
            'category_id' => $category->id
        ]);

        $article->tags()->sync($tagIds);

        return new ArticleResource($article);
    }

    public function show($id, ArticleRepoImp $repo)
    {
        $article = $repo->get($id);

        return new ArticleResource($article);
    }

    public function update($id, Request $request, ArticleRepoImp $repo)
    {
        $content = $request->content;
        $parsedown = new \Parsedown();

        $mdContent = $parsedown->text($content);

        $processContent = json_encode([
            'html' => $mdContent,
            'md' => $content
        ]);
        $category = Category::firstOrCreate([
            'name' => $request->category, // string 'php'
        ], [
           'user_id' => \Auth::id()
        ]);

        $tagNames = $request->tags; // array ['php', 'js']
        $tagIds = $this->getTagIdsBy($tagNames);

        $article = Article::findOrFail($id);
        $article->update([
            'author_id' => \Auth::id(),
            'head_image' => $request->head_image,
            'title' => $request->title,
            'desc' => $request->desc,
            'content' => $processContent,
            'category_id' => $category->id
        ]);

        $article->tags()->sync($tagIds);

        $repo->removeBy($id);

        return new ArticleResource($article);
    }

    public function destroy($id)
    {
        Article::findOrFail($id)->delete();

        return response([], 204);
    }

    public function getTagIdsBy(array $names): array
    {
        $ids = [];
        foreach ($names as $name) {

            $tag = Tag::firstOrCreate([
                'name' => $name
            ], [
                'user_id' => \Auth::id()
            ]);

            $ids[] = $tag->id;
        }

        return $ids;
    }
}
