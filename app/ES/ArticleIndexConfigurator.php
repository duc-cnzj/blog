<?php

namespace App\ES;

use ScoutElastic\Migratable;
use ScoutElastic\IndexConfigurator;

/**
 * Class ArticleIndexConfigurator
 * @package App\ES
 */
class ArticleIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    /**
     * @var string
     */
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
