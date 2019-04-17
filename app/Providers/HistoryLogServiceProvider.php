<?php

namespace App\Providers;

use App\Contracts\WhiteListIpImp;
use App\Contracts\WhiteListUrlImp;
use App\Services\WhiteListIpService;
use App\Services\WhiteListUrlService;
use App\Contracts\HistoryLogHandlerImp;
use Illuminate\Support\ServiceProvider;
use App\Services\HistoryLog\HistoryLogHandler;

/**
 * Class HistoryLogServiceProvider
 * @package App\Providers
 */
class HistoryLogServiceProvider extends ServiceProvider
{
    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public function boot()
    {
        $this->app->singleton(WhiteListUrlImp::class, function () {
            return new WhiteListUrlService();
        });

        $this->app->singleton(WhiteListIpImp::class, function () {
            return new WhiteListIpService();
        });

        $this->app->singleton(HistoryLogHandlerImp::class, function () {
            return new HistoryLogHandler();
        });
    }
}
