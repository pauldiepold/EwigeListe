<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManagePlayerTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function a_players_page_shows_a_group()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());

        $player = factory('App\User')->create()->player;

        $group = factory('App\Group')->create();

        $player->groups()->save($group);

        $this->get($player->path())->assertSee($group->name);
    }
}
