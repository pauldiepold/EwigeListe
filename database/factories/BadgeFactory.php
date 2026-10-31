<?php

namespace Database\Factories;

use App\Models\Badge;
use App\Models\Group;
use App\Models\Player;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BadgeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Badge::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $date = Carbon::createFromDate($this->faker->year('now'), $this->faker->month('now'), 1);

        return [
            'year' => $date,
            'month' => $date,
            'value' => $this->faker->randomNumber(3),
            'type' => 'points',
            'group_id' => function () {
                return Group::factory()->create()->id;
            },
            'player_id' => function () {
                return Player::factory()->create()->id;
            },

        ];
    }
}
