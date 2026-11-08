<?php

namespace Tests\Feature;

use App\Models\LiveGame;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\CreatesLiveRound;
use Tests\TestCase;

class LiveGameEndTest extends TestCase
{
    use CreatesLiveRound;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpLiveRound();
    }

    /**
     * Spielt ein Normalspiel (alle melden sich gesund) mit der jeweils ersten spielbaren Karte bis zum Ende.
     */
    private function playNormalGameToTheEnd(): LiveGame
    {
        $game = $this->startGameWithSeed(1);

        foreach ($this->users as $ignored) {
            $this->actingAs($this->userWhoseTurnItIs())
                ->post(route('live.games.vorbehalt', $game), ['vorbehalt' => 'Gesund'])
                ->assertOk();
        }

        for ($card = 0; $card < 48; $card++) {
            $current = $this->currentGame();
            $dran = $this->userOfPlayer($current->dran);
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
