<?php

use Illuminate\Support\Facades\Auth;

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

if (! function_exists('get_auth_user')) {
    /**
     * @param string ...$guards
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     *
     * @author duc <1025434218@qq.com>
     */
    function get_auth_user(string ...$guards)
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

if (! function_exists('convert_time')) {
    /**
     * convert time to human readable time
     *
     * @param $time
     * @return int|string
     *
     * @author duc <1025434218@qq.com>
     */
    function convert_time($time)
    {
        if ($time == 0) {
            return 0;
        }
        $unit = [-4=>'ps', -3=>'ns', -2=>'mcs', -1=>'ms', 0=>'s'];
        $i = min(0, floor(log($time, 1000)));

        $t = @round($time / pow(1000, $i), 1);

        return $t . $unit[$i];
    }
}
