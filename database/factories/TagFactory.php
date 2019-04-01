<?php

use App\User;
use Faker\Factory;
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
// Faker\Generator $faker
$factory->define(App\Tag::class, function () {
    $faker = Factory::create('zh_CN');

    return [
        'user_id' => function () {
            return app()->environment('testing') ? factory(User::class)->create([
                'mobile'   => Str::random(32),
                'avatar'   => 'test1234567',
                'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
            ])->id : 1;
        },
        'name' => $faker->word,
    ];
});
