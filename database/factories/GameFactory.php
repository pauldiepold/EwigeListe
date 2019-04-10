<?php

use Faker\Generator as Faker;

$factory->define(App\Game::class, function (Faker $faker)
{
    return [
        'points' => $faker->randomDigitNotNull,
        'round_id' => function ()
        {
            return factory(App\Round::class)->create()->id;
        }
    ];
});
