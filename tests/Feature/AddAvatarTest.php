<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function only_members_can_add_avatars()
    {
        //$this->withoutExceptionHandling();

        $this->post('api/users/1/avatar')
            ->assertStatus(302)->assertRedirect('/login');
    }

    /** @test */
    public function a_valid_avatar_must_be_provided()
    {
        //$this->withoutExceptionHandling();

        $this->actingAs(factory('App\User')->create());

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => 'not-an-image'
        ])
            ->assertStatus(500);
    }

    /** @test */
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        $this->actingAs(factory('App\User')->create());

        Storage::fake('public');

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertIsString(auth()->user()->avatar_path);

    }


}
