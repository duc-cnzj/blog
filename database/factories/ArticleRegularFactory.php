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

// TODO 编辑器会把换行换成 ↵
$rules = [
//    ['express' => '/\d+/i', 'replace' => 'duc'],
    ['express' => '/[↵]*1\./i', 'replace' => '##'],
    ['express' => '/[↵]*2\./i', 'replace' => '###'],
    ['express' => '/[↵]*3\./i', 'replace' => '<h3>'],
];

$factory->define(App\ArticleRegular::class, function () use ($rules) {
    $faker = Factory::create('zh_CN');

    return [
        'rule' => array_random($rules),
        'user_id' => function () {
            return app()->environment() === 'testing'
                ? factory(App\User::class)->create([
                    'mobile'   => str_random(32),
                    'avatar'   => 'test1234567',
                    'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm',
                ])->id
                : 1;
        },
        'status' => $faker->boolean
    ];
});
