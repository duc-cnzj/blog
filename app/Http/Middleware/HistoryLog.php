<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use App\Jobs\RecordUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 * Class HistoryLog
 * @package App\Http\Middleware
 */
class HistoryLog
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
        return $next($request);
    }

    /**
     * @param Request $request
     * @param Response $response
     *
     * @author duc <1025434218@qq.com>
     */
    public function terminate(Request $request, Response $response)
    {
        $user = Auth::user();
        $userableId = $user ? $user->id : 0;
        $userableType = $user ? get_class($user) : '';

        dispatch(new RecordUser([
            'ip'            => $request->ip(),
            'url'           => $request->fullUrl(),
            'method'        => $request->getMethod(),
            'content'       => $request->input(),
            'user_agent'    => $request->userAgent(),
            'visited_at'    => Carbon::now(),
            'userable_id'   => $userableId,
            'userable_type' => $userableType,
            'response'      => $response->getContent(),
        ]));
    }
}
