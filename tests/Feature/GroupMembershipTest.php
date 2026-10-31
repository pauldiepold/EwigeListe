<?php

namespace Tests\Feature;

use App\Group;
use App\Player;
use App\Round;
use App\User;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GroupMembershipTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Player $player;

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create(); // Benutzer 1 ist Admin
        $this->user = User::factory()->create();
        $this->player = $this->user->player;
        $this->actingAs($this->user);
    }

    #[Test]
    public function guests_cannot_change_a_list(): void
    {
        $group = Group::factory()->create();
        auth()->logout();

        $this->post(route('groups.join', $group))->assertRedirect('/login');
        $this->post(route('groups.leave', $group))->assertRedirect('/login');
        $this->patch(route('groups.close', $group), ['closed' => true])->assertRedirect('/login');
    }

    #[Test]
    public function changing_a_list_is_not_possible_with_get(): void
    {
        $group = Group::factory()->create();

        $this->get("/liste/{$group->id}/beitreten")->assertNotFound();
        $this->get("/liste/{$group->id}/verlassen")->assertNotFound();
    }

    #[Test]
    public function a_player_can_join_a_list(): void
    {
        $group = Group::factory()->create();

        $this->post(route('groups.join', $group))
            ->assertRedirect(route('groups.show', $group))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Du bist der Liste beigetreten.']);

        $this->assertTrue($group->players()->whereKey($this->player->id)->exists());
    }

    #[Test]
    public function a_player_cannot_join_a_closed_list(): void
    {
        $group = Group::factory()->closed()->create();

        $this->post(route('groups.join', $group))
            ->assertRedirect(route('groups.show', $group))
            ->assertInertiaFlash('toast', ['type' => 'error', 'message' => 'Du kannst dieser Liste nicht beitreten.']);

        $this->assertFalse($group->players()->whereKey($this->player->id)->exists());
    }

    #[Test]
    public function a_member_cannot_join_twice(): void
    {
        $group = Group::factory()->create();
        $group->addPlayer($this->player);

        $this->post(route('groups.join', $group));

        $this->assertSame(1, $group->players()->whereKey($this->player->id)->count());
    }

    #[Test]
    public function a_member_can_leave_a_list(): void
    {
        $group = Group::factory()->create();
        $group->addPlayer($this->player);
        $group->addPlayer(Player::factory()->create());

        $this->post(route('groups.leave', $group))
            ->assertRedirect(route('groups.show', $group))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Du hast die Liste verlassen.']);

        $this->assertFalse($group->players()->whereKey($this->player->id)->exists());
    }

    /**
     * @param  Closure(Player): Group  $list
     */
    #[Test]
    #[DataProvider('listsThatCannotBeLeft')]
    public function a_member_cannot_leave_a_list_when_a_rule_forbids_it(Closure $list): void
    {
        $group = $list($this->player);
        $wasMember = $group->players()->whereKey($this->player->id)->exists();

        $this->post(route('groups.leave', $group))
            ->assertRedirect(route('groups.show', $group))
            ->assertInertiaFlash('toast', ['type' => 'error', 'message' => 'Du kannst diese Liste nicht verlassen.']);

        $this->assertSame($wasMember, $group->players()->whereKey($this->player->id)->exists());
    }

    /**
     * @return array<string, array{Closure(Player): Group}>
     */
    public static function listsThatCannotBeLeft(): array
    {
        $listWithOtherMember = function (Player $player, array $attributes = []): Group {
            $group = Group::factory()->create($attributes);
            $group->addPlayer($player);
            $group->addPlayer(Player::factory()->create());

            return $group;
        };

        return [
            'kein Mitglied' => [fn (Player $player) => Group::factory()->create()],
            'letztes Mitglied' => [function (Player $player) {
                $group = Group::factory()->create();
                $group->addPlayer($player);

                return $group;
            }],
            'Ewige Liste' => [function (Player $player) {
                $group = Group::findOrFail(Group::EWIGE_LISTE_ID);
                $group->addPlayer($player);
                $group->addPlayer(Player::factory()->create());

                return $group;
            }],
            'geschlossene Liste' => [fn (Player $player) => $listWithOtherMember($player, ['closed' => true])],
            'sitzt in einer Runde der Liste' => [function (Player $player) use ($listWithOtherMember) {
                $group = $listWithOtherMember($player);
                $round = Round::factory()->create();
                $round->groups()->attach($group);
                $round->players()->attach($player, ['index' => 0]);

                return $group;
            }],
        ];
    }

    #[Test]
    public function the_creator_can_close_and_reopen_a_list(): void
    {
        $group = Group::factory()->create(['created_by' => $this->player->id]);

        $this->patch(route('groups.close', $group), ['closed' => true])
            ->assertRedirect(route('groups.show', $group))
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Die Liste wurde geschlossen.']);
        $this->assertTrue((bool) $group->refresh()->closed);

        $this->patch(route('groups.close', $group), ['closed' => false])
            ->assertInertiaFlash('toast', ['type' => 'success', 'message' => 'Die Liste wurde geöffnet.']);
        $this->assertFalse((bool) $group->refresh()->closed);
    }

    #[Test]
    public function the_admin_can_close_a_list(): void
    {
        $group = Group::factory()->create();

        $this->actingAs(User::findOrFail(1))->patch(route('groups.close', $group), ['closed' => true]);

        $this->assertTrue((bool) $group->refresh()->closed);
    }

    #[Test]
    public function other_players_cannot_close_a_list(): void
    {
        $group = Group::factory()->create();

        $this->patch(route('groups.close', $group), ['closed' => true])
            ->assertInertiaFlash('toast', ['type' => 'error', 'message' => 'Du kannst diese Liste nicht schließen oder öffnen.']);

        $this->assertFalse((bool) $group->refresh()->closed);
    }

    #[Test]
    public function the_ewige_liste_cannot_be_closed_even_by_the_admin(): void
    {
        $group = Group::findOrFail(Group::EWIGE_LISTE_ID);

        $this->actingAs(User::findOrFail(1))->patch(route('groups.close', $group), ['closed' => true]);

        $this->assertFalse((bool) $group->refresh()->closed);
    }

    #[Test]
    public function closing_requires_the_target_state(): void
    {
        $group = Group::factory()->create(['created_by' => $this->player->id]);

        $this->patch(route('groups.close', $group), [])->assertSessionHasErrors('closed');
    }
}
