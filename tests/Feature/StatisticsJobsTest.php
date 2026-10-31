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

        $job = new UpdateProfile($this->profileCalculatingAfter($profile, function () use ($profile): void {
            Profile::find($profile->id)->updateStats();
        }));
        $job->handle();

        Bus::assertDispatched(UpdateProfile::class);
        $this->assertTrue((bool) $profile->fresh()->queued);
    }

    #[Test]
    public function a_profile_can_be_calculated_again_after_a_failed_calculation(): void
    {
        Bus::fake([UpdateProfile::class]);
        $profile = $this->queuedProfile();

        $job = new UpdateProfile($this->profileCalculatingAfter($profile, function (): void {
            throw new RuntimeException('Berechnung fehlgeschlagen');
        }));
        $this->assertThrows(fn () => $job->handle(), RuntimeException::class);

        $profile->fresh()->updateStats();

        Bus::assertDispatched(UpdateProfile::class);
    }

    #[Test]
    public function a_game_entered_while_a_list_is_calculated_queues_another_calculation(): void
    {
        Bus::fake([UpdateGroup::class]);
        $group = Group::factory()->create(['queued' => true]);

        $job = new UpdateGroup($this->groupCalculatingAfter($group, function () use ($group): void {
            Group::find($group->id)->updateStats();
        }));
        $job->handle();

        Bus::assertDispatched(UpdateGroup::class);
        $this->assertTrue((bool) $group->fresh()->queued);
    }

    #[Test]
    public function a_list_can_be_calculated_again_after_a_failed_calculation(): void
    {
        Bus::fake([UpdateGroup::class]);
        $group = Group::factory()->create(['queued' => true]);

        $job = new UpdateGroup($this->groupCalculatingAfter($group, function (): void {
            throw new RuntimeException('Berechnung fehlgeschlagen');
        }));
        $this->assertThrows(fn () => $job->handle(), RuntimeException::class);

        $group->fresh()->updateStats();

        Bus::assertDispatched(UpdateGroup::class);
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

    /**
     * Die echte Berechnung läuft, nachdem `$interruption` zu ihrem Start ausgeführt wurde.
     */
    private function groupCalculatingAfter(Group $group, Closure $interruption): Group
    {
        $double = new class extends Group
        {
            public Closure $interruption;

            protected $table = 'groups';

            public function getForeignKey(): string
            {
                return 'group_id';
            }

            public function joiningTableSegment(): string
            {
                return 'group';
            }

            public function calculate($date = null): void
            {
                ($this->interruption)();

                parent::calculate($date);
            }
        };
        $double->interruption = $interruption;
        $double->setRawAttributes($group->getAttributes(), true);
        $double->exists = true;

        return $double;
    }

    /**
     * Die echte Berechnung läuft, nachdem `$interruption` zu ihrem Start ausgeführt wurde.
     */
    private function profileCalculatingAfter(Profile $profile, Closure $interruption): Profile
    {
        $double = new class extends Profile
        {
            public Closure $interruption;

            public function calculate(): void
            {
                ($this->interruption)();

                parent::calculate();
            }
        };
        $double->interruption = $interruption;
        $double->setRawAttributes($profile->getAttributes(), true);
        $double->exists = true;

        return $double;
    }
}
