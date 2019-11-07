<?php

namespace Tests\Feature;

use App\Player;
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

        $this->seed('PlayerSeeder');

        $players = Player::all()->random(rand(4,7));

        $groups = factory('App\Group',rand(2,4))->create();

        $this->actingAs($players->first()->user);

        $this->post('/rounds', [
            'players' => $players->pluck('id')->toArray(),
            'groups' => $groups->pluck('id')->toArray()
        ])->assertTrue(true);
            //->assertSee('/rounds/')->assertStatus(200);

    }
}
