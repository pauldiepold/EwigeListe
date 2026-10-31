<?php

namespace Tests\Feature;

use App\Events\RoundUpdated;
use App\Game;
use App\Group;
use App\Player;
use App\Round;
use App\User;
use Illuminate\Broadcasting\Broadcasters\NullBroadcaster;
use Illuminate\Broadcasting\BroadcastException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManageGameTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    /** @var Collection<int, Player> */
    private Collection $players;

    private Round $round;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);

        $this->players = $this->seatedPlayers(5);
        $this->round = $this->roundWith($this->players);
    }

    #[Test]
    public function a_player_of_the_round_can_enter_a_game(): void
    {
        Event::fake([RoundUpdated::class]);

        $this->post(route('games.store', $this->round), $this->validGame())
            ->assertRedirect(route('rounds.show', $this->round))
            ->assertSessionHasNoErrors();

        $game = Game::sole();
        $this->assertSame(3, (int) $game->points);
        $this->assertSame(3, (int) $game->players()->where('player_id', $this->players[1]->id)->sole()->pivot->points);
        Event::assertDispatched(RoundUpdated::class, fn (RoundUpdated $event): bool => $event->roundID === $this->round->id);
    }

    #[Test]
    public function someone_outside_the_round_cannot_enter_a_game(): void
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('games.store', $this->round), $this->validGame())->assertForbidden();

        $this->assertDatabaseCount('games', 0);
    }

    #[Test]
    public function a_winner_who_sits_out_is_rejected(): void
    {
        $sittingOut = $this->players[0]->id;

        $this->post(route('games.store', $this->round), $this->validGame(['winners' => [$sittingOut, $this->players[1]->id]]))
            ->assertSessionHasErrors(['winners' => 'Gewinnen kann nur, wer in diesem Spiel nicht aussetzt!']);

        $this->assertDatabaseCount('games', 0);
    }

    #[Test]
    public function a_game_needs_winners(): void
    {
        $this->post(route('games.store', $this->round), $this->validGame(['winners' => []]))
            ->assertSessionHasErrors(['winners' => 'Es muss mindestens ein Gewinner ausgewählt werden!']);

        $this->assertDatabaseCount('games', 0);
    }

    #[Test]
    public function a_game_needs_points_within_the_allowed_range(): void
    {
        $this->post(route('games.store', $this->round), $this->validGame(['points' => 17]))
            ->assertSessionHasErrors('points');

        $this->assertDatabaseCount('games', 0);
    }

    #[Test]
    public function a_misplay_must_be_entered_as_a_lost_solo(): void
    {
        $this->post(route('games.store', $this->round), $this->validGame(['misplayed' => true]))
            ->assertSessionHasErrors(['misplayed' => 'Wenn sich jemand vergibt, muss dies als verlorenes Solo eingetragen werden!']);

        $this->assertDatabaseCount('games', 0);
    }

    #[Test]
    public function a_misplay_costs_at_least_two_points(): void
    {
        $this->post(route('games.store', $this->round), $this->validGame([
            'winners' => [$this->players[1]->id, $this->players[2]->id, $this->players[3]->id],
            'points' => 1,
            'misplayed' => true,
        ]))->assertSessionHasErrors(['points' => 'Wenn sich jemand vergibt, verliert er mit mindestens 2 Punkten!']);

        $this->assertDatabaseCount('games', 0);
    }

    #[Test]
    public function a_misplay_without_winners_is_a_validation_error_and_no_crash(): void
    {
        $this->post(route('games.store', $this->round), ['points' => 2, 'misplayed' => true])
            ->assertSessionHasErrors('winners');
    }

    #[Test]
    public function games_of_a_live_round_cannot_be_entered_by_hand(): void
    {
        $this->round->createLiveRound();

        $this->from(route('rounds.show', $this->round))
            ->post(route('games.store', $this->round), $this->validGame())
            ->assertRedirect(route('rounds.show', $this->round))
            ->assertInertiaFlash('toast', ['type' => 'error', 'message' => 'In einer Live-Runde werden Spiele automatisch eingetragen.']);

        $this->assertDatabaseCount('games', 0);
    }

    #[Test]
    public function the_last_game_can_be_deleted(): void
    {
        Event::fake([RoundUpdated::class]);
        $game = $this->round->addNewGame([$this->players[1]->id, $this->players[2]->id], 1);

        $this->delete(route('games.destroy', $game))
            ->assertRedirect(route('rounds.show', $this->round));

        $this->assertDatabaseCount('games', 0);
        Event::assertDispatched(RoundUpdated::class);
    }

    #[Test]
    public function an_earlier_game_cannot_be_deleted(): void
    {
        $earlier = $this->round->addNewGame([$this->players[1]->id, $this->players[2]->id], 1);
        $this->round->addNewGame([$this->players[2]->id, $this->players[3]->id], 1);

        $this->from(route('rounds.show', $this->round))
            ->delete(route('games.destroy', $earlier))
            ->assertRedirect(route('rounds.show', $this->round))
            ->assertInertiaFlash('toast', ['type' => 'error', 'message' => 'Du kannst nur das letzte Spiel einer Runde löschen!']);

        $this->assertDatabaseCount('games', 2);
    }

    #[Test]
    public function someone_outside_the_round_cannot_delete_a_game(): void
    {
        $game = $this->round->addNewGame([$this->players[1]->id, $this->players[2]->id], 1);
        $this->actingAs(User::factory()->create());

        $this->delete(route('games.destroy', $game))->assertForbidden();

        $this->assertDatabaseCount('games', 1);
    }

    #[Test]
    public function games_of_a_live_round_cannot_be_deleted(): void
    {
        $game = $this->round->addNewGame([$this->players[1]->id, $this->players[2]->id], 1);
        $this->round->createLiveRound();

        $this->from(route('rounds.show', $this->round))
            ->delete(route('games.destroy', $game))
            ->assertInertiaFlash('toast', ['type' => 'error', 'message' => 'In einer Live-Runde werden Spiele automatisch eingetragen.']);

        $this->assertDatabaseCount('games', 1);
    }

    #[Test]
    public function a_failing_push_does_not_fail_entering_the_game(): void
    {
        Broadcast::extend('failing', fn () => new class extends NullBroadcaster
        {
            public function broadcast(array $channels, $event, array $payload = []): void
            {
                throw new BroadcastException('Pusher nicht erreichbar');
            }
        });
        config([
            'broadcasting.default' => 'failing',
            'broadcasting.connections.failing' => ['driver' => 'failing'],
        ]);

        $this->post(route('games.store', $this->round), $this->validGame())
            ->assertRedirect(route('rounds.show', $this->round));

        $this->assertDatabaseCount('games', 1);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validGame(array $overrides = []): array
    {
        return [
            'winners' => [$this->players[1]->id, $this->players[2]->id],
            'points' => 3,
            'misplayed' => false,
            ...$overrides,
        ];
    }

    /**
     * Der angemeldete Benutzer sitzt auf Sitz 2 und ist damit im ersten Spiel aktiv.
     *
     * @return Collection<int, Player>
     */
    private function seatedPlayers(int $count): Collection
    {
        $others = User::factory()->count($count - 1)->create()->map(fn (User $user): Player => $user->player)->values();
        $others->splice(2, 0, [$this->user->player]);

        return $others;
    }

    /**
     * @param  Collection<int, Player>  $players
     */
    private function roundWith(Collection $players): Round
    {
        $round = Round::factory()->create();
        foreach ($players as $seat => $player) {
            $round->players()->attach($player->id, ['index' => $seat]);
        }
        $ewigeListe = Group::findOrFail(Group::EWIGE_LISTE_ID);
        $ewigeListe->addPlayers($players);
        $round->groups()->attach($ewigeListe);

        return $round->fresh();
    }
}
