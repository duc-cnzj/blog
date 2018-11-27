<?php

namespace App;

use App\ES\ArticleRule;
use App\Events\ArticleCreated;
use ScoutElastic\Searchable;
use App\ES\ArticleIndexConfigurator;
use Illuminate\Database\Eloquent\Model;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;

class Article extends Model
{
    use Searchable, PivotEventTrait;

    /**
     * @var string
     */
    protected $indexConfigurator = ArticleIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        ArticleRule::class,
    ];

    /**
     * @var array
     */
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

    /**
     * @var array
     */
    protected $appends = ['highlight_content'];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     *
     * @author duc <1025434218@qq.com>
     */
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            info('static::created', $model->toArray());
            event(new ArticleCreated($model->makeHidden('content')->load('author')));
        });

        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) {
            if (! $model->shouldBeSearchable()) {
                $model->unsearchable();

                return;
            }

            $model->searchable();
        });
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author duc <1025434218@qq.com>
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author duc <1025434218@qq.com>
     */
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     *
     * @author duc <1025434218@qq.com>
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'article_tag');
    }

    /**
     * @return $this
     *
     * @author duc <1025434218@qq.com>
     */
    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    /**
     * @return mixed
     *
     * @author duc <1025434218@qq.com>
     */
    public function getRecommendArticles()
    {
        return static::where('category_id', $this->category_id)
            ->inRandomOrder()
            ->take(3)
            ->get(['id', 'title'])
            ->toArray();
    }

    /**
     * @return null
     *
     * @author duc <1025434218@qq.com>
     */
    public function getContentHtmlAttribute()
    {
        $arr = json_decode($this->content);

        if (is_object($arr)) {
            return $arr->html;
        } else {
            return null;
        }
    }

    /**
     * @return null
     *
     * @author duc <1025434218@qq.com>
     */
    public function getContentMdAttribute()
    {
        $arr = json_decode($this->content);

        if (is_object($arr)) {
            return $arr->md;
        } else {
            return null;
        }
    }

    /**
     * @return array
     *
     * @author duc <1025434218@qq.com>
     */
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
