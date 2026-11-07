<?php

namespace Tests\Feature;

use App\Models\SocialiteUser;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Laravel\Socialite\Two\User as ProviderUser;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Eine Google- oder Facebook-Identität ohne Konto merkt sich die Session bis zum Login bzw. zur
 * Registrierung. Ihre ID kommt nie aus URL oder Formular, sonst ließe sie sich fälschen (#56).
 */
class SocialiteLoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_unknown_identity_leads_to_the_attach_page(): void
    {
        $this->fakeProviderUser('neu@example.com');

        $this->get(route('auth.socialite.callback', 'google'))
            ->assertRedirect(route('auth.registerOrAttach'));

        $this->get(route('auth.registerOrAttach'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/RegisterOrAttach')
                ->where('existingUserEmail', null)
                ->where('socialiteUser', [
                    'email' => 'neu@example.com',
                    'provider' => 'google',
                    'surname' => 'Max',
                    'name' => 'Mustermann',
                ])
            );
    }

    #[Test]
    public function the_attach_page_offers_login_when_the_email_already_has_an_account(): void
    {
        $user = User::factory()->create();
        $this->rememberUnknownIdentity($user->email);

        $this->get(route('auth.registerOrAttach'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('existingUserEmail', $user->email));
    }

    #[Test]
    public function the_attach_page_redirects_to_login_without_a_remembered_identity(): void
    {
        $this->get(route('auth.registerOrAttach'))->assertRedirect(route('login'));
    }

    #[Test]
    public function the_attach_page_cannot_be_opened_by_id(): void
    {
        $socialiteUser = $this->socialiteUser('fremd@example.com');

        $this->get('login/social/'.$socialiteUser->id)->assertNotFound();
    }

    #[Test]
    public function a_remembered_identity_expires_after_ten_minutes(): void
    {
        $user = User::factory()->create();
        $socialiteUser = $this->rememberUnknownIdentity('neu@example.com');

        $this->travel(11)->minutes();

        $this->get(route('auth.registerOrAttach'))->assertRedirect(route('login'));
        $this->post(route('login'), ['email' => $user->email, 'password' => 'test'])
            ->assertRedirect('/');

        $this->assertNull($socialiteUser->fresh()->user_id);
    }

    #[Test]
    public function logging_in_links_the_remembered_identity(): void
    {
        $user = User::factory()->create();
        $socialiteUser = $this->rememberUnknownIdentity($user->email);

        $this->post(route('login'), ['email' => $user->email, 'password' => 'test'])
            ->assertRedirect('/');

        $this->assertSame($user->id, $socialiteUser->fresh()->user_id);
    }

    #[Test]
    public function logging_in_with_a_forged_socialite_user_id_links_nothing(): void
    {
        $user = User::factory()->create();
        $socialiteUser = $this->socialiteUser('fremd@example.com');

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'test',
            'socialiteUserId' => $socialiteUser->id,
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->assertNull($socialiteUser->fresh()->user_id);
    }

    #[Test]
    public function registering_with_a_remembered_identity_needs_no_captcha_or_password_and_links_it(): void
    {
        $socialiteUser = $this->rememberUnknownIdentity('neu@example.com');

        $this->post(route('register'), [
            'surname' => 'Max',
            'name' => 'Mustermann',
            'email' => 'neu@example.com',
        ])->assertSessionHasNoErrors()->assertRedirect('/');

        $user = User::where('email', 'neu@example.com')->sole();
        $this->assertAuthenticatedAs($user);
        $this->assertSame($user->id, $socialiteUser->fresh()->user_id);
    }

    #[Test]
    public function registering_with_a_forged_socialite_user_id_still_requires_captcha_and_password(): void
    {
        $socialiteUser = $this->socialiteUser('fremd@example.com');

        $this->post(route('register'), [
            'surname' => 'Max',
            'name' => 'Mustermann',
            'email' => 'bot@example.com',
            'socialiteUserId' => $socialiteUser->id,
        ])->assertSessionHasErrors(['password', 'g-recaptcha-response']);

        $this->assertGuest();
        $this->assertNull($socialiteUser->fresh()->user_id);
    }

    #[Test]
    public function a_client_error_retries_the_same_provider(): void
    {
        Socialite::shouldReceive('driver->user')
            ->andThrow(new ClientException('Fehler', new Request('GET', '/'), new Response(400)));

        $this->get(route('auth.socialite.callback', 'google'))
            ->assertRedirect(route('auth.socialite', 'google'));
    }

    #[Test]
    public function an_invalid_oauth_state_returns_to_login_with_a_toast(): void
    {
        Socialite::shouldReceive('driver->user')->andThrow(new InvalidStateException);

        $this->get(route('auth.socialite.callback', 'google'))
            ->assertRedirect(route('login'))
            ->assertInertiaFlash('toast', [
                'type' => 'error',
                'message' => 'Die Anmeldung mit Google ist fehlgeschlagen. Bitte versuche es noch einmal.',
            ]);

        $this->assertGuest();
    }

    private function rememberUnknownIdentity(string $email): SocialiteUser
    {
        $this->fakeProviderUser($email);

        $this->get(route('auth.socialite.callback', 'google'))
            ->assertRedirect(route('auth.registerOrAttach'));

        return SocialiteUser::where('provider_id', '12345')->sole();
    }

    private function fakeProviderUser(string $email): void
    {
        Socialite::shouldReceive('driver->user')->andReturn((new ProviderUser)->map([
            'id' => '12345',
            'name' => 'Max Mustermann',
            'email' => $email,
        ]));
    }

    private function socialiteUser(string $email): SocialiteUser
    {
        return SocialiteUser::create([
            'provider' => 'google',
            'provider_id' => '67890',
            'name' => 'Erika Musterfrau',
            'email' => $email,
        ]);
    }
}
