<?php

use Faker\Generator as Faker;

$factory->define(App\Report::class, function (Faker $faker) {
    return [
        'titulo' => $faker->sentence,
        'descripcion' => $faker->sentence
    ];
});
