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
$factory->define(App\Category::class, function () {
    $faker = Factory::create('zh_CN');

    return [
        'user_id' => 1,
        'name'    => $faker->word,
    ];
});
