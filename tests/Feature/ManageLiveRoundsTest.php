<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\LiveGame;
use App\Models\Round;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManageLiveRoundsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function a_user_can_create_a_live_round()
    {
        $users = User::factory()->count(5)->create();

        $this->actingAs($users->get(0));

        $response = $this->post(route('rounds.store'), [
            'players' => $users->pluck('player_id')->toArray(),
            'groups' => [Group::EWIGE_LISTE_ID],
            'liveGame' => true,
        ]);

        $round = Round::sole();
        $response->assertRedirect($round->path());
        $this->assertNotNull($round->liveRound);
    }

    #[Test]
    public function a_live_round_with_four_humans_deals_twelve_cards_to_everyone(): void
    {
        $users = User::factory()->count(4)->create();
        $this->actingAs($users->first());
        $this->post(route('rounds.store'), [
            'players' => $users->pluck('player_id')->toArray(),
            'groups' => [Group::EWIGE_LISTE_ID],
            'liveGame' => true,
        ]);
        $liveRound = Round::sole()->liveRound;

        $this->post(route('live.rounds.start', $liveRound))->assertOk();

        $liveGame = LiveGame::sole();
        $this->assertSame(2, $liveGame->phase);
        foreach ([0, 1, 2, 3] as $index) {
            $this->assertCount(12, $liveGame->{'spieler'.$index}->hand);
        }
        $this->assertEmpty($liveGame->messages);
    }

    #[Test]
    public function the_ai_columns_are_gone(): void
    {
        $this->assertFalse(Schema::hasColumn('players', 'ai_path'));
        $this->assertFalse(Schema::hasColumn('live_games', 'is_with_ai'));
    }
}
