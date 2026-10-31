<?php

namespace Tests\Feature;

use App\Badge;
use App\Group;
use App\Jobs\UpdateProfile;
use App\Player;
use App\Profile;
use App\Round;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RecalculateStatisticsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_command_recalculates_profiles_lists_and_the_badges_of_every_month(): void
    {
        Carbon::setTestNow('2026-03-15');
        $group = Group::factory()->create(['totalGames' => 99]);
        $group->addPlayer(Player::factory()->create());
        Round::factory()->create(['created_at' => '2026-01-20'])->groups()->attach($group);
        $profile = $group->profiles()->first();
        $profile->update(['games' => 99, 'queued' => true]);

        $this->artisan('stats:recalculate')->assertSuccessful();

        $this->assertSame(0, Profile::find($profile->id)->games);
        $this->assertSame(0, $group->fresh()->totalGames);
        $this->assertSame([1, 2, 3], Badge::where('group_id', $group->id)->where('type', 'points')->orderBy('month')->pluck('month')->all());
    }

    #[Test]
    public function the_command_copes_with_a_list_without_rounds(): void
    {
        $group = Group::factory()->create(['totalGames' => 99]);

        $this->artisan('stats:recalculate')->assertSuccessful();

        $this->assertSame(0, $group->fresh()->totalGames);
    }

    #[Test]
    public function the_admin_can_start_the_recalculation_from_the_list_overview(): void
    {
        Bus::fake([UpdateProfile::class]);
        $admin = User::factory()->create();
        Group::findOrFail(Group::EWIGE_LISTE_ID)->addPlayer($admin->player);

        $this->actingAs($admin)->get(route('groups.index'))
            ->assertInertia(fn (Assert $page) => $page->where('canRecalculateStatistics', true));

        $this->from(route('groups.index'))->post(route('statistics.recalculate'))
            ->assertRedirect(route('groups.index'))
            ->assertInertiaFlash('toast.type', 'success');

        Bus::assertDispatched(UpdateProfile::class);
    }

    #[Test]
    public function other_players_cannot_start_the_recalculation(): void
    {
        Bus::fake([UpdateProfile::class]);
        User::factory()->create(); // Benutzer 1 ist Admin
        $this->actingAs(User::factory()->create());

        $this->get(route('groups.index'))
            ->assertInertia(fn (Assert $page) => $page->where('canRecalculateStatistics', false));

        $this->post(route('statistics.recalculate'))->assertForbidden();

        Bus::assertNotDispatched(UpdateProfile::class);
    }
}
