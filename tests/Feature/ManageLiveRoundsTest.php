<?php

namespace Tests\Feature;

use App\Group;
use App\Round;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
