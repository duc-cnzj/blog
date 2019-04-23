<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Carbon::setlocale(env('CARBON_LOCATE', 'zh'));
    }

    public function boot()
    {
        $this->app['config']->set(['app.faker_locale' => 'zh_CN']);

        \DB::listen(function ($query) {
            \Log::info($query->sql, $query->bindings);
        });
    }
}
