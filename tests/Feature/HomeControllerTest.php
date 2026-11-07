<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
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
    public function a_current_round_shows_its_players_games_and_last_game(): void
    {
        $this->travelTo(Carbon::parse('2026-10-30 14:00:00'));
        $user = User::factory()->create();
        $this->actingAs($user);

        $round = $this->roundWithGameAt(now()->subHour());
        $earlierGame = Game::factory()->create(['round_id' => $round->id, 'created_at' => now()->subHours(2)]);
        $anna = Player::factory()->create(['surname' => 'Anna']);
        $round->players()->attach($anna, ['index' => 0]);
        $round->players()->attach($user->player, ['index' => 1]);
        $this->scoreGame($round->games->first(), [$anna->id => 3, $user->player->id => -3]);
        $this->scoreGame($earlierGame, [$anna->id => 2, $user->player->id => -2]);
        $this->scoreGame(Game::factory()->create(['created_at' => now()->subWeek()]), [$anna->id => 50]);

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('currentRounds.0', fn (Assert $currentRound) => $currentRound
                    ->where('id', $round->id)
                    ->where('players', [
                        ['id' => $anna->id, 'name' => 'Anna', 'avatarPath' => null, 'points' => 5],
                        ['id' => $user->player->id, 'name' => $user->player->surname, 'avatarPath' => $user->avatar_path, 'points' => -5],
                    ])
                    ->where('gamesCount', 2)
                    ->where('lastGameAt', now()->subHour()->toIso8601String())
                    ->where('isLive', false)
                    ->where('isOwn', true)
                )
            );
    }

    #[Test]
    public function a_current_round_without_the_viewer_is_not_their_own(): void
    {
        $this->actingAs(User::factory()->create());

        $round = $this->roundWithGameAt(now()->subHour());
        $round->players()->attach(Player::factory()->create(), ['index' => 0]);

        $this->get(route('index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('currentRounds.0.isOwn', false)
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

    /**
     * @param  array<int, int>  $pointsByPlayerId
     */
    private function scoreGame(Game $game, array $pointsByPlayerId): void
    {
        foreach ($pointsByPlayerId as $playerId => $points) {
            $game->players()->attach($playerId, [
                'points' => $points,
                'won' => $points > 0,
                'soloist' => false,
                'misplayed' => false,
            ]);
        }
    }
}
