<?php

namespace Tests\Feature;

use App\Models\LiveGame;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\CreatesLiveRound;
use Tests\TestCase;

/**
 * Der Ablauf einer Armut über die Endpunkte, die die Live-Seite anspricht:
 * Abgeben (Phase 3), Frage nach dem Mitnehmen (32), Zurückgeben (33).
 */
class LivePovertyTest extends TestCase
{
    use CreatesLiveRound;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpLiveRound();
    }

    /**
     * Startet Spiele mit wechselnder Kartenverteilung, bis ein Spieler eine Armut melden darf, und lässt
     * ihn sie melden (die anderen sind gesund). Gibt den Armut-Spieler zurück.
     */
    private function startGameWithPoverty(): User
    {
        for ($seed = 1; $seed <= 200; $seed++) {
            mt_srand($seed);
            $this->round->fresh()->liveRound->starteNeuesSpiel();
            $game = $this->currentGame();

            $poverty = $game->spielerIDs
                ->map(fn (int $id) => $game->getSpieler($id))
                ->first(fn ($spieler): bool => $spieler->moeglicheVorbehalte->contains('Armut'));

            if ($poverty !== null) {
                break;
            }
            $game->delete();
        }
        $this->assertNotNull($poverty, 'Keine Kartenverteilung mit Armut gefunden.');

        foreach ($this->users as $ignored) {
            $dran = $this->currentGame()->dran;
            $this->actingAs($this->userOfPlayer($dran))
                ->post(route('live.games.vorbehalt', $game), [
                    'vorbehalt' => $dran === $poverty->id ? 'Armut' : 'Gesund',
                ])
                ->assertOk();
        }

        $this->assertSame(3, $this->currentGame()->phase);

        return $this->userOfPlayer($poverty->id);
    }

    /**
     * @return array{karten: array<int, array{id: int}>}
     */
    private function cardsToGiveAway(LiveGame $game, User $poverty): array
    {
        $hand = $game->getSpieler($poverty->player_id)->hand;
        $cards = $hand->where('trumpf', true)->concat($hand->where('trumpf', false))->take(3);

        return ['karten' => $cards->map(fn ($karte): array => ['id' => $karte->id])->values()->all()];
    }

    #[Test]
    public function the_poverty_gives_three_cards_and_the_next_player_is_asked(): void
    {
        $poverty = $this->startGameWithPoverty();
        $game = $this->currentGame();

        $this->actingAs($poverty)
            ->post(route('live.games.armutAbgeben', $game), $this->cardsToGiveAway($game, $poverty))
            ->assertOk();

        $game = $this->currentGame();
        $this->assertSame(32, $game->phase);
        $this->assertNotSame($poverty->player_id, $game->dran);
        $this->assertCount(9, $game->getSpieler($poverty->player_id)->hand);
    }

    #[Test]
    public function giving_away_cards_while_keeping_trump_is_rejected(): void
    {
        $poverty = $this->startGameWithPoverty();
        $game = $this->currentGame();
        $hand = $game->getSpieler($poverty->player_id)->hand;
        $nonTrump = $hand->where('trumpf', false)->take(3)->map(fn ($karte): array => ['id' => $karte->id])->values()->all();

        $this->actingAs($poverty)
            ->postJson(route('live.games.armutAbgeben', $game), ['karten' => $nonTrump])
            ->assertStatus(422);

        $this->assertSame(3, $this->currentGame()->phase);
    }

    #[Test]
    public function taking_the_poverty_leads_to_giving_back_and_then_to_the_game(): void
    {
        $poverty = $this->startGameWithPoverty();
        $game = $this->currentGame();
        $this->actingAs($poverty)
            ->post(route('live.games.armutAbgeben', $game), $this->cardsToGiveAway($game, $poverty))
            ->assertOk();

        $taker = $this->userOfPlayer($this->currentGame()->dran);
        $this->actingAs($taker)
            ->post(route('live.games.armutMitnehmen', $game), ['mitnehmen' => true])
            ->assertOk();

        $game = $this->currentGame();
        $this->assertSame(33, $game->phase);
        $this->assertSame($taker->player_id, $game->dran);
        $hand = $game->getSpieler($taker->player_id)->hand;
        $this->assertCount(15, $hand);

        $this->actingAs($taker)
            ->post(route('live.games.armutZurueckgeben', $game), [
                'karten' => $hand->take(3)->map(fn ($karte): array => ['id' => $karte->id])->values()->all(),
            ])
            ->assertOk();

        $game = $this->currentGame();
        $this->assertSame(4, $game->phase);
        $this->assertCount(12, $game->getSpieler($taker->player_id)->hand);
        $this->assertCount(12, $game->getSpieler($poverty->player_id)->hand);
    }

    #[Test]
    public function when_nobody_takes_the_poverty_a_new_game_starts(): void
    {
        $poverty = $this->startGameWithPoverty();
        $game = $this->currentGame();
        $this->actingAs($poverty)
            ->post(route('live.games.armutAbgeben', $game), $this->cardsToGiveAway($game, $poverty))
            ->assertOk();

        foreach (range(1, 3) as $ignored) {
            $asked = $this->userOfPlayer($this->currentGame()->dran);
            $this->actingAs($asked)
                ->post(route('live.games.armutMitnehmen', $game), ['mitnehmen' => false])
                ->assertOk();
        }

        $newGame = $this->currentGame();
        $this->assertNotSame($game->id, $newGame->id);
        $this->assertSame(2, $newGame->phase);
    }
}
