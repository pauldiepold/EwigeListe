<?php

namespace Database\Factories;

use App\Game;
use App\Round;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'points' => $this->faker->randomDigitNotNull,
            'round_id' => function ()
            {
                return Round::factory()->create()->id;
            }
        ];
    }
}

