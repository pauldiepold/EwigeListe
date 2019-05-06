<?php

use Faker\Generator as Faker;

$factory->define(App\Player::class, function (Faker $faker)
{
    return [
        'surname' => $faker->firstName,
        'name' => $faker->lastName,
    ];
});
