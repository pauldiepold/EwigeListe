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
        if (Auth::user()->player->games->count() > 0)
        {
            $isCurrentRound = Auth::user()->player->games()->latest()->first()->round->id == $round->id ? true : false;
        } else
        {
            $isCurrentRound = false;
        }

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
        foreach ($round->games()->with('players')->get() as $game)
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

            ($game->dealerIndex + 1 == $round->players->count()) && !$game->solo && !$game->misplay ? $colRow->push('endOfRound') : '';

            $game->solo ? $colRow->push('solo') : '';
            $game->misplay ? $colRow->push('misplay') : '';
            $colRound->push($colRow);
        }

        //dd($colRound->toArray());
        return view('rounds.show', compact(
            'round',
            'colRound',
            'activePlayers',
            'lastGame',
            'isCurrentRound'
        ));
    }

    public function createAlt()
    {
        $players = Player::join('profiles', 'players.id', '=', 'profiles.player_id')
            ->where('players.hide', '=', '0')
            ->orderBy('profiles.games', 'desc')
            ->select('players.*')
            ->get();

        return view('rounds.create', compact('players'));
    }

    public function create()
    {
        $allPlayers = Player::join('profiles', 'players.id', '=', 'profiles.player_id')
            ->where('players.hide', '=', '0')
            ->orderBy('profiles.games', 'desc')
            ->select('players.*')
            ->with('groups')
            ->get();

        return view('rounds.create-new', compact('allPlayers'));
    }

    public function store(StoreRound $request)
    {
        $validated = collect($request->validated());

        $numberOfPlayers = $validated->get('numberOfPlayers');
        $playerIDs = collect($validated->get('players'))->take($numberOfPlayers);

        $round = Round::create(['created_by' => Auth::user()->player->id]);

        $index = 0;
        foreach ($playerIDs as $playerID)
        {
            $round->players()->attach(Player::find($playerID)->id, [
                'index' => $index
            ]);
            $index++;
        }

        return $round->path();
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
