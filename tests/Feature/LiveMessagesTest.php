<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\LiveGame;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LiveMessagesTest extends TestCase
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

    /**
     * Die Kartenverteilung hängt am Seed (`Collection::shuffle`).
     */
    private function startGameWithSeed(int $seed): LiveGame
    {
        mt_srand($seed);
        $this->round->fresh()->liveRound->starteNeuesSpiel();

        return $this->currentGame();
    }

    private function currentGame(): LiveGame
    {
        return LiveGame::where('live_round_id', $this->round->fresh()->live_round_id)->latest('id')->firstOrFail();
    }

    private function allReportHealthy(LiveGame $game): void
    {
        foreach ($this->users as $ignored) {
            $user = $this->users->firstWhere('id', $this->currentGame()->dran);
            $this->actingAs($user)
                ->post(route('live.games.vorbehalt', $game), ['vorbehalt' => 'Gesund'])
                ->assertOk();
        }
    }

    #[Test]
    public function throwing_in_starts_a_new_game_with_a_message_naming_the_player(): void
    {
        // Bei Seed 5 darf der Spieler auf Position 3 schmeißen, niemand hat eine Hochzeit.
        $game = $this->startGameWithSeed(5);
        $thrower = $game->getSpieler($game->spielerIDs[3]);
        $this->assertTrue($thrower->moeglicheVorbehalte->contains('Schmeißen'));

        foreach ($this->users as $ignored) {
            $dran = $this->currentGame()->dran;
            $this->actingAs($this->users->firstWhere('id', $dran))
                ->post(route('live.games.vorbehalt', $game), [
                    'vorbehalt' => $dran === $thrower->id ? 'Schmeißen' : 'Gesund',
                ])
                ->assertOk();
        }

        $this->assertTrue($game->fresh()->geschmissen);
        $newGame = $this->currentGame();
        $this->assertNotSame($game->id, $newGame->id);
        $this->assertEquals(
            ['spielerId' => $thrower->id, 'text' => 'hat geschmissen!'],
            $newGame->messages->first(),
        );
    }

    #[Test]
    public function a_game_without_reservations_yields_the_normal_game_message_without_a_player(): void
    {
        $game = $this->startGameWithSeed(1);

        $this->allReportHealthy($game);

        $this->assertEquals(
            ['spielerId' => null, 'text' => 'Es wird ein Normalspiel gespielt!'],
            $this->currentGame()->messages->first(),
        );
    }

    #[Test]
    public function an_announcement_yields_a_message_with_the_announcing_player_and_plain_text(): void
    {
        $game = $this->startGameWithSeed(1);
        $this->allReportHealthy($game);
        $game = $this->currentGame();
        $re = $game->spielerIDs
            ->map(fn ($id) => $game->getSpieler($id))
            ->first(fn ($spieler) => $spieler->isRe);
        $announcer = $this->users->first(fn (User $user): bool => $user->player_id === $re->id);

        $this->actingAs($announcer)
            ->post(route('live.games.ansage', $game), ['ansage' => 'Re'])
            ->assertOk();

        $message = $this->currentGame()->messages->first();
        $this->assertSame($re->id, $message['spielerId']);
        $this->assertSame('Re!', $message['text']);
        $this->assertSame(strip_tags($message['text']), $message['text']);
    }

    #[Test]
    public function the_live_page_delivers_the_messages_as_structured_data(): void
    {
        $game = $this->startGameWithSeed(1);
        $this->allReportHealthy($game);

        $this->actingAs($this->users->first())->get(route('rounds.live', $this->round))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('liveGame.messages.0.spielerId', null)
                ->where('liveGame.messages.0.text', 'Es wird ein Normalspiel gespielt!')
            );
    }

    #[Test]
    public function old_string_messages_are_read_without_tags_and_without_player(): void
    {
        $game = $this->startGameWithSeed(1);
        DB::table('live_games')->where('id', $game->id)->update([
            'messages' => '["<b>Anna<\/b>: Schweinchen!","Es wird ein Normalspiel gespielt!"]',
        ]);

        $messages = $game->fresh()->messages;

        $this->assertEquals([
            ['spielerId' => null, 'text' => 'Anna: Schweinchen!'],
            ['spielerId' => null, 'text' => 'Es wird ein Normalspiel gespielt!'],
        ], $messages->all());
    }

    #[Test]
    public function new_messages_survive_a_round_trip_in_the_structured_format(): void
    {
        $game = $this->startGameWithSeed(1);

        $game->pushMessage('hat geschmissen!', 7);
        $game->save();

        $this->assertEquals(['spielerId' => 7, 'text' => 'hat geschmissen!'], $game->fresh()->messages->first());
    }
}
