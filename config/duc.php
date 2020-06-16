<?php

return [
    'article_use_cache'   => env('ARTICLE_USE_CACHE', true),
    'function_timing_key' => env('FUNCTION_TIMING_KEY', 'request_timing'),
    'go_cache_prefix'     => env('GO_CACHE_PREFIX', 'go_'),
    'ip'                  => [
        'baidu' => env('IP_BAIDU_AK'),
        'ali'   => env('IP_ALI_CODE'),
    ],
];
