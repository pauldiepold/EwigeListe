<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Live\Deck;
use App\Live\Spieler;
use App\Live\Tisch;
use App\LiveRound;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    public function starteNeuesSpiel(LiveRound $liveRound)
    {
        $this->authorize('update', $liveRound);

        // Checke, ob entweder noch kein Spiel in Runde
        // oder alle anderen Spiele geschlossen sind
        if ($liveRound->liveGames->count() != 0 ||
            $liveRound->liveGames()->where('closed', 0)->count() != 0)
        {
            //dd('fehler');
        }

        $activePlayers = $liveRound->round->getActivePlayers(false);

        $deck = new Deck();
        $spieler = collect();
        $spielerIDs = collect();
        $spielerIndize = collect();

        foreach ($activePlayers as $key => $player)
        {
            $spieler->push(
                new Spieler(
                    $player->id,
                    $player->surname . ' ' . $player->name,
                    $player->pivot->index,
                    $deck->deck->get($key)
                )
            );
            $spielerIDs->push($player->id);
            $spielerIndize->push($player->pivot->index);
        }

        $liveGame = $liveRound->liveGames()->create([
            'live_round_id' => $liveRound->id,
            'vorhand' => $activePlayers->first()->pivot->index,
            'spieler0' => $spieler->get(0),
            'spieler1' => $spieler->get(1),
            'spieler2' => $spieler->get(2),
            'spieler3' => $spieler->get(3),
            'spielerIDs' => $spielerIDs,
            'spielerIndize' => $spielerIndize,
        ]);

        return 'success';
    }
}
