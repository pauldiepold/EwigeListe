<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageGroupsTest extends TestCase {

    use RefreshDatabase, WithFaker;

    /** @test */
    public function guests_cannot_manage_groups()
    {
        $group = factory('App\Group')->create();

        $this->get('/groups/create')->assertRedirect('/login');
        $this->post('/groups', $group->toArray())->assertRedirect('login');
    }

    /** @test */
    public function a_user_can_create_a_group()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(factory('App\User')->create());

        $this->get('/groups/create')->assertStatus(200);

        $attributes = factory('App\Group')->raw(['created_by' => auth()->user()->player->id]);

        $this->post('groups', $attributes)->assertRedirect('/groups/1');

        $this->assertDatabaseHas('groups', $attributes);

        $this->get('groups')->assertSee(e($attributes['name']));
    }

    /** @test */
    public function the_creator_of_a_group_is_in_that_group()
    {
        $this->withoutExceptionHandling();
        $this->actingAs(factory('App\User')->create());

        $attributes = factory('App\Group')->raw(['created_by' => auth()->user()->player->id]);

        $this->post('groups', $attributes);

        $group = auth()->user()->player->createdGroups->first();

        $this->assertTrue($group->players->contains(auth()->user()->player));
    }

    /** @test */
    public function a_user_can_view_a_group()
    {
        $this->actingAs(factory('App\User')->create());

        $group = factory('App\Group')->create();

        $this->get($group->path())->assertSee(e($group->name));
    }

    /** @test */
    public function a_group_shows_its_players()
    {
        $this->actingAs(factory('App\User')->create());

        $group = factory('App\Group')->create();

        $players = factory('App\Player', 6)->create();

        $group->addPlayers($players);

        $this->get($group->path())->assertSee($players->first()->name);
    }

    /** @test */
    public function a_group_requires_a_name()
    {
        $this->actingAs(factory('App\User')->create());

        $attributes = factory('App\Group')->raw(['name' => '']);

        $this->post('/groups', $attributes)->assertSessionHasErrors('name');

    }
}
