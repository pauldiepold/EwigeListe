<?php

namespace Database\Factories;

use App\Models\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Player::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'surname' => $this->faker->firstName,
            'name' => $this->faker->lastName,
        ];
    }

    /**
     * Versteckter Spieler: bei der Rundenerstellung nicht auswählbar.
     */
    public function hidden(): static
    {
        return $this->state(['hide' => true]);
    }
}
