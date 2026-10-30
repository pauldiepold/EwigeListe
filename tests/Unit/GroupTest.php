<?php

namespace Tests\Unit;

use App\Group;
use App\Player;
use App\Round;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_a_path()
    {
        $group = Group::factory()->create();
        $this->assertEquals(URL::to('/').'/liste/'.$group->id, $group->path());
    }

    #[Test]
    public function it_is_owned_by_one_player()
    {
        $player = Player::factory()->create();
        $group = Group::factory()->create(['created_by' => $player->id]);

        $this->assertEquals($group->created_by, $player->id);
    }

    #[Test]
    public function has_many_players()
    {
        $this->withoutExceptionHandling();

        $group = Group::factory()->create();

        $players = Player::factory()->count(5)->create();

        $group->players()->saveMany($players);

        $this->assertInstanceOf('App\Player', $group->players->first());
        $this->assertCount(5, $group->players);
    }

    #[Test]
    public function a_player_has_many_groups()
    {
        $this->withoutExceptionHandling();

        $player = Player::factory()->create();

        $groups = Group::factory()->count(5)->create();

        $player->groups()->saveMany($groups);

        $this->assertInstanceOf('App\Group', $player->groups->first());
        $this->assertCount(5, $player->groups);
    }

    #[Test]
    public function has_many_rounds()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $group = Group::factory()->create();

        $rounds = Round::factory()->count(5)->create();

        $group->rounds()->saveMany($rounds);

        $this->assertInstanceOf('App\Round', $group->rounds->first());
        $this->assertCount(5, $group->rounds);
    }

    #[Test]
    public function a_round_has_many_groups()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $round = Round::factory()->create();

        $groups = Group::factory()->count(5)->create();

        $round->groups()->saveMany($groups);

        $this->assertInstanceOf('App\Group', $round->groups->first());
        $this->assertCount(5, $round->groups);
    }

    #[Test]
    public function new_players_can_be_added()
    {
        $this->withoutExceptionHandling();

        $group = Group::factory()->create();

        $firstPlayer = Player::factory()->create();
        $group->players()->save($firstPlayer);

        $this->assertCount(1, $group->players);

        $players = Player::factory()->count(3)->create()->add($firstPlayer);

        $group->addPlayers($players);
        $group->load('players');

        $this->assertCount(4, $group->players);
    }
}
