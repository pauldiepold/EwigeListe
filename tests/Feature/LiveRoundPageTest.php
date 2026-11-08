<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LiveRoundPageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_redirected_to_the_login(): void
    {
        $round = $this->liveRound($this->users(4));

        $this->get(route('rounds.live', $round))->assertRedirect(route('login'));
    }

    #[Test]
    public function an_offline_round_has_no_live_page(): void
    {
        $users = $this->users(4);
        $round = $this->roundFor($users);

        $this->actingAs($users->first())->get(route('rounds.live', $round))->assertNotFound();
    }

    #[Test]
    public function a_player_of_the_current_game_gets_their_hand(): void
    {
        $users = $this->users(4);
        $round = $this->liveRound($users);
        $round->liveRound->starteNeuesSpiel();

        $this->actingAs($users[2])->get(route('rounds.live', $round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/Show')
                ->where('round.id', $round->id)
                ->where('round.viewerPlayerId', $users[2]->player_id)
                ->has('liveGame.spieler', 4)
                ->where('liveGame.phase', 2)
                ->where('ich.id', $users[2]->player_id)
                ->has('ich.hand', 12)
                ->has('ich.moeglicheVorbehalte')
                ->where('lastLiveGame', null)
            );
    }

    #[Test]
    public function a_player_who_sits_out_gets_no_hand(): void
    {
        $users = $this->users(5);
        $round = $this->liveRound($users);
        $round->liveRound->starteNeuesSpiel();
        $sitsOut = $users[0];

        $this->actingAs($sitsOut)->get(route('rounds.live', $round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('ich', null)
                ->has('liveGame.spieler', 4)
                ->where('round.players.0.isActive', false)
                ->where('round.players.1.isActive', true)
            );
    }

    #[Test]
    public function a_stranger_sees_the_public_game_without_a_hand(): void
    {
        $round = $this->liveRound($this->users(4));
        $round->liveRound->starteNeuesSpiel();

        $this->actingAs(User::factory()->create())->get(route('rounds.live', $round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('ich', null)
                ->has('liveGame.aktuellerStich')
                ->missing('liveGame.spieler0')
            );
    }

    #[Test]
    public function the_page_works_before_the_first_game_with_the_rules_and_the_seat_order(): void
    {
        $users = $this->users(4);
        $round = $this->liveRound($users);

        $this->actingAs($users[1])->get(route('rounds.live', $round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('liveGame', null)
                ->where('lastLiveGame', null)
                ->where('ich', null)
                ->where('round.gamesCount', 0)
                ->where('round.liveRoundId', $round->live_round_id)
                ->where('round.rules.fuchsSticht', true)
                ->where('round.rules.schweinchenTrumpfsolo', false)
                ->where('round.players', fn ($players) => collect($players)->pluck('id')->all() === $users->pluck('player_id')->all())
                ->where('round.players.0.firstName', $users[0]->player->first_name)
                ->where('round.players.0.fullName', $users[0]->player->full_name)
            );
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function roundFor(Collection $users): Round
    {
        $round = Round::factory()->create();
        foreach ($users->values() as $seat => $user) {
            $round->players()->attach($user->player_id, ['index' => $seat]);
        }
        $ewigeListe = Group::findOrFail(Group::EWIGE_LISTE_ID);
        $ewigeListe->addPlayers($users->map(fn (User $user): Player => $user->player));
        $round->groups()->attach($ewigeListe);

        return $round->fresh();
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function liveRound(Collection $users): Round
    {
        $round = $this->roundFor($users);
        $round->createLiveRound();

        return $round->fresh();
    }

    /**
     * @return Collection<int, User>
     */
    private function users(int $count): Collection
    {
        return User::factory()->count($count)->create();
    }
}
