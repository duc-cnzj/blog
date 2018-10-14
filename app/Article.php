<?php

namespace App;

use App\ES\ArticleRule;
use ScoutElastic\Searchable;
use App\ES\ArticleIndexConfigurator;
use Illuminate\Database\Eloquent\Model;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Article extends Model
{
    use Searchable, PivotEventTrait;

    protected $indexConfigurator = ArticleIndexConfigurator::class;

    protected $searchRules = [
        ArticleRule::class,
    ];

    protected $mapping = [
        'properties' => [
            'author'           => [
                'properties' => [
                    'id'   => ['type' => 'integer'],
                    'name' => [
                        'type'            => 'text',
                        'analyzer'        => 'ik_max_word',
                        'search_analyzer' => 'ik_max_word',
                    ],
                ],
            ],
            'article_category' => [
                'properties' => [
                    'id'   => ['type' => 'integer'],
                    'name' => [
                        'type'            => 'text',
                        'analyzer'        => 'ik_max_word',
                        'search_analyzer' => 'ik_max_word',
                    ],
                ],
            ],
            'content'          => [
                'type'            => 'text',
                'analyzer'        => 'ik_max_word',
                'search_analyzer' => 'ik_max_word',
                'fields'          => [
                    'raw' => [
                        'type'         => 'keyword',
                        'ignore_above' => 256,
                    ],
                ],
            ],
            'title'            => [
                'type'            => 'text',
                'analyzer'        => 'ik_max_word',
                'search_analyzer' => 'ik_max_word',
            ],
            'desc'             => [
                'type'            => 'text',
                'analyzer'        => 'ik_max_word',
                'search_analyzer' => 'ik_max_word',
            ],
            'tags'             => ['type' => 'text'],
        ],
    ];

    protected $appends = ['highlight_content'];

    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if (! $model->shouldBeSearchable()) {
                $model->unsearchable();

                return;
            }
            info('here');
            $model->searchable();
        });
    }

    public function toSearchableArray()
    {
        $model = $this->load(['category', 'author', 'tags']);

        $result = [
            'author'           => [
                'id'   => $model->author->id,
                'name' => $model->author->name,
            ],
            'article_category' => [
                'id'   => $model->category->id,
                'name' => $model->category->name,
            ],
            'content'          => $model->content_md,
            'title'            => $model->title,
            'desc'             => $model->desc,
            'tags'             => $model->tags()->pluck('name')->toArray(),
        ];

        info('searchable', $result);

        return $result;
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function getRecommendArticles()
    {
        return static::where('category_id', $this->category_id)
            ->inRandomOrder()
            ->take(3)
            ->get(['id', 'title'])
            ->toArray();
    }

    public function getContentHtmlAttribute()
    {
        $arr = json_decode($this->content);

        if (is_object($arr)) {
            return $arr->html;
        } else {
            return null;
        }
    }

    public function getContentMdAttribute()
    {
        $arr = json_decode($this->content);

        if (is_object($arr)) {
            return $arr->md;
        } else {
            return null;
        }
    }

    public function getHighlightContentAttribute()
    {
        $h = optional($this->highlight);

        $categoryField = 'article_category.name';

        return [
                'content'  => is_null($h->content) ? null : implode('......', $h->content),
                'desc'     => is_null($h->desc) ? null : implode('......', $h->desc),
                'title'    => $h->title[0],
                'category' => $h->{$categoryField}[0],
                'tags'     => is_null($h->tags) ? null : implode(',', $h->tags),
            ];
    }
}
