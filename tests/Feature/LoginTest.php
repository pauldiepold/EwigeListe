<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_login_page_renders(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Auth/Login'));
    }

    #[Test]
    public function a_user_can_log_in(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'test'])
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function a_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->from(route('login'))
            ->post(route('login'), ['email' => $user->email, 'password' => 'falsch'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function too_many_attempts_return_to_the_form_with_a_toast(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), ['email' => $user->email, 'password' => 'falsch']);
        }

        $this->from(route('login'))
            ->post(route('login'), ['email' => $user->email, 'password' => 'test'])
            ->assertRedirect(route('login'))
            ->assertInertiaFlash('toast', [
                'type' => 'error',
                'message' => 'Zu viele Versuche. Bitte warte kurz und versuche es dann noch einmal.',
            ]);

        $this->assertGuest();
    }

    #[Test]
    public function remember_sets_the_recaller_cookie(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'test', 'remember' => true])
            ->assertCookie(auth()->guard()->getRecallerName());
    }
}
