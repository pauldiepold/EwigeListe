<?php

use Faker\Generator as Faker;

$factory->define(App\Player::class, function (Faker $faker)
{
    return [
        'surname' => e($faker->firstName),
        'name' => e($faker->lastName),
    ];
});
