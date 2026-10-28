<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManagePlayerTest extends TestCase
{

    use RefreshDatabase;

    #[Test]
    public function a_players_page_shows_a_group()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(\App\User::factory()->create());

        $player = \App\User::factory()->create()->player;

        \App\Group::find(\App\Group::EWIGE_LISTE_ID)->addPlayer($player);
        $group = \App\Group::factory()->create();

        $player->groups()->save($group);

        $this->get($player->path())->assertOk()->assertSee($group->name);
    }
}
