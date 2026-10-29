<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use App\LiveRound;
use App\Round;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageLiveRoundsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function a_user_can_create_a_live_round()
    {
        //$this->withoutExceptionHandling();

        $users = \App\User::factory()->count(5)->create();

        $this->actingAs($users->get(0));

        $this->post('/rounds', [
            'players' => $users->pluck('player_id')->toArray(),
            'groups' => [\App\Group::EWIGE_LISTE_ID],
            'liveGame' => true
        ])
            ->assertRedirect(Round::first()->path());

        $this->assertDatabaseHas('live_rounds', LiveRound::first()->toArray());
    }

}
