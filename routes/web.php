<?php

use App\User;
use App\Article;
use App\Category;
use App\Trending;
use Illuminate\Http\Request;
use App\Contracts\ArticleRepoImp;
use App\Http\Resources\UserResource;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\CategoryResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

require_once __DIR__.'/admin.php';

$router->get('/', function (ArticleRepoImp $repo) use ($router) {
    return $router->app->version();
});

$router->get('/articles/{id}', function ($id, Trending $trending, ArticleRepoImp $repo) {
    $article = $repo->get($id);
    $trending->push($article);

    return new ArticleResource($article);
});

$router->get('/articles', function () {
    return ArticleResource::collection(Article::latest()->take(10)->get());
});

$router->get('/home_articles', function () {
    return ArticleResource::collection(Article::latest()->take(3)->get());
});

$router->get('/newest_articles', function () {
    return ArticleResource::collection(Article::with('author')->latest()->take(13)->get()->each->append('recommend_articles'));
});

$router->get('/trending_articles', function (Trending $trending) {
    return ['data' => $trending->get()];
});

$router->get('/categories', function () {
    return CategoryResource::collection(Category::all(['name', 'id']));
});

$router->get('/nav_links', function () {
    return [
        "data" => [
            ["title" => "首页", "link" => "/"],
            ["title" => "分类", "link" => "/categories"],
            ["title" => "文章", "link" => "/articles"],
            // ["title" => "装置", "link" => "/gadgets"],
            // ["title" => "生活方式", "link" => "/lifestyle"],
            // ["title" => "视频", "link" => "/video"],
            // ["title" => "联系", "link" => "/contact"]
        ]
    ];
});

$router->post('/articles/{id}/comments', function ($id, Request $request) {
    info('request', $request->input());
    $article = Article::findOrFail($id);

    $comment = $article->comments()->create([
        'visitor' => $request->ip(),
        'content' => $request->content,
        'comment_id' => $request->comment_id ?? 0
    ]);

    return new CommentResource($comment);
});
