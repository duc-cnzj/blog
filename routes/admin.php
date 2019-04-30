<?php

use Laravel\Lumen\Routing\Router;

/** @var Router $router */
$router->group([
    'prefix'    => 'auth',
    'namespace' => 'Admin',
], function (Router $router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/logout', 'AuthController@logout');
    $router->post('/refresh', 'AuthController@refresh');
    $router->post('/me', 'AuthController@me');
});

$router->group([
    'prefix'     => 'admin',
    'middleware' => 'auth',
    'namespace'  => 'Admin',
], function (Router $router) {
    $router->post('/article_regulars/change_status', 'ArticleRegularController@changeStatus');

    $router->post('/article_regulars/test', 'ArticleRegularController@test');

    $router->get('/article_regulars', 'ArticleRegularController@index');

    $router->delete('/article_regulars/{id}', 'ArticleRegularController@destroy');

    $router->post('/article_regulars', 'ArticleRegularController@store');

    $router->get('/dashboard', 'DashboardController@index');

    $router->get('/history_data', 'DashboardController@historyData');

    $router->get('/comments/{id}', 'CommentController@show');

    $router->get('/comments', 'CommentController@index');

    $router->post('/articles/{articleId}/comments', 'CommentController@store');

    $router->delete('/comments/{id}', 'CommentController@destroy');

    $router->get('/users', 'UserController@index');

    $router->get('/users/{id}', 'UserController@show');

    $router->put('/users/{id}', 'UserController@update');

    $router->delete('/users/{id}', 'UserController@destroy');

    $router->post('/users', 'UserController@store');

    $router->post('/update_info', 'AuthController@updateInfo');

    $router->post('/images', 'ImageController@store');

    $router->get('/articles', 'ArticleController@index');

    $router->put('/article_change_display/{id}', 'ArticleController@changeDisplay');

    $router->get('/search_articles', 'ArticleController@search');

    $router->get('/articles/{id}', 'ArticleController@show');

    $router->post('/articles', 'ArticleController@store');

    $router->put('/articles/{id}', 'ArticleController@update');

    $router->delete('/articles/{id}', 'ArticleController@destroy');

    $router->put('/article_set_top/{id}', 'ArticleController@setTop');

    $router->put('/article_cancel_set_top/{id}', 'ArticleController@cancelSetTop');

    $router->get('/categories', 'CategoryController@index');

    $router->get('/tags', 'TagController@index');

    $router->get('/histories', 'HistoryController@index');

    $router->get('/histories/{id}', 'HistoryController@show');

    $router->get('/url_white_lists', 'WhiteListUrlController@index');

    $router->post('/url_white_lists', 'WhiteListUrlController@store');

    $router->delete('/url_white_lists', 'WhiteListUrlController@destroy');

    $router->get('/ip_white_lists', 'WhiteListIpController@index');

    $router->post('/ip_white_lists', 'WhiteListIpController@store');

    $router->delete('/ip_white_lists', 'WhiteListIpController@destroy');

    $router->get('/socialite_users', 'SocialiteUserController@index');
});
