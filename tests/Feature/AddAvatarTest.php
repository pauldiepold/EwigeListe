<?php

namespace Tests\Feature;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddAvatarTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function only_members_can_add_avatars()
    {
        $this->post(route('users.updateAvatar', 1))
            ->assertStatus(302)->assertRedirect('/login');
    }

    #[Test]
    public function a_user_cannot_change_the_avatar_of_another_user()
    {
        $otherUser = User::factory()->create();
        $this->actingAs(User::factory()->create());
        $this->assertFalse(auth()->user()->isAdmin());

        $this->post(route('users.updateAvatar', $otherUser), ['avatar' => $this->avatarDataUri()])
            ->assertForbidden();
    }

    #[Test]
    public function a_valid_avatar_must_be_provided()
    {
        $this->actingAs(User::factory()->create());

        $this->post(route('users.updateAvatar', auth()->id()), [
            'avatar' => 'not-an-image',
        ])
            ->assertSessionHasErrors('avatar');
    }

    #[Test]
    public function a_user_may_add_an_avatar_to_their_profile()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('users.updateAvatar', $user), ['avatar' => $this->avatarDataUri()])
            ->assertRedirect(route('users.edit', $user))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Avatar wurde erfolgreich gespeichert!']);

        $filename = $user->fresh()->getRawOriginal('avatar_path');
        $this->assertNotNull($filename);
        Storage::disk('public')->assertExists('avatars/'.$filename);
        Storage::disk('public')->assertExists('avatars/'.str_replace('.jpg', '_full.jpg', $filename));

        [$width] = getimagesizefromstring(Storage::disk('public')->get('avatars/'.$filename));
        $this->assertSame(200, $width);

        [$fullWidth] = getimagesizefromstring(Storage::disk('public')->get('avatars/'.str_replace('.jpg', '_full.jpg', $filename)));
        $this->assertSame(400, $fullWidth);
    }

    #[Test]
    public function the_avatar_filename_does_not_depend_on_the_name_of_the_player()
    {
        Storage::fake('public');
        $user = User::factory()->create([
            'player_id' => Player::factory()->create(['surname' => 'Anna/Maria', 'name' => "O'Neil"])->id,
        ]);
        $this->actingAs($user);

        $this->post(route('users.updateAvatar', $user), ['avatar' => $this->avatarDataUri()])
            ->assertRedirect(route('users.edit', $user));

        $filename = $user->fresh()->getRawOriginal('avatar_path');
        $this->assertMatchesRegularExpression('/^[\w-]+\.jpg$/', $filename);
        Storage::disk('public')->assertExists('avatars/'.$filename);
    }

    /**
     * Der Cropper im Frontend schickt das Bild als Data-URI (`canvas.toDataURL()`).
     */
    private function avatarDataUri(): string
    {
        $image = UploadedFile::fake()->image('avatar.png', 400, 400);

        return 'data:image/png;base64,'.base64_encode(file_get_contents($image->getPathname()));
    }
}
