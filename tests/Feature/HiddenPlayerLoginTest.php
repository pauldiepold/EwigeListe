<?php

namespace Tests\Feature;

use App\Player;
use App\SocialiteUser;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\TestResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as ProviderUser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Versteckte Spieler kommen über keinen Weg hinein: Die Sperre greift bei jedem Request, nicht
 * nur im Login-Formular. Nur der Admin ist ausgenommen.
 */
class HiddenPlayerLoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_running_session_of_a_hidden_player_ends_on_the_next_request(): void
    {
        $this->actingAs($this->hiddenUser());

        $this->assertLockedOut($this->get(route('rounds.create')));
    }

    #[Test]
    public function a_hidden_player_is_also_locked_out_on_public_pages(): void
    {
        $this->actingAs($this->hiddenUser());

        $this->assertLockedOut($this->get(route('index')));
    }

    #[Test]
    public function an_inertia_patch_of_a_hidden_player_redirects_with_303(): void
    {
        $user = $this->hiddenUser();

        $this->actingAs($user)
            ->withHeader('X-Inertia', 'true')
            ->patch(route('users.updateName', $user), ['surname' => 'Neu', 'name' => 'Name'])
            ->assertStatus(303)
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertNotSame('Neu', $user->player->fresh()->surname);
    }

    #[Test]
    public function a_hidden_player_is_locked_out_right_after_the_password_login(): void
    {
        $user = $this->hiddenUser();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'test'])
            ->assertRedirect('/');

        $this->assertLockedOut($this->get('/'));
    }

    #[Test]
    public function a_wrong_password_does_not_reveal_that_a_player_is_hidden(): void
    {
        $user = $this->hiddenUser();

        $this->from(route('login'))
            ->post(route('login'), ['email' => $user->email, 'password' => 'falsch'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email')
            ->assertInertiaFlashMissing('toast');
    }

    #[Test]
    public function a_hidden_player_is_locked_out_right_after_the_socialite_login(): void
    {
        $user = $this->hiddenUser();
        SocialiteUser::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => '12345',
            'name' => 'Max Mustermann',
            'email' => $user->email,
        ]);
        Socialite::shouldReceive('driver->stateless->user')->andReturn((new ProviderUser)->map(['id' => '12345']));

        $this->get('/callback/google')->assertRedirect('/#');
        $this->assertAuthenticatedAs($user);

        $this->assertLockedOut($this->get('/'));
    }

    #[Test]
    public function a_hidden_player_is_locked_out_right_after_resetting_the_password(): void
    {
        $user = $this->hiddenUser();

        $this->post(route('password.update'), [
            'token' => Password::createToken($user),
            'email' => $user->email,
            'password' => 'neues-passwort-123',
            'password_confirmation' => 'neues-passwort-123',
        ])->assertRedirect('/home');

        $this->assertLockedOut($this->get('/home'));
    }

    #[Test]
    public function a_hidden_admin_stays_logged_in(): void
    {
        $admin = User::factory()->for(Player::factory()->hidden())->create();

        $this->actingAs($admin)
            ->get(route('rounds.create'))
            ->assertOk();

        $this->assertAuthenticatedAs($admin);
    }

    private function assertLockedOut(TestResponse $response): void
    {
        $response
            ->assertRedirect(route('login'))
            ->assertInertiaFlash('toast', [
                'type' => 'error',
                'message' => 'Dein Konto ist gesperrt. Wende dich bitte an den Administrator.',
            ]);

        $this->assertGuest();
    }

    /**
     * Der erste Benutzer ist der Admin, deshalb legt der Helfer ihn vorab an.
     */
    private function hiddenUser(): User
    {
        User::factory()->create();

        return User::factory()->for(Player::factory()->hidden())->create();
    }
}
