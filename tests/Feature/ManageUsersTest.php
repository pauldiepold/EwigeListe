<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    #[Test]
    public function a_user_can_see_only_his_settings_page()
    {
        $users = \App\User::factory()->count(3)->create();
        $this->actingAs(\App\User::factory()->create());

        $this->get(route('users.edit', [auth()->id()]))
            ->assertOk();

        $this->get('users/' . $users->first()->id . '/edit')
            ->assertStatus(403);
    }

    #[Test]
    public function a_user_can_determine_their_avatar_path()
    {
        $user = \App\User::factory()->create();

        $this->assertEquals('/img/default.jpg', $user->avatar_path);

        $user->avatar_path = 'me.jpg';

        $this->assertEquals('/storage/avatars/me.jpg', $user->avatar_path);
    }


}
