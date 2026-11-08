<?php

namespace Tests\Feature;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Models\Group;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function a_new_user_can_register()
    {
        $this->get('/register')->assertStatus(200);

        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

        $this->post('register', $this->registrationAttributes())->assertRedirect('/');

        $ewigeListe = Group::find(Group::EWIGE_LISTE_ID);
        $this->assertTrue($ewigeListe->players->contains(auth()->user()->player));
        $this->assertInstanceOf('App\Models\Player', auth()->user()->player);
        $this->assertInstanceOf('App\Models\User', auth()->user());
    }

    #[Test]
    public function registration_requires_a_captcha()
    {
        $attributes = $this->registrationAttributes();
        unset($attributes['g-recaptcha-response']);

        $this->post('register', $attributes)->assertSessionHasErrors('g-recaptcha-response');

        $this->assertGuest();
    }

    #[Test]
    public function registration_names_the_missing_first_and_last_name(): void
    {
        $this->post('register', [...$this->registrationAttributes(), 'first_name' => '', 'last_name' => ''])
            ->assertSessionHasErrors([
                'first_name' => 'Vorname muss ausgefüllt sein.',
                'last_name' => 'Nachname muss ausgefüllt sein.',
            ]);
    }

    #[Test]
    public function registration_rejects_a_password_shorter_than_eight_characters(): void
    {
        $this->post('register', [...$this->registrationAttributes(), 'password' => 'kurz123', 'password_confirmation' => 'kurz123'])
            ->assertSessionHasErrors(['password' => 'Passwort muss mindestens 8 Zeichen lang sein.']);

        $this->assertGuest();
    }

    #[Test]
    public function registration_stores_the_email_in_lowercase(): void
    {
        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);

        $this->post('register', [...$this->registrationAttributes(), 'email' => 'Max.Mustermann@Example.com'])
            ->assertRedirect('/');

        $this->assertSame('max.mustermann@example.com', auth()->user()->email);
    }

    #[Test]
    public function the_unused_password_confirmation_page_returns_404(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('password.confirm'))
            ->assertNotFound();
    }

    #[Test]
    public function a_failed_user_creation_during_registration_leaves_no_player_behind(): void
    {
        NoCaptcha::shouldReceive('verifyResponse')->once()->andReturn(true);
        User::creating(fn () => throw new RuntimeException('Benutzer konnte nicht angelegt werden'));
        $attributes = $this->registrationAttributes();

        $this->post('register', $attributes)->assertServerError();

        $this->assertDatabaseMissing('players', ['first_name' => $attributes['first_name'], 'last_name' => $attributes['last_name']]);
        $this->assertGuest();
    }

    /**
     * @return array<string, string>
     */
    private function registrationAttributes(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'geheim123',
            'password_confirmation' => 'geheim123',
            'g-recaptcha-response' => 'token',
        ];
    }

    #[Test]
    public function test_seeding()
    {
        // $this->seed('PlayerSeeder');
        Player::factory()->create();

        $this->assertTrue(Player::all()->count() > 0);
    }
}
