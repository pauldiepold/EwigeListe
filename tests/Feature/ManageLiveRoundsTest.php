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
        $groups = \App\Group::factory()->count(2)->create();

        $this->actingAs($users->get(0));

        $this->post('/rounds', [
            'players' => $users->pluck('id')->toArray(),
            'groups' => $groups->pluck('id')->toArray(),
            'liveGame' => true
        ])
            ->assertRedirect(Round::first()->path());

        $this->assertDatabaseHas('live_rounds', LiveRound::first()->toArray());
    }

}
