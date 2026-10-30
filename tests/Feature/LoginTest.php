<?php

namespace Tests\Feature;

use App\SocialiteUser;
use App\User;
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
    public function remember_sets_the_recaller_cookie(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'test', 'remember' => true])
            ->assertCookie(auth()->guard()->getRecallerName());
    }

    #[Test]
    public function logging_in_with_a_socialite_user_id_links_the_account(): void
    {
        $user = User::factory()->create();
        $socialiteUser = $this->socialiteUser($user->email);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'test',
            'socialiteUserId' => $socialiteUser->id,
        ])->assertRedirect('/');

        $this->assertSame($user->id, $socialiteUser->fresh()->user_id);
    }

    #[Test]
    public function the_attach_page_offers_login_when_the_email_already_has_an_account(): void
    {
        $user = User::factory()->create();
        $socialiteUser = $this->socialiteUser($user->email);

        $this->get(route('auth.registerOrAttach', $socialiteUser))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/RegisterOrAttach')
                ->where('existingUserEmail', $user->email)
                ->where('socialiteUser.id', $socialiteUser->id)
            );
    }

    #[Test]
    public function the_attach_page_offers_registration_for_a_new_email(): void
    {
        $socialiteUser = $this->socialiteUser('neu@example.com');

        $this->get(route('auth.registerOrAttach', $socialiteUser))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/RegisterOrAttach')
                ->where('existingUserEmail', null)
                ->where('socialiteUser.email', 'neu@example.com')
                ->where('socialiteUser.surname', 'Max')
                ->where('socialiteUser.name', 'Mustermann')
            );
    }

    private function socialiteUser(string $email): SocialiteUser
    {
        return SocialiteUser::create([
            'provider' => 'google',
            'provider_id' => '12345',
            'name' => 'Max Mustermann',
            'email' => $email,
        ]);
    }
}
