<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\LiveGame;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LiveGameEndTest extends TestCase
{
    use RefreshDatabase;

    /** @var Collection<int, User> */
    private Collection $users;

    private Round $round;

    protected function setUp(): void
    {
        parent::setUp();

        // Die Engine vergleicht die Benutzer-ID mit der Spieler-ID, deshalb sind beide hier gleich.
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
    }

    private function currentGame(): LiveGame
    {
        return LiveGame::where('live_round_id', $this->round->fresh()->live_round_id)->latest('id')->firstOrFail();
    }

    /**
     * Spielt ein Normalspiel (alle melden sich gesund) mit der jeweils ersten spielbaren Karte bis zum Ende.
     */
    private function playNormalGameToTheEnd(): LiveGame
    {
        mt_srand(1);
        $this->round->fresh()->liveRound->starteNeuesSpiel();
        $game = $this->currentGame();

        foreach ($this->users as $ignored) {
            $this->actingAs($this->users->firstWhere('id', $this->currentGame()->dran))
                ->post(route('live.games.vorbehalt', $game), ['vorbehalt' => 'Gesund'])
                ->assertOk();
        }

        for ($card = 0; $card < 48; $card++) {
            $current = $this->currentGame();
            $dran = $this->users->firstWhere('id', $current->dran);
            $playable = collect($current->getSpieler($dran->player_id)->hand)->firstWhere('spielbar', true);

            $this->actingAs($dran)
                ->post(route('live.games.karteSpielen', $current), ['karte' => ['id' => $playable->id]])
                ->assertOk();
        }

        return $game->fresh();
    }

    #[Test]
    public function the_last_card_ends_the_game_and_enters_it_into_the_round(): void
    {
        $game = $this->playNormalGameToTheEnd();

        $this->assertTrue($game->beendet);
        $this->assertSame(101, $game->phase);
        $this->assertSame(1, $this->round->fresh()->games()->count());
        $this->assertSame($game->wertungsPunkte, $this->round->games()->first()->points);
    }

    #[Test]
    public function after_the_end_the_live_page_delivers_the_scoring_but_no_running_game(): void
    {
        $game = $this->playNormalGameToTheEnd();

        $this->actingAs($this->users[0])->get(route('rounds.live', $this->round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Live/Show')
                ->where('liveGame', null)
                ->where('ich', null)
                ->where('round.gamesCount', 1)
                ->where('lastLiveGame.id', $game->id)
                ->where('lastLiveGame.phase', 101)
                ->where('lastLiveGame.wertungsPunkte', $game->wertungsPunkte)
                ->has('lastLiveGame.wertung')
                ->has('lastLiveGame.winners', 2)
                ->has('lastLiveGame.reAugen')
                ->has('lastLiveGame.kontraAugen')
                ->has('lastLiveGame.gewinntRe')
            );
    }

    #[Test]
    public function the_next_game_is_dealt_by_the_next_dealer(): void
    {
        $first = $this->playNormalGameToTheEnd();
        $this->assertSame($this->users[1]->player_id, $first->vorhand);

        $this->actingAs($this->users[1])
            ->post(route('live.rounds.start', $this->round->liveRound))
            ->assertOk();

        $second = $this->currentGame();
        $this->assertNotSame($first->id, $second->id);
        $this->assertSame($this->users[2]->player_id, $second->vorhand);
    }
}
