<?php

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
$factory->define(App\Article::class, function (Faker\Generator $faker) {
    return [
        'author_id' => function () {
            return factory(App\User::class)->create()->id;
        },
        'content'     => $faker->catchPhrase,
        'desc'        => $faker->catchPhrase,
        'title'       => $faker->title,
        'head_image'  => $faker->imageUrl,
        'category_id' => function () {
            return factory(App\Category::class)->create()->id;
        },
        'display' => true,
        'top_at'  => null,
    ];
});
