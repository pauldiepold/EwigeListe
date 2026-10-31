<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManagePlayerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_players_page_shows_a_group()
    {
        $this->withoutExceptionHandling();

        $this->actingAs(User::factory()->create());

        $player = User::factory()->create()->player;

        Group::find(Group::EWIGE_LISTE_ID)->addPlayer($player);
        $group = Group::factory()->create();

        $player->groups()->save($group);

        $this->get($player->path())
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('groupOptions', fn ($options) => collect($options)->pluck('name')->contains($group->name))
            );
    }
}
