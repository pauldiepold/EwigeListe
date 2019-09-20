<?php

use Faker\Generator as Faker;

$factory->define(App\Round::class, function (Faker $faker)
{
    return [
        'created_by' => function ()
        {
            return factory('App\Player')->create()->id;
        },
    ];
});
