<?php

namespace App\Http\Controllers;

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
        $misplay = array_key_exists('misplayed', $validated) ? true : false;
        $winners = $validated['winners'];
        $pointsRound = $validated['points'];

        $players = $round->getActivePlayers();
        $solo = (count($winners) != 2 ? true : false);
        $solo = $misplay ? false : $solo;

        $game = Game::create([
            'points' => $pointsRound,
            'solo' => $solo,
            'misplay' => $misplay,
            'dealerIndex' => $round->getDealerIndex(),
            'created_by' => Auth::user()->player->id,
            'round_id' => $round->id,
        ]);

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

            $game->players()->attach($player->id, [
                'won' => $won,
                'soloist' => $soloist,
                'points' => $points,
                'misplayed' => $misplayed,
            ]);
        }

        $groups = $round->groups;

        $profiles = Profile::whereIn('group_id', $groups->pluck('id'))
            ->whereIn('player_id', $players->pluck('id'))
            ->get();

        Profile::updateManyStats($profiles);
        Group::updateManyStats($groups);

        return redirect($round->path());
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

        /*
        foreach ($game->players()->with('profile')->get() as $player)
        {
            if (!$player->profile->queued)
            {
                $player->profile->queued = true;
                $player->profile->save();
                UpdateProfile::dispatch($player->profile);
            }
        }*/

        return redirect($round->path());
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

        //$players = $game->players()->with('profile')->get();
        /*
        foreach ($players as $player)
        {
            if (!$player->profile->queued)
            {
                $player->profile->queued = true;
                $player->profile->save();
                UpdateProfile::dispatch($player->profile);
            }
        }*/

        return redirect($round->path());
    }
}
