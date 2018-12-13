<?php

$router->group([
    'prefix'    => 'auth',
    'namespace' => 'Admin',
], function ($router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/logout', 'AuthController@logout');
    $router->post('/refresh', 'AuthController@refresh');
    $router->post('/me', 'AuthController@me');
});

$router->group([
    'prefix'     => 'admin',
    'middleware' => 'auth',
    'namespace'  => 'Admin',
], function ($router) {
    $router->post('/article_regulars/change_status', 'ArticleRegularController@changeStatus');

    $router->post('/article_regulars/test', 'ArticleRegularController@test');

    $router->get('/article_regulars', 'ArticleRegularController@index');

    $router->delete('/article_regulars/{id}', 'ArticleRegularController@destroy');

    $router->post('/article_regulars', 'ArticleRegularController@store');

    $router->get('/dashboard', 'DashboardController@index');

    $router->get('/comments/{id}', 'CommentController@show');

    $router->get('/comments', 'CommentController@index');

    $router->post('/articles/{articleId}/comments', 'CommentController@store');

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

    $router->get('/categories', 'CategoryController@index');

    $router->get('/tags', 'TagController@index');
});
