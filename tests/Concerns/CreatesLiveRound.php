<?php

namespace Tests\Concerns;

use App\Models\Group;
use App\Models\LiveGame;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Gemeinsames Setup der Live-Feature-Tests: vier Spieler mit Benutzern, eine Runde in der Ewigen Liste
 * mit Live-Runde und Hilfen, um Spiele mit fester Kartenverteilung zu starten.
 */
trait CreatesLiveRound
{
    /** @var Collection<int, User> */
    private Collection $users;

    private Round $round;

    protected function setUpLiveRound(): void
    {
        $this->users = Player::factory()->count(4)->create()->map(
            fn (Player $player): User => User::factory()->create(['id' => $player->id, 'player_id' => $player->id]),
        );
        $this->round = Round::factory()->create();
        foreach ($this->users->values() as $seat => $user) {
            $this->round->players()->attach($user->player_id, ['index' => $seat]);
        }
        $ewigeListe = Group::findOrFail(Group::EWIGE_LISTE_ID);
        $ewigeListe->addPlayers($this->users->map(fn (User $user): Player => $user->player));
        $this->round->groups()->attach($ewigeListe);
        $this->round->createLiveRound();

        $this->beforeApplicationDestroyed(static fn () => mt_srand());
    }

    /**
     * Die Kartenverteilung hängt am Seed (`shuffle()` in `Deck`).
     */
    protected function startGameWithSeed(int $seed): LiveGame
    {
        mt_srand($seed);
        $this->round->fresh()->liveRound->starteNeuesSpiel();

        return $this->currentGame();
    }

    protected function currentGame(): LiveGame
    {
        return LiveGame::where('live_round_id', $this->round->fresh()->live_round_id)->latest('id')->firstOrFail();
    }

    protected function userOfPlayer(int $playerId): User
    {
        return $this->users->firstWhere('player_id', $playerId);
    }

    protected function userWhoseTurnItIs(): User
    {
        return $this->userOfPlayer($this->currentGame()->dran);
    }
}
