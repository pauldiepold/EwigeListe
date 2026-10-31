<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Player;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ManageUsersTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    #[Test]
    public function guests_cannot_see_the_settings_page()
    {
        $this->get(route('users.edit', User::factory()->create()))->assertRedirect('/login');
    }

    #[Test]
    public function a_user_can_see_only_his_settings_page()
    {
        $users = User::factory()->count(3)->create();
        $this->actingAs(User::factory()->create());

        $this->get(route('users.edit', [auth()->id()]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Users/Edit'));

        $this->get('users/'.$users->last()->id.'/edit')
            ->assertForbidden();
    }

    #[Test]
    public function the_admin_can_see_the_settings_page_of_another_user()
    {
        $admin = User::factory()->create();
        $user = $this->signInRegularUser();
        $this->assertTrue($admin->isAdmin());

        $this->actingAs($admin)
            ->get(route('users.edit', $user))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('user.id', $user->id));
    }

    #[Test]
    public function the_settings_page_shows_the_data_of_the_user_and_his_lists()
    {
        $user = $this->signInRegularUser();
        $openGroup = Group::factory()->create(['name' => 'Offene Liste']);
        $closedGroup = Group::factory()->closed()->create(['name' => 'Alte Liste']);
        Group::find(Group::EWIGE_LISTE_ID)->addPlayer($user->player);
        $openGroup->addPlayer($user->player);
        $closedGroup->addPlayer($user->player);
        $this->profile($user, $openGroup)->forceFill(['default' => true])->save();

        $this->get(route('users.edit', $user))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Users/Edit')
                ->where('user', [
                    'id' => $user->id,
                    'player_id' => $user->player_id,
                    'email' => $user->email,
                    'surname' => $user->player->surname,
                    'name' => $user->player->name,
                    'avatar_path' => null,
                ])
                ->where('profiles', fn ($profiles) => collect($profiles)->keyBy('name')->map(
                    fn ($profile) => [$profile['default'], $profile['selectable']]
                )->all() == [
                    Group::find(Group::EWIGE_LISTE_ID)->name => [false, false],
                    'Offene Liste' => [true, true],
                    'Alte Liste' => [false, false],
                ])
            );
    }

    #[Test]
    public function a_user_without_an_avatar_has_no_avatar_path()
    {
        $user = User::factory()->create();

        $this->assertNull($user->avatar_path);
        $this->assertNull($user->player->avatar_path);

        $user->avatar_path = 'me.jpg';

        $this->assertEquals('/storage/avatars/me.jpg', $user->avatar_path);
    }

    #[Test]
    public function a_user_can_change_his_name()
    {
        $user = $this->signInRegularUser();

        $this->patch(route('users.updateName', $user), ['vorname' => 'Anna', 'nachname' => 'Müller'])
            ->assertRedirect(route('users.edit', $user))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Name wurde erfolgreich gespeichert!']);

        $this->assertDatabaseHas('players', ['id' => $user->player_id, 'surname' => 'Anna', 'name' => 'Müller']);
    }

    #[Test]
    public function a_name_requires_first_and_last_name()
    {
        $user = $this->signInRegularUser();

        $this->patch(route('users.updateName', $user), ['vorname' => '', 'nachname' => 'M'])
            ->assertSessionHasErrors(['vorname', 'nachname']);
    }

    #[Test]
    public function a_user_can_change_his_email()
    {
        $user = $this->signInRegularUser();

        $this->patch(route('users.updateMail', $user), ['email' => 'neu@example.com'])
            ->assertRedirect(route('users.edit', $user))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'E-Mail wurde erfolgreich geändert!']);

        $this->assertSame('neu@example.com', $user->fresh()->email);
    }

    #[Test]
    public function a_user_can_save_his_unchanged_email()
    {
        $user = $this->signInRegularUser();

        $this->patch(route('users.updateMail', $user), ['email' => $user->email])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.edit', $user));
    }

    #[Test]
    public function an_email_must_be_valid_and_not_used_by_another_user()
    {
        $user = $this->signInRegularUser();
        $otherUser = User::factory()->create();

        $this->patch(route('users.updateMail', $user), ['email' => 'keine-adresse'])
            ->assertSessionHasErrors('email');

        $this->patch(route('users.updateMail', $user), ['email' => $otherUser->email])
            ->assertSessionHasErrors('email');
    }

    #[Test]
    public function a_user_can_change_his_password()
    {
        $user = $this->signInRegularUser();

        $this->patch(route('users.updatePassword', $user), [
            'current_password' => 'testtest',
            'password' => 'neues-passwort',
            'password_confirmation' => 'neues-passwort',
        ])
            ->assertRedirect(route('users.edit', $user))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Passwort wurde erfolgreich geändert!']);

        $this->assertTrue(Hash::check('neues-passwort', $user->fresh()->password));
    }

    #[Test]
    public function a_new_password_requires_the_old_password_and_a_confirmation()
    {
        $user = $this->signInRegularUser();

        $this->patch(route('users.updatePassword', $user), [
            'current_password' => 'falsches-passwort',
            'password' => 'neues-passwort',
            'password_confirmation' => 'anderes-passwort',
        ])->assertSessionHasErrors(['current_password', 'password']);

        $this->patch(route('users.updatePassword', $user), [
            'current_password' => 'testtest',
            'password' => 'kurz',
            'password_confirmation' => 'kurz',
        ])->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('testtest', $user->fresh()->password));
    }

    #[Test]
    public function the_admin_can_change_the_password_of_another_user_without_knowing_it()
    {
        $admin = User::factory()->create();
        $user = $this->signInRegularUser();
        $this->actingAs($admin);

        $this->patch(route('users.updatePassword', $user), [
            'current_password' => '',
            'password' => 'neues-passwort',
            'password_confirmation' => 'neues-passwort',
        ])->assertSessionHasErrors('current_password');

        $this->patch(route('users.updatePassword', $user), [
            'current_password' => 'beliebig',
            'password' => 'neues-passwort',
            'password_confirmation' => 'neues-passwort',
        ])->assertRedirect(route('users.edit', $user));

        $this->assertTrue(Hash::check('neues-passwort', $user->fresh()->password));
    }

    #[Test]
    #[DataProvider('userMutations')]
    public function a_user_cannot_change_another_user(string $route, array $data)
    {
        $this->signInRegularUser();
        $otherUser = User::factory()->create(['email' => 'andere@example.com']);

        $this->patch(route($route, $otherUser), $data)->assertForbidden();

        $this->assertTrue(Hash::check('test', $otherUser->fresh()->password));
        $this->assertSame('andere@example.com', $otherUser->fresh()->email);
        $this->assertNotSame('Anna', $otherUser->player->fresh()->surname);
    }

    /**
     * @return array<string, array{string, array<string, mixed>}>
     */
    public static function userMutations(): array
    {
        return [
            'Name' => ['users.updateName', ['vorname' => 'Anna', 'nachname' => 'Müller']],
            'E-Mail' => ['users.updateMail', ['email' => 'neu@example.com']],
            'Passwort' => ['users.updatePassword', [
                'current_password' => 'testtest',
                'password' => 'neues-passwort',
                'password_confirmation' => 'neues-passwort',
            ]],
            'Standardlisten' => ['users.updateListen', ['default_profiles' => []]],
        ];
    }

    #[Test]
    public function a_user_can_set_his_default_lists()
    {
        $user = $this->signInRegularUser();
        $newDefault = Group::factory()->create();
        $oldDefault = Group::factory()->create();
        $newDefault->addPlayer($user->player);
        $oldDefault->addPlayer($user->player);
        $this->profile($user, $oldDefault)->forceFill(['default' => true])->save();

        $this->patch(route('users.updateListen', $user), [
            'default_profiles' => [$this->profile($user, $newDefault)->id],
        ])
            ->assertRedirect(route('users.edit', $user))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Standardlisten wurden gespeichert!']);

        $this->assertTrue((bool) $this->profile($user, $newDefault)->default);
        $this->assertFalse((bool) $this->profile($user, $oldDefault)->default);
    }

    #[Test]
    public function default_lists_must_be_a_list_of_ids()
    {
        $user = $this->signInRegularUser();

        $this->patch(route('users.updateListen', $user), [])
            ->assertSessionHasErrors('default_profiles');

        $this->patch(route('users.updateListen', $user), ['default_profiles' => ['keine-id']])
            ->assertSessionHasErrors('default_profiles.0');
    }

    #[Test]
    public function a_user_can_remove_all_his_default_lists()
    {
        $user = $this->signInRegularUser();
        $group = Group::factory()->create();
        $group->addPlayer($user->player);
        $this->profile($user, $group)->forceFill(['default' => true])->save();

        $this->patch(route('users.updateListen', $user), ['default_profiles' => []])
            ->assertSessionHasNoErrors();

        $this->assertFalse((bool) $this->profile($user, $group)->default);
    }

    #[Test]
    public function closed_lists_and_the_ewige_liste_cannot_become_default_lists()
    {
        $user = $this->signInRegularUser();
        $closedGroup = Group::factory()->closed()->create();
        $ewigeListe = Group::find(Group::EWIGE_LISTE_ID);
        $closedGroup->addPlayer($user->player);
        $ewigeListe->addPlayer($user->player);

        $this->patch(route('users.updateListen', $user), [
            'default_profiles' => [$this->profile($user, $closedGroup)->id, $this->profile($user, $ewigeListe)->id],
        ])->assertSessionHasNoErrors();

        $this->assertFalse((bool) $this->profile($user, $closedGroup)->default);
        $this->assertFalse((bool) $this->profile($user, $ewigeListe)->default);
    }

    #[Test]
    public function a_user_cannot_change_the_default_lists_of_another_player()
    {
        $user = $this->signInRegularUser();
        $otherUser = User::factory()->create();
        $group = Group::factory()->create();
        $group->addPlayer($otherUser->player);
        $otherProfile = $this->profile($otherUser, $group);
        $otherProfile->forceFill(['default' => false])->save();

        $this->patch(route('users.updateListen', $user), ['default_profiles' => [$otherProfile->id]])
            ->assertRedirect(route('users.edit', $user));

        $this->assertFalse((bool) $otherProfile->fresh()->default);
    }

    /**
     * Ein angemeldeter Benutzer ohne Admin-Rechte, dessen ID sich von der Spieler-ID unterscheidet.
     */
    private function signInRegularUser(): User
    {
        if (User::whereKey(1)->doesntExist()) {
            User::factory()->create();
        }
        Player::factory()->create();

        $user = User::factory()->create(['password' => Hash::make('testtest')]);
        $this->assertNotEquals($user->id, $user->player_id);
        $this->actingAs($user);

        return $user;
    }

    private function profile(User $user, Group $group): Profile
    {
        return $user->player->profiles()->where('group_id', $group->id)->sole();
    }
}
