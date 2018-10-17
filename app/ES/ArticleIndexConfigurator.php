<?php

namespace App\ES;

use ScoutElastic\Migratable;
use ScoutElastic\IndexConfigurator;

class ArticleIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = 'article_index';

    /**
     * @var array
     */
    protected $settings = [
        'number_of_shards'   => '1',
        'number_of_replicas' => '1',
        'analysis'           => [
            'analyzer' => [
                'es_std' => [
                    'type'      => 'standard',
                    'stopwords' => '_spanish_',
                ],
            ],
        ],
    ];
}
