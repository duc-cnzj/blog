<?php

namespace App\Providers;

use App\User;
use App\Article;
use App\ArticleRegular;
use App\Policies\UserPolicy;
use App\Policies\ArticlePolicy;
use Illuminate\Support\Facades\Gate;
use App\Policies\ArticleRegularPolicy;
use Illuminate\Support\ServiceProvider;

/**
 * Class AuthServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $policies = [
        User::class           => UserPolicy::class,
        Article::class        => ArticlePolicy::class,
        ArticleRegular::class => ArticleRegularPolicy::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }
}
