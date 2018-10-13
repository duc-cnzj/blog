<?php

namespace App\ES;

use ScoutElastic\SearchRule;

class ArticleRule extends SearchRule
{
    /**
     * @inheritdoc
     */
    public function buildHighlightPayload()
    {
        return [
            'fields' => [
                'content' => [
                    'type' => 'plain',
                    "pre_tags"=>"<span style='color:blue'>",
                    "post_tags"=>"</span>"
                ],
                'desc' => [
                    'type' => 'plain',
                    "fragment_size"=> 150,
                    "number_of_fragments"=> 3
                ]
            ],
            "pre_tags"=>"<span style='color:red'>",
            "post_tags"=>"</span>",
        ];
    }

    /**
     * @inheritdoc
     */
    public function buildQueryPayload()
    {
        //
    }
}