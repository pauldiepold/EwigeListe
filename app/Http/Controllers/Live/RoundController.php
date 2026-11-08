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

        $liveRound->starteNeuesSpiel();

        broadcast(new RoundUpdated($liveRound->round->id));

        return 'success';
    }
}
