<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{

    use RefreshDatabase, WithFaker;

    #[Test]
    public function only_members_can_add_avatars()
    {
        //$this->withoutExceptionHandling();

        $this->post('api/users/1/avatar')
            ->assertStatus(302)->assertRedirect('/login');
    }

    #[Test]
    public function a_valid_avatar_must_be_provided()
    {
        //$this->withoutExceptionHandling();

        $this->actingAs(\App\User::factory()->create());

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => 'not-an-image'
        ])
            ->assertStatus(500);
    }

    #[Test]
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        $this->actingAs(\App\User::factory()->create());

        Storage::fake('public');

        $this->json('POST', 'api/users/' . auth()->id() . '/avatar', [
            'avatar' => $file = UploadedFile::fake()->image('avatar.jpg')
        ]);

        $this->assertIsString(auth()->user()->avatar_path);

    }


}
