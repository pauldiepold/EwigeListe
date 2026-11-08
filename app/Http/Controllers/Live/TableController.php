<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Http\Resources\LiveGame as LiveGameResource;
use App\Http\Resources\LiveRoundPageResource;
use App\Models\Round;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TableController extends Controller
{
    /**
     * Die Live-Seite einer Runde. Die Props haben dieselbe Form wie die Broadcasts `LiveGameDataBroadcasted`
     * (`liveGame`, `ich`) und `LiveGameDataBroadcastedInaktiv` (`liveGame`), damit Pushes sie später ersetzen können.
     * `ich` mit Hand und möglichen Vorbehalten bekommt nur ein Spieler des aktuellen Spiels.
     */
    public function show(Request $request, Round $round): Response
    {
        abort_unless($round->isLive(), 404);

        $round->load(['players.user', 'liveRound'])->loadCount('games');

        $liveRound = $round->liveRound;
        $currentGame = $liveRound->currentLiveGame();
        $lastGame = $liveRound->lastLiveGame();
        $playerId = $request->user()->player_id;

        return Inertia::render('Live/Show', [
            'round' => (new LiveRoundPageResource($round))->resolve($request),
            'liveGame' => $currentGame ? (new LiveGameResource($currentGame))->resolve($request) : null,
            'lastLiveGame' => $lastGame ? (new LiveGameResource($lastGame))->resolve($request) : null,
            'ich' => $currentGame?->spielerIDs->contains($playerId) ? $currentGame->getSpieler($playerId) : null,
        ]);
    }
}
