<?php

namespace App\Http\Controllers;

use App\Round;
use App\Player;
use App\Group;
use App\Game;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreRound;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RoundController extends Controller
{

    public function index(Group $group = null)
    {
        $selectedGroup = isset($group) ? $group : Group::find(1);

        $groups = Group::all();

        $rounds = Round::whereHas('groups', function (Builder $query) use ($selectedGroup)
        {
            $query->where('groups.id', '=', $selectedGroup->id);
        })
            ->latest()
            ->with(['players'])
            ->withCount('games')
            ->get();

        return view('rounds.index', compact('rounds', 'groups', 'selectedGroup'));
    }

    public function show(Round $round)
    {
        $round->load('players', 'groups');
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
        $dealerIndex = $round->getDealerIndex();

        //Kopfzeile
        $colRow = collect();
        foreach ($round->players as $player)
        {
            $colItem = collect($player->surname);
            $colItem->push($player->path());
            $player->pivot->index == $dealerIndex ? $colItem->push('dealer') : '';
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

        return view('rounds.show', compact(
            'round',
            'colRound',
            'activePlayers',
            'lastGame',
            'isCurrentRound'
        ));
    }

    public function create()
    {
        $allPlayers = Player::where('hide', '=', '0')
            ->with('groups')
            ->get();

        return view('rounds.create', compact('allPlayers'));
    }

    public function store(StoreRound $request)
    {
        $validated = collect($request->validated());
        $players = Player::find($validated->get('players'));

        $groups = Group::find(
            collect($validated->get('groups'))->prepend(1)
        );

        $round = Round::create(['created_by' => auth()->user()->player->id]);

        $index = 0;
        foreach ($validated->get('players') as $playerID)
        {
            $round->players()->attach($playerID, [
                'index' => $index
            ]);
            $index++;
        }

        $round->groups()->saveMany($groups);

        foreach ($groups as $group)
        {
            $group->addPlayers($players);
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

        return redirect()->route('rounds.create');
    }

}
