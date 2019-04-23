<?php

use Illuminate\Support\Arr;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/
// TODO 编辑器会把换行换成 ↵
$rules = [
//    ['express' => '/\d+/i', 'replace' => 'duc'],
    ['express' => '/[↵]*1\./i', 'replace' => '##'],
    ['express' => '/[↵]*2\./i', 'replace' => '###'],
    ['express' => '/[↵]*3\./i', 'replace' => '<h3>'],
];

$factory->define(App\ArticleRegular::class, function (Faker\Generator $faker) use ($rules) {
    return [
        'rule'    => Arr::random($rules),
        'user_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'status' => $faker->boolean,
    ];
});
