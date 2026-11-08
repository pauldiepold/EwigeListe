<?php

namespace App\Http\Controllers\Live;

use App\Events\RoundUpdated;
use App\Http\Controllers\Controller;
use App\Models\LiveRound;

class RoundController extends Controller
{
    public function starteNeuesSpiel(LiveRound $liveRound)
    {
        $this->authorize('update', $liveRound);

        abort_if($liveRound->currentLiveGame() !== null, 422, 'Es läuft bereits ein Spiel!');

        $liveRound->starteNeuesSpiel();

        broadcast(new RoundUpdated($liveRound->round->id));

        return 'success';
    }
}
