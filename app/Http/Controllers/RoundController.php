<?php

namespace App\Http\Controllers;

use App\Round;
use App\Player;
use App\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Requests\StoreRound;
use Illuminate\Support\Facades\Redirect;

class RoundController extends Controller {

    public function index()
    {
        $rounds = Round::latest()->with(['games', 'players'])->paginate(50);

        return view('rounds.index', compact('rounds'));
    }

    public function show(Round $round)
    {
        $activePlayers = $round->getActivePlayers();
        $activePlayerIDs = $activePlayers->pluck('id');

        $lastGame = $round->getLastGame();

        $colRound = collect();
        $colRow = collect();
        $playerPoints = collect();

        //Kopfzeile
        foreach ($round->players as $player)
        {
            $colItem = collect();
            $colItem->push($player->surname);
            $colItem->push($player->id);
            $player->pivot->index == $round->getDealerIndex() ? $colItem->push('dealer') : '';
            $activePlayerIDs->contains($player->id) && $round->players->count() > 5 ?  $colItem->push('active') : '';

            $colRow->push($colItem);
        }
        $colRound->push($colRow);

        //Spiele
        foreach ($round->games()->oldest()->with('players')->get() as $game)
        {
            $colRow = collect();

            foreach ($game->players as $player)
            {
                $colItem = collect();

                $playerPoints->put($player->id, $playerPoints->get($player->id) + $player->pivot->points);
                $colItem->push($playerPoints->get($player->id));

                $player->pivot->won ? $colItem->push('won') : '';

                $colRow->put($player->id, $colItem);
            }

            foreach ($round->players as $player)
            {
                if (!$colRow->has($player->id))
                {
                    $colItem = collect();
                    $colItem->push('-');
                    $colRow->put($player->id, $colItem);
                }
            }

            $colItem = collect();
            $colItem->push($game->points);
            $colRow->put('points', $colItem);

            ($game->dealerIndex + 1 == $round->players->count()) && !$game->solo ? $colRow->push('endOfRound') : '';

            $game->solo ? $colRow->push('solo') : '';
            $colRound->push($colRow);
        }
        //dd($colRound);
        return view('rounds.show', compact('round', 'colRound', 'activePlayers', 'lastGame'));
    }

    public function create($numberOfPlayers = 4)
    {
        if ($numberOfPlayers < 4 || $numberOfPlayers > 7)
        {
            abort(404);
        }

        $players = Player::join('profiles', 'players.id', '=', 'profiles.player_id')
            ->where('players.hide', '=', '0')
            ->orderBy('profiles.games', 'desc')
            ->select('players.*')
            ->get();

        return view('rounds.create', ['players' => $players, 'numberOfPlayers' => $numberOfPlayers]);
    }

    public function store(StoreRound $request)
    {
        $players_array = $request->validated();
        $players_array = Arr::except($players_array, ['_token']);

        if (count($players_array) != count(array_unique($players_array)))
        {
            return Redirect::back()->withInput()->withErrors(['Bitte keinen Namen doppelt auswählen!']);
        }

        $round = Round::create();

        $players = Player::find($players_array);

        //Spieler sortieren wie in $players_array
        $players = $players->sortBy(function ($model) use ($players_array)
        {
            return array_search($model->getKey(), $players_array);
        });

        $index = 0;
        foreach ($players as $player)
        {
            $round->players()->attach($player->id, [
                'index' => $index
            ]);
            $index++;
        }

        return redirect('/rounds/' . $round->id);
    }

}
