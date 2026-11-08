<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\CreatesLiveRound;
use Tests\TestCase;

/**
 * Benutzer-ID und Spieler-ID sind verschiedene Dinge: Die Live-Seite erkennt Mitspieler an der Spieler-ID.
 */
class LivePlayerIdentityTest extends TestCase
{
    use CreatesLiveRound;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpLiveRound();
    }

    #[Test]
    public function the_user_ids_differ_from_the_player_ids(): void
    {
        $this->assertEmpty($this->users->pluck('id')->intersect($this->users->pluck('player_id')));
    }

    #[Test]
    public function a_participant_whose_user_id_differs_from_the_player_id_reports_a_reservation(): void
    {
        $game = $this->startGameWithSeed(1);

        $this->actingAs($this->userWhoseTurnItIs())
            ->post(route('live.games.vorbehalt', $game), ['vorbehalt' => 'Gesund'])
            ->assertOk();

        $this->assertNotSame($game->dran, $this->currentGame()->dran);
    }

    #[Test]
    public function a_user_outside_the_game_may_not_report_a_reservation(): void
    {
        $game = $this->startGameWithSeed(1);
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->post(route('live.games.vorbehalt', $game), ['vorbehalt' => 'Gesund'])
            ->assertForbidden();
    }

    #[Test]
    public function a_user_whose_id_equals_a_participants_player_id_is_still_an_outsider(): void
    {
        $game = $this->startGameWithSeed(1);
        $outsider = User::factory()->create(['id' => $game->dran]);

        $this->actingAs($outsider)
            ->post(route('live.games.vorbehalt', $game), ['vorbehalt' => 'Gesund'])
            ->assertForbidden();
    }

    #[Test]
    public function a_participant_may_listen_to_the_private_channel_of_their_own_player_only(): void
    {
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => 'key',
            'broadcasting.connections.pusher.secret' => 'secret',
            'broadcasting.connections.pusher.app_id' => '1',
        ]);
        // Der Log-Broadcaster aus phpunit.xml prüft keine Channels; deshalb die Channels am Pusher-Treiber neu anmelden.
        app(BroadcastManager::class)->forgetDrivers();
        require base_path('routes/channels.php');
        $liveRound = $this->round->fresh()->liveRound;
        [$me, $other] = [$this->users[0], $this->users[1]];

        $this->actingAs($me)
            ->post('/broadcasting/auth', [
                'channel_name' => "private-liveRound.{$liveRound->id}.{$me->player_id}",
                'socket_id' => '1234.5678',
            ])
            ->assertOk();

        $this->actingAs($me)
            ->post('/broadcasting/auth', [
                'channel_name' => "private-liveRound.{$liveRound->id}.{$other->player_id}",
                'socket_id' => '1234.5678',
            ])
            ->assertForbidden();
    }
}
