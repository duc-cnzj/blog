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
use Illuminate\Support\Str;

$factory->define(App\History::class, function (Faker\Generator $faker) {
    return [
        'content'       => $faker->randomElement([[], ['name' => 'duc', 'age' => 24], ['a' => 'das'], ['q' => 'da']]),
        'ip'            => $faker->ipv4,
        'url'           => $faker->url,
        'method'        => $faker->randomElement(['GET', 'POST', 'PUT', 'PATCH', 'DELETE']),
        'status_code'   => $faker->randomElement([200, 201, 204, 301, 400, 401, 404, 500]),
        'response'      => $faker->sentence,
        'user_agent'    => $faker->userAgent,
        'visited_at'    => $faker->date(),
        'userable_id'   => 0,
        'userable_type' => '',
    ];
});

$factory->state(App\History::class, 'withAdminUser', function (Faker\Generator $faker) {
    $user = factory(User::class)->create([
        'mobile'   => Str::random(32),
        'avatar'   => 'test1234567',
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
    ]);

    return [
        'userable_id'   => $user->id,
        'userable_type' => get_class($user),
    ];
});

$factory->state(App\History::class, 'withSocialiteUser', function (Faker\Generator $faker) {
    $user = factory(\App\SocialiteUser::class)->create();

    return [
        'userable_id'   => $user->id,
        'userable_type' => get_class($user),
    ];
});
