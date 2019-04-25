<?php

use Illuminate\Support\Str;

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
$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name'     => $faker->name,
        'email'    => Str::random(12) . $faker->unique()->safeEmail,
        'mobile'   => $faker->phoneNumber,
        'bio'      => $faker->text,
        'avatar'   => $faker->imageUrl,
        'password' => 'secret', // secret
    ];
});
