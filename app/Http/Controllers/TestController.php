<?php

namespace App\Http\Controllers;

use App\Round;
use App\Player;
use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreRound;
use Illuminate\Support\Facades\Redirect;

class TestController extends Controller {

    public function test()
    {
        $round = Round::find(1);

        $colRound = collect();
        $colRow = collect();

        //Kopfzeile
        foreach ($round->players as $player)
        {
            $colItem = collect();
            $colItem->push($player->surname);
            $player->pivot->index == $round->getDealerIndex() ? $colItem->push('dealer') : '';

            $colRow->push($colItem);
        }
        $colRound->push($colRow);

        //Spiele
        foreach ($round->games as $game)
        {
            $colRow = collect();

            foreach ($game->players as $player)
            {
                $colItem = collect();
                $colItem->push($player->name);
                $player->pivot->won ? $colItem->push('won') : '';
                $colRow->push($colItem);
            }

            $colItem = collect();
            $colItem->push($game->points);
            $colRow->push($colItem);

            $game->solo ? $colRow->push('solo') : '' ;
            $colRound->push($colRow);
        }
    }
}
