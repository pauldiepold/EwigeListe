<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_a_path()
    {
        $group = factory('App\Group')->create();
        $this->assertEquals(URL::to('/') . '/liste/' . $group->id, $group->path());
    }

    /** @test */
    public function it_is_owned_by_one_player()
    {
        $player = factory('App\Player')->create();
        $group = factory('App\Group')->create(['created_by' => $player->id]);

        $this->assertEquals($group->created_by, $player->id);
    }

    /** @test */
    public function has_many_players()
    {
        $this->withoutExceptionHandling();

        $group = factory('App\Group')->create();

        $players = factory('App\Player', 5)->create();

        $group->players()->saveMany($players);

        $this->assertInstanceOf('App\Player', $group->players->first());
        $this->assertCount(5, $group->players);
    }

    /** @test */
    public function a_player_has_many_groups()
    {
        $this->withoutExceptionHandling();

        $player = factory('App\Player')->create();

        $groups = factory('App\Group', 5)->create();

        $player->groups()->saveMany($groups);

        $this->assertInstanceOf('App\Group', $player->groups->first());
        $this->assertCount(5, $player->groups);
    }

    /** @test */
    public function has_many_rounds()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());

        $group = factory('App\Group')->create();

        $rounds = factory('App\Round', 5)->create();

        $group->rounds()->saveMany($rounds);

        $this->assertInstanceOf('App\Round', $group->rounds->first());
        $this->assertCount(5, $group->rounds);
    }

    /** @test */
    public function a_round_has_many_groups()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());

        $round = factory('App\Round')->create();

        $groups = factory('App\Group', 5)->create();

        $round->groups()->saveMany($groups);

        $this->assertInstanceOf('App\Group', $round->groups->first());
        $this->assertCount(5, $round->groups);
    }

    /** @test */
    public function new_players_can_be_added()
    {
        $this->withoutExceptionHandling();

        $group = factory('App\Group')->create();

        $firstPlayer = factory('App\Player')->create();
        $group->players()->save($firstPlayer);

        $this->assertCount(1, $group->players);

        $players = factory('App\Player', 3)->create()->add($firstPlayer);

        $group->addPlayers($players);
        $group->load('players');

        $this->assertCount(4, $group->players);
    }
}
