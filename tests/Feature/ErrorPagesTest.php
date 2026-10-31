<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function an_unknown_url_shows_the_error_page_with_navigation(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/gibt-es-nicht')
            ->assertNotFound()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Error')
                ->where('status', 404)
                ->where('auth.user.id', $user->id)
            );
    }

    #[Test]
    public function a_forbidden_page_shows_the_error_page(): void
    {
        User::factory()->create(); // Benutzer 1 ist Admin
        $hidden = Player::factory()->hidden()->create();

        $this->actingAs(User::factory()->create())->get(route('players.show', $hidden))
            ->assertForbidden()
            ->assertInertia(fn (Assert $page) => $page->component('Error')->where('status', 403));
    }

    #[Test]
    public function a_server_error_shows_the_error_page_without_touching_the_database(): void
    {
        config(['app.debug' => false]);
        Route::middleware('web')->get('/test-fehler', fn () => throw new RuntimeException('kaputt'));

        $this->actingAs(User::factory()->create())->get('/test-fehler')
            ->assertInternalServerError()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Error')
                ->where('status', 500)
                ->missing('auth')
                ->missing('navigation')
            );
    }

    #[Test]
    public function a_server_error_keeps_the_debug_page_in_debug_mode(): void
    {
        config(['app.debug' => true]);
        Route::middleware('web')->get('/test-fehler', fn () => throw new RuntimeException('kaputt'));

        $this->get('/test-fehler')
            ->assertInternalServerError()
            ->assertSee('RuntimeException');
    }

    #[Test]
    public function json_requests_keep_their_json_error(): void
    {
        $this->getJson('/gibt-es-nicht')
            ->assertNotFound()
            ->assertJsonStructure(['message']);
    }

    #[Test]
    public function an_expired_session_returns_to_the_form_with_a_toast(): void
    {
        Route::middleware('web')->patch('/test-abgelaufen', fn () => throw new TokenMismatchException);

        $this->from('/login')->patch('/test-abgelaufen')
            ->assertStatus(303)
            ->assertRedirect('/login')
            ->assertInertiaFlash('toast.type', 'error');
    }

    #[Test]
    public function maintenance_mode_shows_a_page_that_does_not_need_built_assets(): void
    {
        Route::get('/test-wartung', fn () => abort(503));

        $this->get('/test-wartung')
            ->assertServiceUnavailable()
            ->assertSee('gleich wieder da')
            ->assertDontSee('/build/', false)
            ->assertDontSee('data-page', false);
    }
}
