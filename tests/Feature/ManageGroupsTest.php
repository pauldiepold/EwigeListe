<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageGroupsTest extends TestCase {

    use RefreshDatabase, WithFaker;

    #[Test]
    public function guests_cannot_manage_groups()
    {
        $group = \App\Group::factory()->create();

        $this->get('/liste/erstellen')->assertRedirect('/login');
        $this->post('/groups', $group->toArray())->assertRedirect('login');
    }

    #[Test]
    public function a_user_can_create_a_group()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(\App\User::factory()->create());

        $this->get(route('groups.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Groups/Create'));

        $attributes = \App\Group::factory()->raw(['created_by' => auth()->user()->player->id]);

        $response = $this->post('groups', $attributes);

        $response->assertRedirect(\App\Group::where('name', $attributes['name'])->sole()->path());

        $this->assertDatabaseHas('groups', $attributes);

        $this->get(route('groups.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Groups/Index')
                ->has('groups', 2)
                ->where('groups', fn ($groups) => collect($groups)->contains('name', $attributes['name']))
            );
    }

    #[Test]
    public function the_creator_of_a_group_is_in_that_group()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(\App\User::factory()->create());

        $attributes = \App\Group::factory()->raw(['created_by' => auth()->user()->player->id]);

        $this->post('groups', $attributes);

        $group = auth()->user()->player->createdGroups->first();

        $this->assertTrue($group->players->contains(auth()->user()->player));
    }

    #[Test]
    public function a_user_can_view_a_group()
    {
        $this->actingAs(\App\User::factory()->create());

        $group = \App\Group::factory()->create(['name' => "O'Kon"]);
        $group->calculate();

        $this->get($group->path())->assertOk()->assertSee($group->name);
    }

    #[Test]
    public function a_group_shows_its_players()
    {
        $this->markTestIncomplete('Die Tabelle erscheint erst nach gespielten Runden; wird mit der Migration der Seite neu geschrieben (#30).');

        $this->actingAs(\App\User::factory()->create());

        $group = \App\Group::factory()->create();

        $players = \App\Player::factory()->count(6)->create();

        $group->addPlayers($players);
        $group->calculate();

        $this->get($group->path())->assertOk()->assertSee($players->first()->name);
    }

    #[Test]
    public function a_group_requires_a_name()
    {
        $this->actingAs(\App\User::factory()->create());

        $attributes = \App\Group::factory()->raw(['name' => '']);

        $this->post('/groups', $attributes)->assertSessionHasErrors('name');

    }
}
