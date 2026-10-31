<?php

namespace App\Jobs;

use App\Group;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Group $group, protected ?Carbon $date = null) {}

    /**
     * Setzt `queued` vor der Berechnung zurück, damit ein währenddessen eingetragenes Spiel eine
     * neue Berechnung auslöst und ein Fehlschlag spätere Berechnungen nicht blockiert.
     */
    public function handle(): void
    {
        $this->group->queued = false;
        $this->group->save();

        $this->group->calculate($this->date);
    }
}
