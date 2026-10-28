<?php

namespace Tests\Feature;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use App\Player;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function a_new_user_can_register()
    {
        $this->get('/register')->assertStatus(200);

        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

        $this->post('register', $this->registrationAttributes())->assertRedirect('/');

        $ewigeListe = \App\Group::find(\App\Group::EWIGE_LISTE_ID);
        $this->assertTrue($ewigeListe->players->contains(auth()->user()->player));
        $this->assertInstanceOf('App\Player', auth()->user()->player);
        $this->assertInstanceOf('App\User', auth()->user());
    }

    #[Test]
    public function registration_requires_a_captcha()
    {
        $attributes = $this->registrationAttributes();
        unset($attributes['g-recaptcha-response']);

        $this->post('register', $attributes)->assertSessionHasErrors('g-recaptcha-response');

        $this->assertGuest();
    }

    /**
     * @return array<string, string>
     */
    private function registrationAttributes(): array
    {
        return [
            'surname' => $this->faker->firstName,
            'name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'geheim123',
            'password_confirmation' => 'geheim123',
            'g-recaptcha-response' => 'token',
        ];
    }

    #[Test]
    public function test_seeding()
    {
        //$this->seed('PlayerSeeder');
        \App\Player::factory()->create();

        $this->assertTrue(Player::all()->count() > 0);
    }
}
