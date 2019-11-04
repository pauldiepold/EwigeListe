<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Badge;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Badge::class, function (Faker $faker) {
    $date = Carbon::createFromDate($faker->year('now'), $faker->month('now'), 1);

    return [
        'year' => $date,
        'month' => $date,
        'value' => $faker->randomNumber(3),
        'type' => 'points',
        'group_id' => function ()
        {
            return factory('App\Group')->create()->id;
        },
        'player_id' => function ()
        {
            return factory('App\Player')->create()->id;
        }

    ];
});
