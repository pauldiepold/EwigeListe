<?php

namespace App\Console\Commands;

use App\Group;
use App\Jobs\UpdateGroup;
use App\Jobs\UpdateProfile;
use App\Profile;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;

class RecalculateStatistics extends Command
{
    protected $signature = 'stats:recalculate';

    protected $description = 'Berechnet alle Profile, Listenstatistiken und Abzeichen über die Queue neu';

    /**
     * Stellt kleine Jobs ein statt alles am Stück zu rechnen: So überschreitet keiner das Zeitlimit
     * des Workers, und neu eingetragene Spiele müssen nicht auf das Ende warten.
     */
    public function handle(): int
    {
        $profiles = Profile::all();
        $profiles->each(fn (Profile $profile) => UpdateProfile::dispatch($profile));

        $groups = Group::all();
        $groups->each(function (Group $group): void {
            foreach ($this->monthsSinceFirstRound($group) as $month) {
                UpdateGroup::dispatch($group, $month);
            }
        });

        $this->info("Neuberechnung für {$profiles->count()} Profile und {$groups->count()} Listen eingestellt.");

        return self::SUCCESS;
    }

    /**
     * Eine Liste ohne Runden berechnet nur den aktuellen Monat.
     */
    private function monthsSinceFirstRound(Group $group): CarbonPeriod
    {
        $firstRoundAt = $group->rounds()->min('rounds.created_at') ?? Carbon::now();

        return CarbonPeriod::create(Carbon::parse($firstRoundAt)->startOfMonth(), '1 month', Carbon::now());
    }
}
