<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_a_path()
    {
        $group = \App\Group::factory()->create();
        $this->assertEquals(URL::to('/') . '/liste/' . $group->id, $group->path());
    }

    #[Test]
    public function it_is_owned_by_one_player()
    {
        $player = \App\Player::factory()->create();
        $group = \App\Group::factory()->create(['created_by' => $player->id]);

        $this->assertEquals($group->created_by, $player->id);
    }

    #[Test]
    public function has_many_players()
    {
        $this->withoutExceptionHandling();

        $group = \App\Group::factory()->create();

        $players = \App\Player::factory()->count(5)->create();

        $group->players()->saveMany($players);

        $this->assertInstanceOf('App\Player', $group->players->first());
        $this->assertCount(5, $group->players);
    }

    #[Test]
    public function a_player_has_many_groups()
    {
        $this->withoutExceptionHandling();

        $player = \App\Player::factory()->create();

        $groups = \App\Group::factory()->count(5)->create();

        $player->groups()->saveMany($groups);

        $this->assertInstanceOf('App\Group', $player->groups->first());
        $this->assertCount(5, $player->groups);
    }

    #[Test]
    public function has_many_rounds()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(\App\User::factory()->create());

        $group = \App\Group::factory()->create();

        $rounds = \App\Round::factory()->count(5)->create();

        $group->rounds()->saveMany($rounds);

        $this->assertInstanceOf('App\Round', $group->rounds->first());
        $this->assertCount(5, $group->rounds);
    }

    #[Test]
    public function a_round_has_many_groups()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(\App\User::factory()->create());

        $round = \App\Round::factory()->create();

        $groups = \App\Group::factory()->count(5)->create();

        $round->groups()->saveMany($groups);

        $this->assertInstanceOf('App\Group', $round->groups->first());
        $this->assertCount(5, $round->groups);
    }

    #[Test]
    public function new_players_can_be_added()
    {
        $this->withoutExceptionHandling();

        $group = \App\Group::factory()->create();

        $firstPlayer = \App\Player::factory()->create();
        $group->players()->save($firstPlayer);

        $this->assertCount(1, $group->players);

        $players = \App\Player::factory()->count(3)->create()->add($firstPlayer);

        $group->addPlayers($players);
        $group->load('players');

        $this->assertCount(4, $group->players);
    }
}
