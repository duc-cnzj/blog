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
    $router->get('/users', 'UserController@index');

    $router->put('/users/{user}', 'UserController@update');

    $router->delete('/users/{user}', 'UserController@destroy');

    $router->post('/users', 'UserController@store');

    $router->post('/update_info', 'AuthController@updateInfo');

    $router->post('/images', 'ImageController@store');

    $router->get('/articles', 'ArticleController@index');

    $router->get('/search_articles', 'ArticleController@search');

    $router->get('/articles/{id}', 'ArticleController@show');

    $router->post('/articles', 'ArticleController@store');

    $router->put('/articles/{id}', 'ArticleController@update');

    $router->delete('/articles/{id}', 'ArticleController@destroy');

    $router->get('/categories', 'CategoryController@index');

    $router->get('/tags', 'TagController@index');
});
