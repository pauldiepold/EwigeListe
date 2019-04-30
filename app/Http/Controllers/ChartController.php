<?php

namespace App\Http\Controllers;

use App\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller {

    public function roundChart(Round $round)
    {
        $points = collect();
        $names = collect();

        //Kopfzeile
        foreach ($round->players as $player)
        {
            $points->put($player->id, collect());
            $names->push($player->surname);
        }

        //Spiele
        foreach ($round->games()->oldest()->with('players')->get() as $game)
        {
            foreach ($game->players as $player)
            {
                $currentPlayerPoints = $points->get($player->id);
                $currentPlayerPoints->push($currentPlayerPoints->last() + $player->pivot->points);
            }

            foreach ($round->players as $player)
            {
                if (!$game->players->contains($player))
                {
                    $currentPlayerPoints = $points->get($player->id);
                    if ($currentPlayerPoints->count() == 0)
                    {
                        $currentPlayerPoints->push(0);
                    } else
                    {
                        $currentPlayerPoints->push($currentPlayerPoints->last());
                    }
                }
            }
        }
        $points = $points->values();

        $data = collect();
        $data->put('names', $names);
        $data->put('points', $points);
        return $data->toArray();
    }
}
