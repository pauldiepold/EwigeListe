<?php

namespace Tests\Feature;

use App\Group;
use App\Jobs\UpdateGroup;
use App\Jobs\UpdateProfile;
use App\Player;
use App\Profile;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\TestCase;

class StatisticsJobsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_game_entered_while_a_profile_is_calculated_queues_another_calculation(): void
    {
        Bus::fake([UpdateProfile::class]);
        $profile = $this->queuedProfile();

        $job = new UpdateProfile($this->profileCalculatingWith($profile, function () use ($profile): void {
            Profile::find($profile->id)->updateStats();
        }));
        $job->handle();

        Bus::assertDispatched(UpdateProfile::class);
        $this->assertTrue((bool) $profile->fresh()->queued);
    }

    #[Test]
    public function a_failed_profile_calculation_does_not_block_later_ones(): void
    {
        $profile = $this->queuedProfile();

        $job = new UpdateProfile($this->profileCalculatingWith($profile, function (): void {
            throw new RuntimeException('Berechnung fehlgeschlagen');
        }));

        try {
            $job->handle();
            $this->fail('Der Job hätte die Exception weiterreichen müssen.');
        } catch (RuntimeException) {
            $this->assertFalse((bool) $profile->fresh()->queued);
        }
    }

    #[Test]
    public function a_game_entered_while_a_list_is_calculated_queues_another_calculation(): void
    {
        Bus::fake([UpdateGroup::class]);
        $group = $this->queuedGroup();

        $job = new UpdateGroup($this->groupCalculatingWith($group, function () use ($group): void {
            Group::find($group->id)->updateStats();
        }), null);
        $job->handle();

        Bus::assertDispatched(UpdateGroup::class);
        $this->assertTrue((bool) $group->fresh()->queued);
    }

    #[Test]
    public function a_failed_list_calculation_does_not_block_later_ones(): void
    {
        $group = $this->queuedGroup();

        $job = new UpdateGroup($this->groupCalculatingWith($group, function (): void {
            throw new RuntimeException('Berechnung fehlgeschlagen');
        }), null);

        try {
            $job->handle();
            $this->fail('Der Job hätte die Exception weiterreichen müssen.');
        } catch (RuntimeException) {
            $this->assertFalse((bool) $group->fresh()->queued);
        }
    }

    private function queuedGroup(): Group
    {
        $group = Group::factory()->create();
        $group->queued = true;
        $group->save();

        return $group;
    }

    private function queuedProfile(): Profile
    {
        $group = Group::factory()->create();
        $player = Player::factory()->create();
        $group->addPlayer($player);

        $profile = Profile::where('group_id', $group->id)->where('player_id', $player->id)->firstOrFail();
        $profile->queued = true;
        $profile->save();

        return $profile;
    }

    private function groupCalculatingWith(Group $group, Closure $calculation): Group
    {
        $double = new class extends Group
        {
            public Closure $calculation;

            protected $table = 'groups';

            public function calculate($date = null): void
            {
                ($this->calculation)();
            }
        };
        $double->calculation = $calculation;
        $double->setRawAttributes($group->getAttributes(), true);
        $double->exists = true;

        return $double;
    }

    private function profileCalculatingWith(Profile $profile, Closure $calculation): Profile
    {
        $double = new class extends Profile
        {
            public Closure $calculation;

            public function calculate(): void
            {
                ($this->calculation)();
            }
        };
        $double->calculation = $calculation;
        $double->setRawAttributes($profile->getAttributes(), true);
        $double->exists = true;

        return $double;
    }
}
