<?php

namespace App;

use App\Services\HandleRule;
use Illuminate\Database\Eloquent\Model;

class ArticleRegular extends Model
{
    /**
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'rule'   => 'array',
        'status' => 'boolean',
    ];

    protected $table = 'article_regular_rules';

    public function user()
    {
        $this->belongsTo(User::class, 'user_id');
    }

    public function setRuleAttribute($value)
    {
        $value['express'] = trim($value['express'], '/');
        $value['express'] = (new HandleRule($value['express']))->apply();

        $this->attributes['rule'] = json_encode($value);
    }
}
