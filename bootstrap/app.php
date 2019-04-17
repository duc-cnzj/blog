<?php

use DucCnzj\Ip\IpClient;
use App\Services\IpRedisCacheStore;
use Laravel\Socialite\SocialiteManager;
use Laravel\Socialite\Contracts\Factory;
use App\Providers\ArticleServiceProvider;
use App\Providers\HistoryLogServiceProvider;
use Illuminate\Filesystem\FilesystemServiceProvider;

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();

$app->withEloquent();

$app->configure('duc');
$app->configure('queue');
$app->configure('cors');
$app->configure('scout');
$app->configure('cache');
$app->configure('services');
$app->configure('filesystems');
$app->configure('broadcasting');
$app->configure('scout_elastic');
$app->configure('backup-manager');
$app->instance('path.config', app()->basePath() . DIRECTORY_SEPARATOR . 'config');

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(Factory::class, function ($app) {
    return new SocialiteManager($app);
});

$app->singleton('ip', function () {
    return (new IpClient())
        ->setCacheStore(new IpRedisCacheStore)
        ->use('taobao');
});

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

$app->middleware([
    \Barryvdh\Cors\HandleCors::class,
    \App\Http\Middleware\HistoryLog::class,
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'cors' => \Barryvdh\Cors\HandleCors::class,
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(Tymon\JWTAuth\Providers\LumenServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(Laravel\Tinker\TinkerServiceProvider::class);
$app->register(Barryvdh\Cors\ServiceProvider::class);
// laravel-lang
$app->register(Overtrue\LaravelLang\TranslationServiceProvider::class);
// Predis
$app->register(Illuminate\Redis\RedisServiceProvider::class);
// elasticSearch
$app->register(Laravel\Scout\ScoutServiceProvider::class);
$app->register(ScoutElastic\ScoutElasticServiceProvider::class);
$app->register(BackupManager\Laravel\Lumen55ServiceProvider::class);
$app->register(FilesystemServiceProvider::class);
// customer service providers
$app->register(ArticleServiceProvider::class);
$app->register(HistoryLogServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
    require __DIR__ . '/../routes/admin.php';
});

return $app;
