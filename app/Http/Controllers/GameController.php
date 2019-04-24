<?php

namespace App\Http\Controllers;

use App\Game;
use App\Round;
use App\Player;
use App\Http\Requests\StoreGame;
use App\Http\Requests\UpdateGame;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use App\Jobs\UpdateProfile;

class   GameController extends Controller {


    public function store(StoreGame $request, Round $round)
    {
        $this->authorize('update', $round);

        $validated = $request->validated();
		$misplay = array_key_exists('misplayed', $validated) ? true : false;
        $round->addGame($validated['winners'], $validated['points'], $misplay);
		foreach($round->players()->with('profile')->get() as $player) {
			UpdateProfile::dispatch($player->profile);
		}

        return redirect('/rounds/' . $round->id);
    }

    public function update(UpdateGame $request, Game $game)
    {
        $round = $game->round;
        $this->authorize('update', $round);

        $validated = $request->validated();
		$misplay = array_key_exists('updateMisplayed', $validated) ? true : false;
        $winners = $validated['updateWinners'];
        $pointsRound = $validated['updatePoints'];

        $players = $game->players;
        $solo = (count($winners) != 2 ? true : false);
		$solo = $misplay ? false : $solo;

        $game->points = $pointsRound;
        $game->solo = $solo;
		$game->misplay = $misplay;
        $game->save();

        foreach ($players as $player)
        {
            if (count($winners) == 1 &&
                in_array($player->id, $winners))           // Solo gewonnen
            {
                $soloist = true;
                $won = true;
                $points = 3 * $pointsRound;
				$misplayed = false;
            } elseif (count($winners) == 3 &&
                      !in_array($player->id, $winners))    // Solo verloren
            {
                $soloist = $misplay ? false : true;
                $won = false;
                $points = -3 * $pointsRound;
				$misplayed = $misplay ? true : false;
            } elseif ((count($winners) == 2 &&
                       in_array($player->id, $winners)) ||
                      (count($winners) == 3 &&
                       in_array($player->id, $winners)))    // Normalspiel gewonnen - Gegen Solo gewonnen
            {
                $soloist = false;
                $won = true;
                $points = 1 * $pointsRound;
				$misplayed = false;
            } elseif ((count($winners) == 2 &&
                       !in_array($player->id, $winners)) ||
                      (count($winners) == 1 &&
                       !in_array($player->id, $winners)))   // Normalspiel verloren - Gegen Solo verloren
            {
                $soloist = false;
                $won = false;
                $points = -1 * $pointsRound;
				$misplayed = false;
            }

            $player->pivot->won = $won;
            $player->pivot->soloist = $soloist;
            $player->pivot->points = $points;
			$player->pivot->misplayed = $misplayed;
            $player->pivot->save();
        }
        return redirect('/rounds/' . $round->id);
    }

    public function destroy(Game $game)
    {
        $this->authorize('update', $game->round);

        if ($game->isNot($game->round->getLastGame()))
        {
            return Redirect::back()->withInput()->withErrors(['Du kannst nur das letzte Spiel einer Runde löschen!']);
        }
		
		DB::table('game_player')->where('game_id', $game->id)->delete();

        $game->delete();

        return redirect('/rounds/' . $game->round->id);
    }

}
