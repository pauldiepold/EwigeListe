<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'password' => Hash::make('test'),
            'remember_token' => Str::random(10),
            'player_id' => function () {
                return Player::factory()->create()->id;
            },
            // Auf Prod gilt zufällig users.id == player_id. Der Versatz lässt Tests rot werden,
            // die Benutzer- und Spieler-ID verwechseln (z. B. auth()->id() statt player_id).
            'id' => fn (array $attributes) => $attributes['player_id'] + 1000,
        ];
    }

    /**
     * Wer die Benutzer-ID 1 hat, ist Admin (User::isAdmin()).
     */
    public function admin(): static
    {
        return $this->state(['id' => 1]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
