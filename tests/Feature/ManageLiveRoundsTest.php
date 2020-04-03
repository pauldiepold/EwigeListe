<?php

namespace Tests\Feature;

use App\LiveRound;
use App\Round;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageLiveRoundsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_create_a_live_round()
    {
        //$this->withoutExceptionHandling();

        $users = factory('App\User', 5)->create();
        $groups = factory('App\Group', 2)->create();

        $this->actingAs($users->get(0));

        $this->post('/rounds', [
            'players' => $users->pluck('id')->toArray(),
            'groups' => $groups->pluck('id')->toArray(),
            'liveGame' => true
        ])
            ->assertOk()
            ->assertSee(Round::first()->path());

        $this->assertDatabaseHas('live_rounds', LiveRound::first()->toArray());
    }

}
