<?php

use App\Article;

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

$factory->define(App\Comment::class, function (Faker\Generator $faker) {
    return [
        'visitor'    => $faker->ipv4,
        'content'    => $faker->sentence,
        'comment_id' => 0,
        'article_id' => function () {
            return factory(Article::class)->create()->id;
        },
        'userable_id'   => 0,
        'userable_type' => '',
    ];
});
