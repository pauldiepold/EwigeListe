<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Meldungen nach Redirects laufen als Inertia-Flash `toast` ({type, message}) und
 * werden zentral als Toast angezeigt; Pages kümmern sich nicht darum.
 */
class FlashToastTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function quick_register_flashes_a_success_toast_onto_the_next_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('register.quick.store'), $this->quickRegisterAttributes())
            ->assertRedirect(route('rounds.create'))
            ->assertInertiaFlash('toast', [
                'type' => 'success',
                'message' => 'Spieler Max Mustermann wurde erfolgreich erstellt!',
            ]);

        $this->assertDatabaseHas('users', ['email' => 'max@example.com']);
    }

    #[Test]
    public function the_toast_arrives_as_flash_data_after_following_the_redirect(): void
    {
        $this->actingAs(User::factory()->create());

        $this->followingRedirects()
            ->post(route('register.quick.store'), $this->quickRegisterAttributes())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Rounds/Create')
                ->hasFlash('toast.type', 'success')
                ->hasFlash('toast.message', 'Spieler Max Mustermann wurde erfolgreich erstellt!')
                ->missing('flash')
            );
    }

    #[Test]
    public function requesting_a_reset_link_flashes_a_success_toast(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionMissing('status')
            ->assertInertiaFlash('toast', [
                'type' => 'success',
                'message' => trans(Password::RESET_LINK_SENT),
            ]);
    }

    #[Test]
    public function resetting_the_password_flashes_a_success_toast(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'neues-passwort-123',
            'password_confirmation' => 'neues-passwort-123',
        ])
            ->assertRedirect('/home')
            ->assertSessionMissing('status')
            ->assertInertiaFlash('toast', [
                'type' => 'success',
                'message' => trans(Password::PASSWORD_RESET),
            ]);
    }

    /**
     * @return array{surname: string, name: string, email: string}
     */
    private function quickRegisterAttributes(): array
    {
        return [
            'surname' => 'Max',
            'name' => 'Mustermann',
            'email' => 'max@example.com',
        ];
    }
}
