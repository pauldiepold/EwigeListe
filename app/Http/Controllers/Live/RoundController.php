<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Live\Deck;
use App\Live\Spieler;
use App\Live\Stich;
use App\LiveRound;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    public function starteNeuesSpiel(LiveRound $liveRound)
    {
        $this->authorize('update', $liveRound);

        $liveRound->starteNeuesSpiel();

        return 'success';
    }
}
