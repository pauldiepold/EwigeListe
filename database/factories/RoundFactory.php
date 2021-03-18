<?php

namespace Database\Factories;

use App\Player;
use App\Round;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RoundFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Round::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'created_by' => function ()
            {
                return Player::factory()->create()->id;
            },
        ];
    }
}
