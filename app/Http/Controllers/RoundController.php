<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRound;
use App\Profile;
use App\Round;
use App\Player;
use App\Group;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreRound;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class RoundController extends Controller
{

    public function index(Group $group = null)
    {
        $selectedGroup = isset($group) ? $group : Group::find(1);

        $groups = Group::all();

        $rounds_count = Round::whereHas('groups', function (Builder $query) use ($selectedGroup)
        {
            $query->where('groups.id', '=', $selectedGroup->id);
        })
            ->count();

        return view('rounds.index', compact('rounds_count', 'groups', 'selectedGroup'));
    }

    public function show(Round $round)
    {
        $round->load('players.groups', 'groups');
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
            ->withCount('gamePlayers')
            ->orderByRaw('game_players_count desc')
            ->get();

        return view('rounds.create', compact('allPlayers'));
    }

    public function store(StoreRound $request)
    {
        $validated = collect($request->validated());
        $players = Player::find($validated->get('players'));

        $groups = Group::find(
            $validated->get('groups')
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

    public function update(UpdateRound $request, Round $round)
    {
        $this->authorize('update', $round);

        $validated = collect($request->validated());

        $groups = Group::find(
            $validated->get('groups')
        );

        $players = $round->players;

        $round->groups()->detach();
        $round->groups()->saveMany($groups);

        foreach ($groups as $group)
        {
            $group->addPlayers($players);
        }

        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($groups, $round->updated_at);

        return 'success';
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

    public function archiveTable(Group $group, Player $player = null)
    {
        $roundsQuery = Round::whereHas('groups', function (Builder $query) use ($group)
        {
            $query->where('groups.id', '=', $group->id);
        });

        if (isset($player))
        {
            $roundsQuery->whereHas('players', function (Builder $query) use ($player)
            {
                $query->where('players.id', '=', $player->id);
            });
        }

        $rounds = $roundsQuery
            ->with('players')
            ->withCount('games');

        return Datatables::of($rounds)
            ->addColumn('players', function ($round)
            {
                return '<a href="' . $round->path . '">' . nice_count($round->players->pluck('surname')->toArray()) . '</a>';
            })
            ->addColumn('date', function ($round) {
                return $round->updated_at->format('d.m.Y');
            })
            ->escapeColumns([''])
            ->orderColumn('date', 'updated_at $1')
            ->make(true);
    }

}
