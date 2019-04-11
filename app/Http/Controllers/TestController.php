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

	public function test() {
		
		$round = Round::find(1);
	
		$colRound = collect();
		
		foreach ($round->games as $game) {	
			$colRow = collect();
			
			foreach ($game->players as $player) {
				$colItem = collect();
				$colItem->push($player->name);
				
				$colRow->push($colItem);
			}
			$colItem = collect();
			$colItem->push($game->points);
			$colRow->push($colItem);
			
			if ($game->solo) {
				$colRow->push('solo');
			}

			$colRound->push($colRow);
		}
		dd($colRound);
		
	}
}
