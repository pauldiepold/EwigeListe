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
        $this->post('api/users/1/avatar')
            ->assertStatus(302)->assertRedirect('/login');
    }

    #[Test]
    public function a_user_cannot_change_the_avatar_of_another_user()
    {
        $otherUser = \App\User::factory()->create();
        $this->actingAs(\App\User::factory()->create());
        $this->assertFalse(auth()->user()->isAdmin());

        $this->postJson('api/users/' . $otherUser->id . '/avatar', ['avatar' => $this->avatarDataUri()])
            ->assertForbidden();
    }

    #[Test]
    public function a_valid_avatar_must_be_provided()
    {
        $this->actingAs(\App\User::factory()->create());

        $this->postJson('api/users/' . auth()->id() . '/avatar', [
            'avatar' => 'not-an-image'
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('avatar');
    }

    #[Test]
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        Storage::fake('public');
        $user = \App\User::factory()->create();
        $this->actingAs($user);

        $this->postJson('api/users/' . $user->id . '/avatar', ['avatar' => $this->avatarDataUri()])
            ->assertSuccessful();

        $filename = $user->fresh()->getRawOriginal('avatar_path');
        $this->assertNotNull($filename);
        Storage::disk('public')->assertExists('avatars/' . $filename);
        Storage::disk('public')->assertExists('avatars/' . str_replace('.jpg', '_full.jpg', $filename));

        [$width] = getimagesizefromstring(Storage::disk('public')->get('avatars/' . $filename));
        $this->assertSame(200, $width);
    }

    /**
     * Der Cropper im Frontend schickt das Bild als Data-URI (`canvas.toDataURL()`).
     */
    private function avatarDataUri(): string
    {
        $image = UploadedFile::fake()->image('avatar.png', 400, 400);

        return 'data:image/png;base64,' . base64_encode(file_get_contents($image->getPathname()));
    }
}
