<?php

namespace Tests\Feature;

use App\Round;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InertiaShellTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_get_no_user_and_no_player(): void
    {
        $this->get(route('index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('auth.user', null)
                ->where('auth.player', null)
                ->where('navigation.showCurrentRound', false));
    }

    #[Test]
    public function authenticated_users_get_their_user_and_player(): void
    {
        $user = User::factory()->create();
        $player = $user->player;

        $this->actingAs($user)
            ->get(route('index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('auth.user', ['id' => $user->id])
                ->where('auth.player', [
                    'id' => $player->id,
                    'surname' => $player->surname,
                    'name' => $player->name,
                ]));
    }

    #[Test]
    public function the_current_round_is_offered_once_the_player_has_a_round(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page->where('navigation.showCurrentRound', false));

        Round::factory()->create()->players()->attach($user->player, ['index' => 0]);

        $this->actingAs($user)
            ->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page->where('navigation.showCurrentRound', true));
    }

    #[Test]
    public function the_root_view_contains_the_pwa_head(): void
    {
        $this->get(route('index'))
            ->assertOk()
            ->assertSee('rel="manifest"', false)
            ->assertSee('name="theme-color"', false)
            ->assertSee('rel="apple-touch-icon"', false);
    }
}
