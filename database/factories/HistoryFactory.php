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

use App\User;

$factory->define(App\History::class, function (Faker\Generator $faker) {
    return [
        'content'       => $faker->randomElement([[], ['name' => 'duc', 'age' => 24], ['a' => 'das'], ['q' => 'da']]),
        'ip'            => $faker->ipv4,
        'url'           => $faker->url,
        'method'        => $faker->randomElement(['GET', 'POST', 'PUT', 'PATCH', 'DELETE']),
        'status_code'   => $faker->randomElement([200, 201, 204, 301, 400, 401, 404, 500]),
        'response'      => $faker->sentence,
        'user_agent'    => $faker->userAgent,
        'address'       => $faker->address,
        'visited_at'    => $faker->date(),
        'userable_id'   => 0,
        'userable_type' => '',
    ];
});

$factory->state(App\History::class, 'withAdminUser', function (Faker\Generator $faker) {
    return [
        'userable_id'   => factory(User::class)->create()->id,
        'userable_type' => get_class(factory(User::class)->create()),
    ];
});

$factory->state(App\History::class, 'withSocialiteUser', function (Faker\Generator $faker) {
    return [
        'userable_id'   => factory(\App\SocialiteUser::class)->create()->id,
        'userable_type' => get_class(factory(\App\SocialiteUser::class)->create()),
    ];
});
