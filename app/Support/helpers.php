<?php

if (! function_exists('c')) {
    function c($comments, $pid = 0) {
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

