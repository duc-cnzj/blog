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
$factory->define(App\User::class, function () {
    $faker = Factory::create('zh_CN');

    return [
        'name'     => $faker->name,
        'email'    => str_random(12) . $faker->unique()->safeEmail,
        'mobile'   => $faker->phoneNumber,
        'bio'      => $faker->text,
        'avatar'   => $faker->imageUrl,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        // 'remember_token' => str_random(10),
    ];
});
