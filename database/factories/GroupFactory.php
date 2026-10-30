<?php

namespace Database\Factories;

use App\Group;
use App\Player;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'name' => $this->faker->lastName,
            'created_by' => function () {
                return Player::factory()->create()->id;
            },
        ];
    }

    /**
     * Die Ewige Liste: hat immer die ID 1 und existiert in jeder Installation.
     */
    public function ewigeListe(): static
    {
        return $this->state([
            'id' => Group::EWIGE_LISTE_ID,
            'name' => 'Ewige Liste',
        ]);
    }

    /**
     * Geschlossene Liste: nimmt keine neuen Runden mehr an.
     */
    public function closed(): static
    {
        return $this->state(['closed' => true]);
    }
}
