<?php

namespace App\Http\Controllers;

use App\Events\RoundUpdated;
use App\Game;
use App\Group;
use App\Profile;
use App\Round;
use App\Player;
use App\Http\Requests\StoreGame;
use App\Http\Requests\UpdateGame;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class   GameController extends Controller
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

        if ($game->isNot($round->getLastGame()))
        {
            return Redirect::back()->withInput()->withErrors(['Du kannst nur das letzte Spiel einer Runde löschen!']);
        }

        $game->delete();

        broadcast(new RoundUpdated($round->id))->toOthers();
        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($round->groups, $round->updated_at);

        return 'success';
    }
}
