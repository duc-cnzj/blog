<?php

use Faker\Factory;

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
// Faker\Generator $faker
$factory->define(App\Article::class, function () {
    $faker = Factory::create('zh_CN');

    return [
        'author_id' => 1,
        'content' => $faker->sentence,
        'desc' => $faker->sentence,
        'title' => $faker->title,
        'head_image' => $faker->imageUrl
    ];
});
