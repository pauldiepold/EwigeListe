<?php

namespace Tests\Feature;

use App\Game;
use App\Player;
use App\Round;
use App\User;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_see_the_home_page_without_current_rounds(): void
    {
        $this->roundWithGameAt(now()->subHour());

        $this->get(route('index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home/Index')
                ->where('currentRounds', [])
            );
    }

    #[Test]
    public function users_see_only_rounds_with_a_game_from_the_last_24_hours(): void
    {
        $this->freezeTime();
        $this->actingAs(User::factory()->create());

        $recent = $this->roundWithGameAt(now()->subHours(23));
        $stale = $this->roundWithGameAt(now()->subHours(25));
        $stale->touch();
        Round::factory()->create();

        $this->get(route('index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Home/Index')
                ->has('currentRounds', 1)
                ->where('currentRounds.0.id', $recent->id)
            );
    }

    #[Test]
    public function current_rounds_are_ordered_by_their_latest_game(): void
    {
        $this->freezeTime();
        $this->actingAs(User::factory()->create());

        $playedRecently = $this->roundWithGameAt(now()->subHour());
        $editedRecently = $this->roundWithGameAt(now()->subHours(3));
        $this->travel(5)->minutes();
        $editedRecently->touch();

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('currentRounds.0.id', $playedRecently->id)
                ->where('currentRounds.1.id', $editedRecently->id)
            );
    }

    #[Test]
    public function a_current_round_shows_its_players_and_last_game(): void
    {
        $this->travelTo(Carbon::parse('2026-10-30 14:00:00'));
        $this->actingAs(User::factory()->create());

        $round = $this->roundWithGameAt(now()->subHour());
        $players = Player::factory()->count(2)->sequence(['surname' => 'Anna'], ['surname' => 'Ben'])->create();
        $players->each(fn (Player $player, int $index) => $round->players()->attach($player, ['index' => $index]));

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('currentRounds.0', fn (Assert $currentRound) => $currentRound
                    ->where('id', $round->id)
                    ->where('players', 'Anna - Ben')
                    ->where('lastGameAt', 'heute  um 13:00 Uhr')
                )
            );
    }

    #[Test]
    public function the_games_chart_is_deferred_and_empty_without_games(): void
    {
        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->missing('homeGamesChart')
                ->loadDeferredProps(fn (Assert $reload) => $reload
                    ->where('homeGamesChart', [
                        'gameDates' => [],
                        'gameCounter' => [],
                    ])
                )
            );
    }

    private function roundWithGameAt(DateTimeInterface $playedAt): Round
    {
        $round = Round::factory()->create();
        Game::factory()->create([
            'round_id' => $round->id,
            'created_at' => $playedAt,
            'updated_at' => $playedAt,
        ]);

        return $round->refresh();
    }
}
