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
     * @var array
     */
    protected $whiteList = ['admin/histories*'];

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
        $whiteList = $this->getWhiteList();

        if ($request->is(...$whiteList)) {
            info("白名单: {$request->fullUrl()} 不记录访问信息。");

            return;
        }

        if (in_array($request->getMethod(), ['OPTIONS', 'HEAD'])) {
            return;
        }

        $user = Auth::user();
        $userableId = $user ? $user->id : 0;
        $userableType = $user ? get_class($user) : '';

        $data = [
            'ip'            => $request->ip(),
            'url'           => $request->getPathInfo(),
            'method'        => $request->getMethod(),
            'content'       => $request->input(),
            'user_agent'    => $request->userAgent(),
            'visited_at'    => Carbon::now(),
            'userable_id'   => $userableId,
            'userable_type' => $userableType,
            'response'      => $response->getContent(),
            'status_code'   => $response->getStatusCode(),
        ];

        dispatch(new RecordUser($data));
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
    public function getWhiteList(): array
    {
        $whiteList = array_map(function ($except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            return $except;
        }, $this->whiteList);

        return $whiteList;
    }
}
