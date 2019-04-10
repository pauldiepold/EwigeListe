<?php

namespace App\Http\Controllers;

use App\Game;
use App\Round;
use App\Player;
use App\Http\Requests\StoreGame;
use Illuminate\Support\Facades\Redirect;

class   GameController extends Controller {

    public function create()
    {
        $round = Player::find(1)->rounds->where('active', 1)->first();

        $players = $round->getActivePlayers();

        return view('games.create', ['round' => $round, 'players' => $players]);
    }

    public function store(StoreGame $request, Round $round)
    {
        $validated = $request->validated();
        $winners = $validated['winners'];
        $pointsRound = $validated['points'];

        $players = $round->getActivePlayers();
        $solo = (count($winners) != 2 ? true : false);

        $game = Game::create([
            'points' => $pointsRound,
            'solo' => $solo,
        ]);
        $game->round()->associate($round)->save();

        foreach ($players as $player)
        {
            if (count($winners) == 1 &&
                in_array($player->id, $winners))           // Solo gewonnen
            {
                $soloist = true;
                $won = true;
                $points = 3 * $pointsRound;
            } elseif (count($winners) == 3 &&
                      !in_array($player->id, $winners))    // Solo verloren
            {
                $soloist = true;
                $won = false;
                $points = -3 * $pointsRound;
            } elseif ((count($winners) == 2 &&
                       in_array($player->id, $winners)) ||
                      (count($winners) == 3 &&
                       in_array($player->id, $winners)))    // Normalspiel gewonnen - Gegen Solo gewonnen
            {
                $soloist = false;
                $won = true;
                $points = 1 * $pointsRound;
            } elseif ((count($winners) == 2 &&
                       !in_array($player->id, $winners)) ||
                      (count($winners) == 1 &&
                       !in_array($player->id, $winners)))   // Normalspiel verloren - Gegen Solo verloren
            {
                $soloist = false;
                $won = false;
                $points = -1 * $pointsRound;
            }

            $game->players()->attach($player->id, [
                'won' => $won,
                'soloist' => $soloist,
                'points' => $points
            ]);
        }

        return redirect('/rounds/1');
    }

    public function show(Game $game)
    {
        $game->getDealerIndex();
    }

    public function showDelete(Round $round)
    {
        return view('games.delete', ['game' => $round->getLastGame()]);
    }

    public function destroy(Game $game)
    {
        if ($game->isNot($game->round->getLastGame()))
        {
            return Redirect::back()->withInput()->withErrors(['Du kannst nur das letzte Spiel einer Runde löschen!']);
        }


        $game->delete();

        return redirect('/rounds/1');
    }

}
