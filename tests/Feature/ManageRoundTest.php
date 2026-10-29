<?php

namespace Tests\Feature;

use App\Group;
use App\Player;
use App\Round;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
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
                ->where('loggedInPlayerId', $players[0]->id)
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
