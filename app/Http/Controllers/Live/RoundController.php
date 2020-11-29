<?php

namespace App\Http\Controllers\Live;

use App\Events\RoundUpdated;
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

        broadcast(new RoundUpdated($liveRound->round->id));

        return 'success';
    }

    public function updateRegeln(LiveRound $liveRound, Request $request)
    {
        $validatedData = $request->validate([
            'schweinchen' => 'required|boolean',
            'fuchsSticht' => 'required|boolean',
            'schweinchenTrumpfsolo' => 'required|boolean',
            'koenigsSolo' => 'required|boolean',
            'karlchen' => 'required|boolean',
            'karlchenFangen' => 'required|boolean',
        ]);

        $liveRound->update($validatedData);

        broadcast(new RoundUpdated($liveRound->round->id));

        return 'success';
    }
}
