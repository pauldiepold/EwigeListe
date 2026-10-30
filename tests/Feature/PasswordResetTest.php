<?php

namespace Tests\Feature;

use App\Notifications\MyOwnResetPassword;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Toast-Meldungen nach Link-Anforderung und Reset deckt `FlashToastTest` ab.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function requesting_a_reset_link_sends_the_notification(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->get(route('password.request'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/ForgotPassword'));

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, MyOwnResetPassword::class);
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
    public function resetting_changes_the_password(): void
    {
        $user = User::factory()->create();

        $this->post(route('password.update'), [
            'token' => Password::createToken($user),
            'email' => $user->email,
            'password' => 'neuesPasswort123',
            'password_confirmation' => 'neuesPasswort123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('neuesPasswort123', $user->fresh()->password));
    }
}
