<?php

namespace Tests\Feature;

use App\Player;
use App\Round;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageRoundTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_round()
    {
        $this->withoutExceptionHandling();

        //$this->seed('PlayerSeeder');
        //$players = Player::all()->random(rand(4,7));

        $users = factory('App\User', rand(4, 7))->create();
        $groups = factory('App\Group', rand(2, 4))->create();

        $this->actingAs($users->first());

        $this->post('/rounds', [
            'players' => $users->pluck('id')->toArray(),
            'groups' => $groups->pluck('id')->toArray(),
            'liveGame' => false
        ])
            ->assertOk()
            ->assertSee(Round::first()->path());

    }
}
