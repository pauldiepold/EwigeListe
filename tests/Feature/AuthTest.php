<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Player;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_new_user_can_register()
    {
        $this->get('/register')->assertStatus(200);

        $password = $this->faker->password();
        $attributes = [
            'surname' => $this->faker->firstName(),
            'name' => $this->faker->lastName(),
            'email' => $this->faker->email(),
            'password' => $password,
            'password_confirmation' => $password
        ];

        $this->post('register', $attributes)->assertRedirect('home');

        $this->assertInstanceOf('App\Player', auth()->user()->player);
        $this->assertInstanceOf('App\User', auth()->user());
        $this->assertInstanceOf('App\Profile', auth()->user()->player->profile);
    }

    /** @test */
    public function test_seeding()
    {
        //$this->seed('PlayerSeeder');
        factory('App\Player')->create();

        $this->assertTrue(Player::all()->count() > 0);
    }
}
