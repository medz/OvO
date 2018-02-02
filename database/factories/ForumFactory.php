<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Forum::class, function (Faker $faker) {
    return [
        'bg_color' => $faker->hexColor,
    ];
});
