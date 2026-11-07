<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Group;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoundShowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->viewer = User::factory()->create();
        $this->actingAs($this->viewer);
    }

    #[Test]
    public function guests_are_redirected_to_the_login(): void
    {
        auth()->logout();
        $round = $this->roundWithPlayers($this->otherPlayers(4));

        $this->get(route('rounds.show', $round))->assertRedirect(route('login'));
    }

    #[Test]
    public function the_table_shows_running_totals_and_who_won(): void
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);
        $ids = $players->pluck('id')->all();

        $round->addNewGame([$ids[0], $ids[1]], 2);
        $round->addNewGame([$ids[0], $ids[2]], 3);

        $this->get(route('rounds.show', $round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Rounds/Show')
                ->where('round.id', $round->id)
                ->has('round.games', 2)
                ->where('round.games.0.points', 2)
                ->where('round.games.0.scores', [
                    ['points' => 2, 'won' => true],
                    ['points' => 2, 'won' => true],
                    ['points' => -2, 'won' => false],
                    ['points' => -2, 'won' => false],
                ])
                ->where('round.games.1.scores', [
                    ['points' => 5, 'won' => true],
                    ['points' => -1, 'won' => false],
                    ['points' => 1, 'won' => true],
                    ['points' => -5, 'won' => false],
                ])
            );
    }

    #[Test]
    public function a_player_who_sits_out_has_no_score_in_that_game(): void
    {
        $players = $this->otherPlayers(5);
        $round = $this->roundWithPlayers($players);
        $ids = $players->pluck('id')->all();

        $round->addNewGame([$ids[1], $ids[2]], 1);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.games.0.scores.0', ['points' => null, 'won' => false])
                ->where('round.games.0.scores.1', ['points' => 1, 'won' => true])
            );
    }

    #[Test]
    public function solos_and_misplays_do_not_move_the_dealer_on_and_a_full_cycle_ends_with_a_marker(): void
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);
        $ids = $players->pluck('id')->all();

        foreach (range(1, 3) as $ignored) {
            $round->addNewGame([$ids[0], $ids[1]], 1);
        }
        $round->addNewGame([$ids[0]], 1);
        $round->addNewGame([$ids[0], $ids[1], $ids[2]], 2, true);
        $round->addNewGame([$ids[0], $ids[1]], 1);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.games.2.endsDealerCycle', false)
                ->where('round.games.3.isSolo', true)
                ->where('round.games.3.endsDealerCycle', false)
                ->where('round.games.4.isMisplay', true)
                ->where('round.games.4.endsDealerCycle', false)
                ->where('round.games.5.endsDealerCycle', true)
                ->where('round.players.0.isDealer', true)
                ->where('round.players.3.isDealer', false)
            );
    }

    #[Test]
    public function the_end_of_a_dealer_cycle_follows_from_the_games_played(): void
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);
        $ids = $players->pluck('id')->all();

        foreach (range(1, 4) as $ignored) {
            $round->addNewGame([$ids[0], $ids[1]], 1);
        }
        Game::query()->update(['dealerIndex' => 0]);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.games.2.endsDealerCycle', false)
                ->where('round.games.3.endsDealerCycle', true)
            );
    }

    #[Test]
    public function only_players_of_the_round_may_change_it(): void
    {
        $others = $this->roundWithPlayers($this->otherPlayers(4));
        $own = $this->roundWithPlayers($this->otherPlayers(3)->prepend($this->viewer->player));

        $this->get(route('rounds.show', $others))
            ->assertInertia(fn (Assert $page) => $page->where('round.canUpdate', false));
        $this->get(route('rounds.show', $own))
            ->assertInertia(fn (Assert $page) => $page->where('round.canUpdate', true));
    }

    #[Test]
    public function profiles_the_viewer_may_not_open_have_no_link(): void
    {
        $hidden = Player::factory()->hidden()->create();
        $visible = Player::factory()->create();
        $round = $this->roundWithPlayers($this->otherPlayers(2)->prepend($visible)->prepend($hidden));

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.players.0.path', null)
                ->where('round.players.1.path', $visible->path())
            );

        $this->actingAs($this->admin)->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page->where('round.players.0.path', $hidden->path()));
    }

    #[Test]
    public function a_live_round_is_marked_as_such(): void
    {
        $round = $this->roundWithPlayers($this->otherPlayers(4));
        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page->where('round.isLive', false));

        $round->createLiveRound();

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page->where('round.isLive', true));
    }

    /**
     * @param  list<int>  $expectedSeats
     */
    #[Test]
    #[DataProvider('seatOrders')]
    public function the_active_players_are_ordered_from_the_seat_of_the_viewer(int $playerCount, int $viewerSeat, array $expectedSeats): void
    {
        $players = $this->otherPlayers($playerCount - 1)->values();
        $players->splice($viewerSeat, 0, [$this->viewer->player]);
        $round = $this->roundWithPlayers($players);

        $expectedIds = collect($expectedSeats)->map(fn (int $seat) => $players[$seat]->id)->all();

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.activePlayers', fn ($active) => collect($active)->pluck('id')->all() === $expectedIds)
            );
    }

    /**
     * Ohne Spiele gibt Sitz 0. Bei 5 Spielern setzt der Geber aus, bei 6 auch Sitz 3, bei 7 auch die Sitze 2 und 4.
     * Der nächste Sitz sitzt links vom Betrachter; wer aussetzt, beginnt beim nächsten aktiven Sitz.
     *
     * @return array<string, array{int, int, list<int>}>
     */
    public static function seatOrders(): array
    {
        return [
            '4 Spieler, aktiv' => [4, 2, [2, 3, 0, 1]],
            '5 Spieler, aktiv' => [5, 2, [2, 3, 4, 1]],
            '5 Spieler, setzt aus' => [5, 0, [1, 2, 3, 4]],
            '6 Spieler, setzt aus als Geber' => [6, 0, [1, 2, 4, 5]],
            '6 Spieler, setzt aus gegenüber' => [6, 3, [4, 5, 1, 2]],
            '7 Spieler, setzt aus' => [7, 2, [3, 5, 6, 1]],
            '7 Spieler, nächster Sitz setzt auch aus' => [7, 4, [5, 6, 1, 3]],
        ];
    }

    #[Test]
    public function someone_outside_the_round_sees_the_active_players_in_seat_order(): void
    {
        $players = $this->otherPlayers(5);
        $round = $this->roundWithPlayers($players);

        $expectedIds = $players->slice(1)->pluck('id')->values()->all();

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.activePlayers', fn ($active) => collect($active)->pluck('id')->all() === $expectedIds)
            );
    }

    #[Test]
    public function the_average_game_duration_counts_up_to_now_while_the_round_is_running(): void
    {
        Carbon::setTestNow('2026-10-31 20:00:00');
        $round = $this->roundWithGamesAt(['19:00:00', '19:20:00', '19:40:00', '19:50:00']);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.info.averageGameDuration', '15 Minuten und 0 Sekunden')
            );
    }

    #[Test]
    public function the_average_game_duration_ends_with_the_last_game_after_half_an_hour(): void
    {
        Carbon::setTestNow('2026-10-31 23:00:00');
        $round = $this->roundWithGamesAt(['19:00:00', '19:20:00', '19:40:00', '20:00:00']);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.info.averageGameDuration', '20 Minuten und 0 Sekunden')
            );
    }

    /**
     * @param  list<string>  $times
     */
    #[Test]
    #[DataProvider('implausibleDurations')]
    public function an_implausible_average_game_duration_is_not_shown(array $times): void
    {
        Carbon::setTestNow('2026-10-31 23:00:00');
        $round = $this->roundWithGamesAt($times);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page->where('round.info.averageGameDuration', null));
    }

    /**
     * @return array<string, array{list<string>}>
     */
    public static function implausibleDurations(): array
    {
        return [
            'weniger als 4 Spiele' => [['19:00:00', '19:20:00', '19:40:00']],
            'höchstens 2 Minuten pro Spiel' => [['19:00:00', '19:01:00', '19:02:00', '19:03:00']],
            'mindestens 40 Minuten pro Spiel' => [['16:00:00', '17:00:00', '18:00:00', '19:00:00']],
        ];
    }

    #[Test]
    public function the_info_names_who_started_the_round_and_who_entered_the_last_game(): void
    {
        $starter = Player::factory()->create(['surname' => 'Anna']);
        $round = $this->roundWithPlayers($this->otherPlayers(4));
        $round->created_by = $starter->id;
        $round->save();

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.info.startedBy', 'Anna')
                ->where('round.info.lastGameBy', null)
            );

        $round->addNewGame([$round->players[0]->id, $round->players[1]->id], 1);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.info.lastGameBy', $this->viewer->player->surname)
                ->where('round.lastGameId', $round->games()->sole()->id)
            );
    }

    #[Test]
    public function the_last_game_is_prepared_for_editing_with_the_players_of_its_time(): void
    {
        $players = $this->otherPlayers(4)->values();
        $players->splice(2, 0, [$this->viewer->player]);
        $round = $this->roundWithPlayers($players);
        $round->addNewGame([$players[1]->id, $players[2]->id], 1);
        $game = $round->addNewGame([$players[4]->id, $players[0]->id], 3);

        // Sitz 1 gab im zweiten Spiel und setzte aus; die aktiven Spieler des nächsten Spiels sind andere.
        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.lastGame.id', $game->id)
                ->where('round.lastGame.points', 3)
                ->where('round.lastGame.isMisplay', false)
                ->where('round.lastGame.winnerIds', fn ($ids) => collect($ids)->sort()->values()->all() === collect([$players[0]->id, $players[4]->id])->sort()->values()->all())
                ->where('round.lastGame.viewerIsActive', true)
                ->where('round.lastGame.activePlayers', fn ($active) => collect($active)->pluck('id')->all() === [$players[2]->id, $players[3]->id, $players[4]->id, $players[0]->id])
            );
    }

    #[Test]
    public function the_last_game_signature_changes_when_the_game_is_edited(): void
    {
        $players = $this->otherPlayers(3)->push($this->viewer->player);
        $round = $this->roundWithPlayers($players);
        $game = $round->addNewGame([$players[0]->id, $players[1]->id], 1);

        $signature = fn (): string => $this->get(route('rounds.show', $round))->viewData('page')['props']['round']['lastGame']['signature'];
        $before = $signature();

        $round->updateGame($game, [$players[0]->id, $players[1]->id], 2);

        $this->assertNotSame($before, $signature());
    }

    #[Test]
    public function someone_who_cannot_change_the_games_gets_no_last_game_to_edit(): void
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);
        $round->addNewGame([$players[0]->id, $players[1]->id], 1);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page->where('round.lastGame', null));
    }

    #[Test]
    public function a_round_without_games_has_no_last_game(): void
    {
        $round = $this->roundWithPlayers($this->otherPlayers(4));

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page->where('round.lastGame', null));
    }

    #[Test]
    public function a_game_without_a_known_creator_does_not_break_the_page(): void
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);
        $game = $round->addNewGame([$players[0]->id, $players[1]->id], 1);
        Game::whereKey($game->id)->update(['created_by' => null]);

        $this->get(route('rounds.show', $round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('round.info.lastGameBy', null));
    }

    #[Test]
    public function the_chart_is_only_offered_from_four_games_and_loads_on_request(): void
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);
        $ids = $players->pluck('id')->all();

        foreach (range(1, 3) as $ignored) {
            $round->addNewGame([$ids[0], $ids[1]], 1);
        }

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.hasChart', false)
                ->missing('roundChart')
            );

        $round->addNewGame([$ids[0], $ids[2]], 2);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.hasChart', true)
                ->missing('roundChart')
                ->reloadOnly('roundChart', fn (Assert $reload) => $reload
                    ->where('roundChart.names', $players->pluck('surname')->all())
                    ->where('roundChart.points.0', [0, 1, 2, 3, 5])
                    ->where('roundChart.points.3', [0, -1, -2, -3, -5])
                )
            );
    }

    #[Test]
    public function the_chart_keeps_the_score_of_a_player_who_sits_out(): void
    {
        $players = $this->otherPlayers(5);
        $round = $this->roundWithPlayers($players);
        $ids = $players->pluck('id')->all();

        foreach (range(1, 4) as $ignored) {
            $round->addNewGame([$ids[1], $ids[2]], 1);
        }

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->reloadOnly('roundChart', fn (Assert $reload) => $reload
                    ->where('roundChart.points.0', [0, 0, -1, -2, -3])
                )
            );
    }

    #[Test]
    public function lists_of_the_round_are_shown_even_when_closed_and_foreign_closed_lists_are_not_offered(): void
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);

        $attachedClosed = Group::factory()->create(['name' => 'Urlaub 2024']);
        $attachedClosed->addPlayers($players);
        $round->groups()->attach($attachedClosed);
        $attachedClosed->closed = true;
        $attachedClosed->save();

        $otherClosed = Group::factory()->closed()->create(['name' => 'Urlaub 2023']);
        $otherClosed->addPlayer($players[0]);

        $open = Group::factory()->create(['name' => 'Stammtisch']);
        $open->addPlayer($players[0]);

        Group::factory()->create(['name' => 'Fremde Liste']);

        $this->get(route('rounds.show', $round))
            ->assertInertia(fn (Assert $page) => $page
                ->where('round.lists', fn ($lists) => collect($lists)->map(fn ($list) => [$list['name'], $list['selected'], $list['locked']])->all() === [
                    ['Ewige Liste', true, true],
                    ['Stammtisch', false, false],
                    ['Urlaub 2024', true, true],
                ])
            );
    }

    #[Test]
    public function the_current_round_redirects_to_the_latest_own_round(): void
    {
        $older = $this->roundWithPlayers($this->otherPlayers(3)->prepend($this->viewer->player));
        $older->created_at = now()->subDay();
        $older->save();
        $latest = $this->roundWithPlayers($this->otherPlayers(3)->prepend($this->viewer->player));
        $this->roundWithPlayers($this->otherPlayers(4));

        $this->get(route('rounds.current'))->assertRedirect(route('rounds.show', $latest));
    }

    #[Test]
    public function without_an_own_round_the_current_round_leads_to_creating_one(): void
    {
        $this->get(route('rounds.current'))->assertRedirect(route('rounds.create'));
    }

    /**
     * @return Collection<int, Player>
     */
    private function otherPlayers(int $count): Collection
    {
        return User::factory()->count($count)->create()->map(fn (User $user): Player => $user->player)->values();
    }

    /**
     * @param  Collection<int, Player>  $players
     */
    private function roundWithPlayers(Collection $players): Round
    {
        $round = Round::factory()->create();
        foreach ($players->values() as $seat => $player) {
            $round->players()->attach($player->id, ['index' => $seat]);
        }
        $ewigeListe = Group::findOrFail(Group::EWIGE_LISTE_ID);
        $ewigeListe->addPlayers($players);
        $round->groups()->attach($ewigeListe);

        return $round->fresh();
    }

    /**
     * @param  list<string>  $times
     */
    private function roundWithGamesAt(array $times): Round
    {
        $players = $this->otherPlayers(4);
        $round = $this->roundWithPlayers($players);
        $ids = $players->pluck('id')->all();

        foreach ($times as $time) {
            $game = $round->addNewGame([$ids[0], $ids[1]], 1);
            Game::whereKey($game->id)->update(['created_at' => "2026-10-31 {$time}"]);
        }

        return $round->fresh();
    }
}
