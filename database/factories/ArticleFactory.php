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
    $content = $faker->catchPhrase;

    return [
        'author_id' => function () {
            return app()->environment('testing')
                ? factory(App\User::class)->create([
                    'mobile'   => str_random(32),
                    'avatar'   => 'test1234567',
                    'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
                ])->id
                : 1;
        },
        'content' => json_encode([
            'html' => $content,
            'md'   => $content,
        ]),
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
