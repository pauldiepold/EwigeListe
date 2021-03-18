<?php

namespace Database\Factories;

use App\Group;
use App\Badge;
use App\Player;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'group_id' => function ()
            {
                return Group::factory()->create()->id;
            },
            'player_id' => function ()
            {
                return Player::factory()->create()->id;
            }

        ];
    }
}

