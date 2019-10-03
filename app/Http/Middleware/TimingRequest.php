<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Contracts\Support\Responsable;

class TimingRequest
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);

        if ($response instanceof Responsable) {
            $response = $response->toResponse($request);
        }

        $time = convert_time(microtime(true) - APP_START);
        $response->headers->set(config('duc.function_timing_key'), $time);

        return $response;
    }
}
