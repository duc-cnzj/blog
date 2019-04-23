<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('recursiveReplies')) {
    /**
     * @param array $comments
     * @param int   $pid
     *
     * @return array
     * @author duc <1025434218@qq.com>
     */
    function recursiveReplies(array $comments, $pid = 0)
    {
        $arr = [];
        foreach ($comments as $item) {
            if ((int) $item['comment_id'] === $pid) {
                $data = recursiveReplies($comments, $item['id']);
                $item['replies'] = $data;
                $arr[] = $item;
            }
        }

        return $arr;
    }
}

if (! function_exists('config_path')) {
    /**
     * @return string
     *
     * @author duc <1025434218@qq.com>
     */
    function config_path()
    {
        return app()->getConfigurationPath();
    }
}

if (! function_exists('getAuthUser')) {
    /**
     * @param string ...$guards
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     *
     * @author duc <1025434218@qq.com>
     */
    function getAuthUser(string ...$guards)
    {
        $guards = empty($guard) ? array_keys(config('auth.guards')) : $guards;

        foreach ($guards as $guard) {
            Auth::shouldUse($guard);
            if ($user = Auth::user()) {
                return $user;
            }
        }

        return null;
    }
}
