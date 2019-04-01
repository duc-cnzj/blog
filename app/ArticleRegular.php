<?php

namespace App;

use App\Services\HandleRule;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ArticleRegular
 * @package App
 */
class ArticleRegular extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $casts = [
        'rule'   => 'array',
        'status' => 'boolean',
    ];

    /**
     * @var string
     */
    protected $table = 'article_regular_rules';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     *
     * @author duc <1025434218@qq.com>
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $value
     *
     * @author duc <1025434218@qq.com>
     */
    public function setRuleAttribute($value)
    {
        $value['express'] = trim($value['express'], '/');
        $value['express'] = (new HandleRule($value['express']))->apply();

        $this->attributes['rule'] = json_encode($value);
    }
}
