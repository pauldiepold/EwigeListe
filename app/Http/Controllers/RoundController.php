<?php

namespace App\Http\Controllers;

use App\Round;
use App\Player;
use App\Game;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRound;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RoundController extends Controller {

    public function index()
    {
        $rounds = Round::latest()->with(['games', 'players'])->paginate(35);

        return view('rounds.index', compact('rounds'));
    }

    public function show(Round $round)
    {
        $activePlayers = $round->getActivePlayers();
        $lastGame = $round->getLastGame();
        $isCurrentRound = Auth::user()->player->games()->latest()->first()->round->id == $round->id ? true : false;

        $colRound = collect();
        $playerPoints = collect();

        //Kopfzeile
        $colRow = collect();
        foreach ($round->players as $player)
        {
            $colItem = collect($player->surname);
            $colItem->push($player->id);
            $player->pivot->index == $round->getDealerIndex() ? $colItem->push('dealer') : '';
            $activePlayers->pluck('id')->contains($player->id) && $round->players->count() > 5 ? $colItem->push('active') : '';

            $colRow->push($colItem);
        }
        $colRound->push($colRow);

        //Spiele
        foreach ($round->games()->oldest()->with('players')->get() as $game)
        {
            $colRow = collect();
            foreach ($round->players as $player)
            {
                if ($game->players->pluck('id')->contains($player->id))
                {
                    $playerPoints->put($player->id, $playerPoints->get($player->id) + $game->players->where('id', $player->id)->first()->pivot->points);
                    $colItem = collect($playerPoints->get($player->id));

                    $game->players->where('id', $player->id)->first()->pivot->won ? $colItem->push('won') : '';
                } else
                {
                    $colItem = collect('-');
                }
                $colRow->push($colItem);
            }

            $colRow->push(collect($game->points));

            ($game->dealerIndex + 1 == $round->players->count()) && !$game->solo ? $colRow->push('endOfRound') : '';

            $game->solo ? $colRow->push('solo') : '';
            $game->misplay ? $colRow->push('misplay') : '';
            $colRound->push($colRow);
        }

        return view('rounds.show', compact(
            'round',
            'colRound',
            'activePlayers',
            'lastGame',
            'isCurrentRound'
        ));
    }

    public function create($numberOfPlayers = 4)
    {
        if ($numberOfPlayers < 4 || $numberOfPlayers > 7)
        {
            $numberOfPlayers = 4;
        }

        $players = Player::join('profiles', 'players.id', '=', 'profiles.player_id')
            ->where('players.hide', '=', '0')
            ->orderBy('profiles.games', 'desc')
            ->select('players.*')
            ->get();

        return view('rounds.create', compact('players', 'numberOfPlayers'));
    }

    public function store(StoreRound $request)
    {
        $validated = collect($request->validated());
        $playerIDs = collect($validated->get('players'));

        $round = Round::create(['created_by' => Auth::user()->player->id]);

        $index = 0;
        foreach ($playerIDs as $playerID)
        {
            $round->players()->attach(Player::find($playerID)->id, [
                'index' => $index
            ]);
            $index++;
        }

        return redirect('/rounds/' . $round->id);
    }

    public function destroy(Round $round)
    {
        $this->authorize('update', $round);

        if ($round->games->count() != 0)
        {
            return Redirect::back()->with('deleteError', 'Du kannst nur eine Runde ohne Spiele löschen!');
        }

        $round->delete();

        return redirect('/rounds/create');
    }

}
