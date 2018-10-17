<?php

if (! function_exists('c')) {
    /**
     * @param array $comments
     * @param int   $pid
     *
     * @return array
     * @author duc <1025434218@qq.com>
     */
    function c(array $comments, $pid = 0) {
        $arr = [];
        foreach ($comments as $item) {
            if ($item['comment_id'] === $pid) {
                $data = c($comments, $item['id']);
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
    function config_path() {
        return app()->getConfigurationPath();
    }
}