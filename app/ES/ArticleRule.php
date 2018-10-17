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
                'title' => [
                    'type'     => 'plain',
                    'pre_tags' => "<span style='background-color:#bfa;padding:1px;'>",
                    'post_tags'=> '</span>',
                ],
                'tags' => [
                    'type'     => 'plain',
                    'pre_tags' => "<span style='background-color:#bfa;padding:1px;'>",
                    'post_tags'=> '</span>',
                ],
                'article_category.name' => [
                    'type'     => 'plain',
                    'pre_tags' => "<span style='background-color:#bfa;padding:1px;'>",
                    'post_tags'=> '</span>',
                ],
                'content' => [
                    'type'               => 'plain',
                    'pre_tags'           => "<span style='background-color:#bfa;padding:1px;'>",
                    'post_tags'          => '</span>',
                    'fragment_size'      => 10,
                    'number_of_fragments'=> 2,
                ],
                'desc' => [
                    'type'               => 'plain',
                    'fragment_size'      => 10,
                    'number_of_fragments'=> 2,
                ],
            ],
            'pre_tags' => "<span style='color:red'>",
            'post_tags'=> '</span>',
        ];
    }
}
