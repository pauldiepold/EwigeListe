<?php

namespace App\Http\Controllers;

use App\Game;
use App\Round;
use App\Player;
use App\Http\Requests\StoreGame;
use Illuminate\Support\Facades\Redirect;

class   GameController extends Controller {

    public function create(Round $round)
    {
        $this->authorize('update', $round);

        $players = $round->getActivePlayers();

        return view('games.create', ['round' => $round, 'players' => $players]);
    }

    public function store(StoreGame $request, Round $round)
    {
        $this->authorize('update', $round);

        $validated = $request->validated();
        $round->addGame($validated['winners'], $validated['points']);

        return redirect('/rounds/' . $round->id);
    }

    public function destroy(Game $game)
    {
        $this->authorize('update', $game->round);

        if ($game->isNot($game->round->getLastGame()))
        {
            return Redirect::back()->withInput()->withErrors(['Du kannst nur das letzte Spiel einer Runde löschen!']);
        }

        $game->delete();

        return redirect('/rounds/' . $game->round->id);
    }

}
