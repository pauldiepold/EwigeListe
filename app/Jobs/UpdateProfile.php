<?php

namespace App\Jobs;

use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateProfile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Profile $profile) {}

    /**
     * Setzt `queued` vor der Berechnung zurück, damit ein währenddessen eingetragenes Spiel eine
     * neue Berechnung auslöst und ein Fehlschlag spätere Berechnungen nicht blockiert.
     */
    public function handle(): void
    {
        $this->profile->queued = false;
        $this->profile->save();

        $this->profile->calculate();
    }
}
