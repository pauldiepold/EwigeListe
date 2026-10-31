<?php

namespace App\Console\Commands;

use App\Group;
use App\Jobs\UpdateGroup;
use App\Jobs\UpdateProfile;
use App\Profile;
use Illuminate\Console\Command;

class RecalculateStatistics extends Command
{
    protected $signature = 'stats:recalculate';

    protected $description = 'Berechnet alle Profile, Listenstatistiken und Abzeichen über die Queue neu';

    /**
     * Stellt kleine Jobs ein statt alles am Stück zu rechnen, damit keiner das Zeitlimit des Workers
     * überschreitet. Jeder Monatsjob rechnet auch die Listenstatistik neu; das ist doppelt, aber günstig.
     */
    public function handle(): int
    {
        $profiles = Profile::all();
        $profiles->each(fn (Profile $profile) => UpdateProfile::dispatch($profile));

        $groups = Group::all();
        $groups->each(function (Group $group): void {
            foreach ($group->monthsSinceFirstRound() as $month) {
                UpdateGroup::dispatch($group, $month);
            }
        });

        $this->info("Neuberechnung für {$profiles->count()} Profile und {$groups->count()} Listen eingestellt.");

        return self::SUCCESS;
    }
}
