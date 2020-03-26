<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Group;
use Faker\Generator as Faker;

$factory->define(Group::class, function (Faker $faker)
{
    return [
        'name' => e($faker->lastName),
        'created_by' => function ()

        {
            return factory('App\Player')->create()->id;
        }
    ];
});
