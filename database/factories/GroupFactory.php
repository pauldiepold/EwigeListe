<?php

namespace Database\Factories;

use App\Group;
use App\Player;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Group::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => e($this->faker->lastName),
            'created_by' => function ()

            {
                return Player::factory()->create()->id;
            }
        ];
    }
}

