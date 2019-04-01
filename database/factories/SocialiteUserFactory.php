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

use Illuminate\Support\Str;

$factory->define(App\SocialiteUser::class, function (Faker\Generator $faker) {
    return [
        'user_id'       => 0,
        'name'          => $faker->name,
        'avatar'        => $faker->imageUrl,
        'url'           => $faker->url,
        'identity_type' => 'github',
        'identifier'    => Str::random(32),
        'credential'    => Str::random(32),
        'last_login_at' => \Carbon\Carbon::now(),
    ];
});
