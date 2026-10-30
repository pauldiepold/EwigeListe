<?php

namespace App\Http\Controllers;

use App\Events\RoundUpdated;
use App\Game;
use App\Group;
use App\Http\Requests\StoreGame;
use App\Profile;
use App\Round;
use Illuminate\Support\Facades\Redirect;

class GameController extends Controller
{
    public function store(StoreGame $request, Round $round)
    {
        $this->authorize('update', $round);

        $validated = $request->validated();
        $winners = $validated['winners'];
        $pointsRound = $validated['points'];
        $misplay = $validated['misplayed'];

        $game = $round->addNewGame($winners, $pointsRound, $misplay);

        broadcast(new RoundUpdated($round->id))->toOthers();
        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($round->groups, $round->updated_at);

        return 'success';
    }

    public function destroy(Game $game)
    {
        $round = $game->round;

        $this->authorize('update', $round);

        if ($game->isNot($round->getLastGame())) {
            return Redirect::back()->withInput()->withErrors(['Du kannst nur das letzte Spiel einer Runde löschen!']);
        }

        $game->delete();

        broadcast(new RoundUpdated($round->id))->toOthers();
        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($round->groups, $round->updated_at);

        return 'success';
    }
}
