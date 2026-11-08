<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Toast nach dem Reset (und keinen nach der Link-Anforderung) deckt `FlashToastTest` ab.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_forgot_password_page_renders(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/ForgotPassword'));
    }

    #[Test]
    public function requesting_a_reset_link_mails_a_german_link_to_the_reset_page(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user): bool {
            $mail = $notification->toMail($user);

            return $mail->subject === 'Passwort zurücksetzen'
                && $mail->actionText === 'Passwort zurücksetzen'
                && $mail->actionUrl === route('password.reset', ['token' => $notification->token, 'email' => $user->email]);
        });
    }

    #[Test]
    public function requesting_a_reset_link_answers_in_german(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertSessionHas('status', 'Wir haben dir einen Link zum Zurücksetzen des Passworts gesendet.');
    }

    #[Test]
    public function the_reset_page_receives_token_and_email_from_the_link(): void
    {
        $this->get(route('password.reset', ['token' => 'abc', 'email' => 'max@example.com']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/ResetPassword')
                ->where('token', 'abc')
                ->where('email', 'max@example.com')
            );
    }

    #[Test]
    public function resetting_changes_the_password_and_leads_to_the_login(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => Password::createToken($user),
            'email' => $user->email,
            'password' => 'neuesPasswort123',
            'password_confirmation' => 'neuesPasswort123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertTrue(Hash::check('neuesPasswort123', $user->fresh()->password));
    }

    #[Test]
    public function resetting_rejects_a_password_shorter_than_eight_characters(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => Password::createToken($user),
            'email' => $user->email,
            'password' => 'kurz123',
            'password_confirmation' => 'kurz123',
        ])->assertSessionHasErrors(['password' => 'Passwort muss mindestens 8 Zeichen lang sein.']);

        $this->assertTrue(Hash::check('test', $user->fresh()->password));
    }
}
