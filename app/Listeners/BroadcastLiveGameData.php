<?php

namespace App\Listeners;

use App\Events\LiveGameDataBroadcasted;
use App\Events\LiveGameDataBroadcastedInaktiv;
use App\Events\LiveGameSaved;

class BroadcastLiveGameData
{
    /**
     * Handle the event.
     *
     * @param LiveGameSaved $event
     * @return void
     */
    public function handle(LiveGameSaved $event)
    {
        foreach ($event->liveGame->spielerIDs as $spielerID)
        {
            event(new LiveGameDataBroadcasted($event->liveGame, $spielerID));
        }

        event(new LiveGameDataBroadcastedInaktiv($event->liveGame));
    }
}