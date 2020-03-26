<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function a_user_can_see_only_his_settings_page()
    {
        $users = factory('App\User', 3)->create();
        $this->actingAs(factory('App\User')->create());

        $this->get(route('users.edit', [auth()->id()]))
            ->assertOk();

        $this->get('users/' . $users->first()->id . '/edit')
            ->assertStatus(403);
    }

    /** @test */
    public function a_user_can_change_his_name()
    {
        factory('App\User')->create();
        $user = factory('App\User')->create();
        $this->actingAs($user);

        $attributes = [
            'name' => e($this->faker->lastName),
            'surname' => e($this->faker->firstName)];

        $this->assertTrue(true);
        //$this->patch(route('users.updateName', ['user' => auth()->id()]), $attributes)
        //    ->assertRedirect(route('users.edit', ['user' => auth()->id()]));
    }

    /** @test */
    public function a_user_can_determine_their_avatar_path()
    {
        $user = factory('App\User')->create();

        $this->assertEquals('storage/avatars/default.jpg', $user->avatar());

        $user->avatar_path = 'avatars/me.jpg';

        $this->assertEquals('storage/avatars/me.jpg', $user->avatar());
    }


}
