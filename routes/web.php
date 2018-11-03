<?php

use App\Article;
use App\Comment;
use App\Category;
use App\Trending;
use Illuminate\Http\Request;
use App\Contracts\ArticleRepoImp;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CategoryResource;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

require_once __DIR__ . '/admin.php';

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/articles/{id}', function ($id, Trending $trending, ArticleRepoImp $repo) {
    $article = $repo->get($id);
    $trending->push($article);

    return new ArticleResource($article);
});

$router->get('/articles', function () {
    return ArticleResource::collection(
        Article::with('author')
            ->latest()
            ->select(['id', 'category_id', 'head_image', 'title', 'desc', 'created_at', 'author_id'])
            ->paginate()
    );
});

$router->get('/search_articles', function (Request $request) {
    $query = $request->query('q');
    if (! is_null($query)) {
        $articles = Article::search($query)
            ->rule(\App\ES\ArticleRule::class)
            ->get()
            ->load('author', 'tags', 'category');

        return ArticleResource::collection($articles);
    }
});

$router->get('/home_articles', function () {
    $articles = Article::with('category:id,name')
        ->latest()
        ->take(3)
        ->get(['id', 'category_id', 'head_image', 'title', 'created_at']);

    return ArticleResource::collection($articles);
});

$router->get('/newest_articles', function () {
    return ArticleResource::collection(Article::with('author', 'category:id,name')
        ->latest()
        ->take(13)
        ->get(['id', 'category_id', 'head_image', 'title', 'created_at', 'author_id']));
});

$router->get('/popular_articles', function () {
    return ArticleResource::collection(Article::with('author', 'category:id,name')
        ->inRandomOrder()
        ->take(8)
        ->get(['id', 'category_id', 'head_image', 'title', 'created_at', 'author_id']));
});

$router->get('/trending_articles', function (Trending $trending, ArticleRepoImp $repo) {
    $articleIds = $trending->get();
    $articles = $repo->getMany($articleIds);

    return ArticleResource::collection(collect($articles));
});

$router->get('/categories', function () {
    return CategoryResource::collection(Category::all(['name', 'id']));
});

$router->get('/nav_links', function () {
    return [
        'data' => [
            ['title' => '首页', 'link' => '/'],
            // ['title' => '分类', 'link' => '/categories'],
            ['title' => '文章', 'link' => '/articles'],
        ],
    ];
});

$router->get('/articles/{id}/comments', function ($id, Request $request) {
    $comments = Comment::where('article_id', $id)
        ->get(['visitor', 'content', 'comment_id', 'created_at', 'id']);

    return response([
        'data' => array_reverse(
            c(CommentResource::collection($comments)->toArray($request))
        ),
    ], 200);
});

$router->post('/articles/{id}/comments', function ($id, Request $request) {
    $article = Article::findOrFail($id);
    $content = $request->input('content');
    $parsedown = new \Parsedown();
    $htmlContent = $parsedown->text($content);

    $comment = $article->comments()->create([
        'visitor'    => $request->ip(),
        'content'    => $htmlContent,
        'comment_id' => $request->comment_id ?? 0,
    ]);

    return (new CommentResource($comment))
        ->additional([
            'data' => [
                'replies' => [],
            ],
        ]);
});
