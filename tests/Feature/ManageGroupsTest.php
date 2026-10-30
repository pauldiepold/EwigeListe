<?php

namespace Tests\Feature;

use App\Group;
use App\Player;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManageGroupsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function guests_cannot_manage_groups()
    {
        $group = Group::factory()->create();

        $this->get('/liste/erstellen')->assertRedirect('/login');
        $this->post('/groups', $group->toArray())->assertRedirect('login');
    }

    #[Test]
    public function a_user_can_create_a_group()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(User::factory()->create());

        $this->get(route('groups.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Groups/Create'));

        $attributes = Group::factory()->raw(['created_by' => auth()->user()->player->id]);

        $response = $this->post('groups', $attributes);

        $response->assertRedirect(Group::where('name', $attributes['name'])->sole()->path());

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
        $this->actingAs(User::factory()->create());

        $attributes = Group::factory()->raw(['created_by' => auth()->user()->player->id]);

        $this->post('groups', $attributes);

        $group = auth()->user()->player->createdGroups->first();

        $this->assertTrue($group->players->contains(auth()->user()->player));
    }

    #[Test]
    public function a_user_can_view_a_group()
    {
        $this->actingAs(User::factory()->create());

        $group = Group::factory()->create(['name' => "O'Kon"]);
        $group->calculate();

        $this->get($group->path())->assertOk()->assertSee($group->name);
    }

    #[Test]
    public function a_group_shows_its_players()
    {
        $this->markTestIncomplete('Die Tabelle erscheint erst nach gespielten Runden; wird mit der Migration der Seite neu geschrieben (#30).');

        $this->actingAs(User::factory()->create());

        $group = Group::factory()->create();

        $players = Player::factory()->count(6)->create();

        $group->addPlayers($players);
        $group->calculate();

        $this->get($group->path())->assertOk()->assertSee($players->first()->name);
    }

    #[Test]
    public function a_group_requires_a_name()
    {
        $this->actingAs(User::factory()->create());

        $attributes = Group::factory()->raw(['name' => '']);

        $this->post('/groups', $attributes)->assertSessionHasErrors('name');

    }
}
