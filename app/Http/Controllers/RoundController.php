<?php

namespace App\Http\Controllers;

use App\Http\Resources\Round as RoundResource;
use App\Http\Requests\UpdateRound;
use App\Live\Deck;
use App\LiveRound;
use App\Profile;
use App\Round;
use App\Player;
use App\Group;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\StoreRound;
use Illuminate\Http\Request;
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
        $roundResource = new RoundResource($round->load(['games.players', 'games.createdBy', 'groups', 'players.groups', 'players.user']));

        return view('rounds.show', compact('round', 'roundResource'));
    }

    public function fetchData(Round $round)
    {
        return new RoundResource($round->load(['games.players', 'games.createdBy', 'groups', 'players.groups', 'players.user']));
    }

    public function current()
    {
        $lastRound = auth()->user()->player->rounds()->latest()->first();
        if ($lastRound)
        {
            return redirect($lastRound->path());
        } else
        {
            return redirect()->route('rounds.create');
        }
    }

    public function create()
    {
        $allPlayers = Player::where('hide', '=', '0')
            ->with(['groups', 'profiles:id,player_id,group_id,default'])
            ->withCount('gamePlayers')
            ->orderByRaw('game_players_count desc')
            ->get();

        return view('rounds.create', compact('allPlayers'));
    }

    public function store(StoreRound $request)
    {
        $validated = collect($request->validated());

        $groups = Group::find(
            $validated->get('groups')
        );

        $round = Round::create();

        foreach ($validated->get('players') as $key => $playerID)
        {
            $round->players()->attach($playerID, [
                'index' => $key
            ]);
        }

        $round->groups()->saveMany($groups);

        foreach ($groups as $group)
        {
            $group->addPlayers($round->players);
        }

        if ($validated->get('liveGame'))
        {
            $round->startLiveRound();
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

    public function changeDates(Round $round, Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $dateDiff = Carbon::create($request->input('date'))->diffInDays($round->created_at, false);

        if ($dateDiff > 0)
        {
            $case = 'subDays';
        } elseif ($dateDiff < 0)
        {
            $case = 'addDays';
            $dateDiff = abs($dateDiff) + 1;
        } else
        {
            return 'false';
        }

        $round->created_at = $round->created_at->$case($dateDiff);
        $round->save();

        foreach ($round->games as $game)
        {
            $game->created_at = $game->created_at->$case($dateDiff);
            $game->save();
        }

        return 'success';
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
            ->withCount(['games', 'groups']);

        return Datatables::of($rounds)
            ->addColumn('players', function ($round)
            {
                return '<a href="' . $round->path . '">' . $round->players_string . '</a>';
            })
            ->addColumn('date', function ($round)
            {
                return $round->updated_at->format('d.m.Y');
            })
            ->addColumn('playerIDs', function ($round)
            {
                return $round->players->pluck('id');
            })
            ->escapeColumns([''])
            ->orderColumn('date', 'updated_at $1')
            ->make(true);
    }

}
