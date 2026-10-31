<?php

namespace Tests\Feature;

use App\Jobs\UpdateGroup;
use App\Jobs\UpdateProfile;
use App\Models\Group;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManageRoundTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_cannot_see_the_create_round_page(): void
    {
        $this->get(route('rounds.create'))->assertRedirect('/login');
    }

    #[Test]
    public function guests_cannot_store_a_round(): void
    {
        $this->post(route('rounds.store'), [])->assertRedirect('/login');

        $this->assertDatabaseCount('rounds', 0);
    }

    #[Test]
    public function a_user_sees_all_visible_players_with_their_lists(): void
    {
        $players = $this->actingAsRoundPlayers(2);
        $hiddenPlayer = Player::factory()->hidden()->create();

        $list = Group::factory()->create();
        $list->addPlayer($players[0]);
        $players[0]->profiles()->where('group_id', $list->id)->update(['default' => true]);

        $this->get(route('rounds.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Rounds/Create')
                ->where('auth.player.id', $players[0]->id)
                ->where('players', fn ($props) => collect($props)->pluck('id')->intersect($players->pluck('id'))->count() === 2
                    && collect($props)->doesntContain('id', $hiddenPlayer->id))
                ->where('players', function ($props) use ($players, $list) {
                    $own = collect($props)->firstWhere('id', $players[0]->id);

                    return collect($own['groups'])->contains('id', $list->id)
                        && collect($own['profiles'])->contains(
                            fn ($profile) => $profile['group_id'] === $list->id && $profile['default'] === true
                        );
                })
            );
    }

    #[Test]
    public function a_user_can_create_a_round_with_players_in_the_chosen_order(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $order = [$players[2]->id, $players[0]->id, $players[3]->id, $players[1]->id];

        $response = $this->post(route('rounds.store'), [
            'players' => $order,
            'groups' => [Group::EWIGE_LISTE_ID],
            'liveGame' => false,
        ]);

        $round = Round::sole();
        $response->assertRedirect($round->path());
        $this->assertSame($order, $round->players->pluck('id')->all());
        $this->assertNull($round->liveRound);
    }

    #[Test]
    public function a_round_counts_for_the_chosen_lists_and_always_for_the_ewige_liste(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $list = Group::factory()->create();
        $list->addPlayer($players[0]);

        $this->post(route('rounds.store'), [
            'players' => $players->pluck('id')->all(),
            'groups' => [$list->id],
            'liveGame' => false,
        ])->assertSessionHasNoErrors();

        $round = Round::sole();
        $this->assertEqualsCanonicalizing(
            [Group::EWIGE_LISTE_ID, $list->id],
            $round->groups->pluck('id')->all()
        );
        $this->assertEqualsCanonicalizing(
            $players->pluck('id')->all(),
            $list->fresh()->players->pluck('id')->all()
        );
    }

    #[Test]
    public function a_round_cannot_be_added_to_a_closed_list(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $closedList = Group::factory()->closed()->create();
        $closedList->addPlayer($players[0]);

        $this->post(route('rounds.store'), [
            'players' => $players->pluck('id')->all(),
            'groups' => [Group::EWIGE_LISTE_ID, $closedList->id],
            'liveGame' => false,
        ])->assertSessionHasErrors(['groups' => "Die Liste {$closedList->name} ist geschlossen."]);

        $this->assertDatabaseCount('rounds', 0);
    }

    #[Test]
    public function a_round_cannot_be_added_to_a_list_without_any_of_its_players(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $foreignList = Group::factory()->create();

        $this->post(route('rounds.store'), [
            'players' => $players->pluck('id')->all(),
            'groups' => [Group::EWIGE_LISTE_ID, $foreignList->id],
            'liveGame' => false,
        ])->assertSessionHasErrors(['groups' => "Keiner der Spieler ist Mitglied der Liste {$foreignList->name}."]);

        $this->assertDatabaseCount('rounds', 0);
    }

    #[Test]
    #[DataProvider('invalidPlayerCounts')]
    public function a_round_needs_between_four_and_seven_players(int $playerCount): void
    {
        $players = $this->actingAsRoundPlayers($playerCount);

        $this->post(route('rounds.store'), [
            'players' => $players->pluck('id')->all(),
            'groups' => [Group::EWIGE_LISTE_ID],
            'liveGame' => false,
        ])->assertSessionHasErrors(['players' => 'Eine Runde braucht 4 bis 7 Spieler.']);

        $this->assertDatabaseCount('rounds', 0);
    }

    /**
     * @return array<string, array{int}>
     */
    public static function invalidPlayerCounts(): array
    {
        return [
            'drei Spieler' => [3],
            'acht Spieler' => [8],
        ];
    }

    #[Test]
    public function a_player_of_the_round_can_change_its_lists_and_its_players_become_members(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $list = Group::factory()->create();
        $list->addPlayer($players[1]);

        $this->patch(route('rounds.update', $round), ['groups' => [Group::EWIGE_LISTE_ID, $list->id]])
            ->assertRedirect(route('rounds.show', $round))
            ->assertSessionHasNoErrors();

        $this->assertEqualsCanonicalizing([Group::EWIGE_LISTE_ID, $list->id], $round->groups()->pluck('groups.id')->all());
        $this->assertEqualsCanonicalizing($players->pluck('id')->all(), $list->fresh()->players->pluck('id')->all());
    }

    #[Test]
    public function someone_outside_the_round_cannot_change_its_lists(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $this->actingAs(User::factory()->create());

        $this->patch(route('rounds.update', $round), ['groups' => [Group::EWIGE_LISTE_ID]])->assertForbidden();
    }

    #[Test]
    public function a_round_always_stays_on_the_ewige_liste_and_on_its_closed_lists(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $closedList = Group::factory()->closed()->create();
        $closedList->addPlayers($players);
        $round->groups()->attach($closedList);
        $openList = Group::factory()->create();
        $openList->addPlayer($players[0]);

        $this->patch(route('rounds.update', $round), ['groups' => [$openList->id]])->assertSessionHasNoErrors();

        $this->assertEqualsCanonicalizing(
            [Group::EWIGE_LISTE_ID, $closedList->id, $openList->id],
            $round->groups()->pluck('groups.id')->all()
        );
    }

    #[Test]
    public function an_existing_round_cannot_be_added_to_a_closed_list(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $closedList = Group::factory()->closed()->create();
        $closedList->addPlayer($players[0]);

        $this->patch(route('rounds.update', $round), ['groups' => [$closedList->id]])
            ->assertSessionHasErrors(['groups' => "Die Liste {$closedList->name} ist geschlossen."]);

        $this->assertSame([Group::EWIGE_LISTE_ID], $round->groups()->pluck('groups.id')->all());
    }

    #[Test]
    public function an_existing_round_cannot_be_added_to_a_list_without_any_of_its_players(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $foreignList = Group::factory()->create();

        $this->patch(route('rounds.update', $round), ['groups' => [$foreignList->id]])
            ->assertSessionHasErrors(['groups' => "Keiner der Spieler ist Mitglied der Liste {$foreignList->name}."]);

        $this->assertSame([Group::EWIGE_LISTE_ID], $round->groups()->pluck('groups.id')->all());
    }

    #[Test]
    public function changing_lists_does_not_change_the_members_of_lists_the_round_stays_on(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $closedList = Group::factory()->closed()->create();
        $closedList->addPlayer($players[0]);
        $round->groups()->attach($closedList);
        $openList = Group::factory()->create();
        $openList->addPlayer($players[0]);
        $round->groups()->attach($openList);

        $this->patch(route('rounds.update', $round), ['groups' => [$openList->id]])->assertSessionHasNoErrors();

        $this->assertSame([$players[0]->id], $closedList->fresh()->players->pluck('id')->all());
        $this->assertSame([$players[0]->id], $openList->fresh()->players->pluck('id')->all());
    }

    #[Test]
    public function lists_a_round_is_removed_from_are_recalculated_as_well(): void
    {
        Bus::fake([UpdateGroup::class, UpdateProfile::class]);
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $removedList = Group::factory()->create();
        $removedList->addPlayers($players);
        $round->groups()->attach($removedList);

        $this->patch(route('rounds.update', $round), ['groups' => [Group::EWIGE_LISTE_ID]])->assertSessionHasNoErrors();

        $this->assertSame([Group::EWIGE_LISTE_ID], $round->groups()->pluck('groups.id')->all());
        Bus::assertDispatched(UpdateGroup::class, 2);
        Bus::assertDispatched(UpdateProfile::class, 8);
    }

    #[Test]
    public function a_round_without_games_can_be_deleted(): void
    {
        $round = $this->roundWith($this->actingAsRoundPlayers(4));

        $this->delete(route('rounds.destroy', $round))
            ->assertRedirect(route('rounds.create'))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Die Runde wurde gelöscht.']);

        $this->assertDatabaseCount('rounds', 0);
    }

    #[Test]
    public function a_round_with_games_cannot_be_deleted(): void
    {
        $players = $this->actingAsRoundPlayers(4);
        $round = $this->roundWith($players);
        $round->addNewGame([$players[0]->id, $players[1]->id], 1);

        $this->from(route('rounds.show', $round))
            ->delete(route('rounds.destroy', $round))
            ->assertRedirect(route('rounds.show', $round))
            ->assertInertiaFlash('toast', ['type' => 'error', 'message' => 'Du kannst nur eine Runde ohne Spiele löschen!']);

        $this->assertDatabaseCount('rounds', 1);
    }

    #[Test]
    public function someone_outside_the_round_cannot_delete_it(): void
    {
        $round = $this->roundWith($this->actingAsRoundPlayers(4));
        $this->actingAs(User::factory()->create());

        $this->delete(route('rounds.destroy', $round))->assertForbidden();

        $this->assertDatabaseCount('rounds', 1);
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
        $round->groups()->attach(Group::EWIGE_LISTE_ID);

        return $round->fresh();
    }

    /**
     * Meldet einen Benutzer an und liefert seinen Spieler (Index 0) plus weitere Spieler.
     * Echte Spieler sind immer Mitglied der Ewigen Liste.
     *
     * @return Collection<int, Player>
     */
    private function actingAsRoundPlayers(int $count): Collection
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $players = collect([$user->player])->concat(Player::factory()->count($count - 1)->create());
        Group::findOrFail(Group::EWIGE_LISTE_ID)->addPlayers($players);

        return $players->values();
    }
}
