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
        $rounds = Round::orderBy('created_at', 'asc')->paginate(50);

        return view('rounds.index', ['rounds' => $rounds]);
    }

    public function create($numberOfPlayers = 4)
    {

        if ($numberOfPlayers < 4 || $numberOfPlayers > 7)
        {
            abort(404);
        }

        $players = Player::all();

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

        $index = 1;
        foreach ($players as $player)
        {
            $round->players()->attach($player->id, [
                'index' => $index
            ]);
            $index++;
        }

        return redirect('/rounds');
    }

    public function show(Round $round)
    {
        $games = $round->games;

        return view('rounds.show', ['round' => $round, 'games' => $games]);
    }

    public function edit(Round $round)
    {
        //
    }

    public function update(Request $request, Round $round)
    {
        //
    }

    public function destroy(Round $round)
    {
        //
    }
}
