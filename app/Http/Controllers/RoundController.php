<?php

namespace App\Http\Controllers;

use App\Http\Resources\CreateRoundPlayerResource;
use App\Http\Resources\GroupOptionResource;
use App\Http\Resources\RoundArchiveRowResource;
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
use Inertia\Inertia;
use Inertia\Response;
use Yajra\DataTables\Facades\DataTables;

class RoundController extends Controller
{

    public function index(Group $group = null): Response
    {
        $selectedGroup = $group ?? Group::findOrFail(1);

        $groups = Group::query()->orderBy('name')->get();

        $request = request();

        $sort = $request->query('sort');
        $directionInput = strtolower((string) $request->query('direction', 'desc'));
        $allowedSorts = ['date', 'games', 'online'];
        $sort = in_array($sort, $allowedSorts, true) ? $sort : 'date';
        $direction = $directionInput === 'asc' ? 'asc' : 'desc';

        $roundsQuery = Round::whereHas('groups', function (Builder $query) use ($selectedGroup) {
            $query->where('groups.id', '=', $selectedGroup->id);
        })
            ->with(['players', 'liveRound'])
            ->withCount(['games', 'groups']);

        match ($sort) {
            'games' => $roundsQuery
                ->orderBy('games_count', $direction)
                ->orderByDesc('rounds.id'),
            'online' => $roundsQuery
                ->orderByRaw('(live_round_id IS NOT NULL) '.strtoupper($direction))
                ->orderByDesc('rounds.id'),
            default => $roundsQuery
                ->orderBy('updated_at', $direction)
                ->orderByDesc('rounds.id'),
        };

        $paginator = $roundsQuery->paginate(20)->withQueryString();
        $paginator->setCollection(
            $paginator->getCollection()->map(
                static fn (Round $round) => (new RoundArchiveRowResource($round))->resolve($request)
            )
        );

        return Inertia::render('Rounds/Index', [
            'groupOptions'     => array_values(GroupOptionResource::collection($groups)->resolve($request)),
            'selectedGroup'    => (new GroupOptionResource($selectedGroup))->resolve($request),
            'rounds'           => $paginator,
            'loggedInPlayerId' => auth()->user()->player->id,
            'archiveSort'      => [
                'column'    => $sort,
                'direction' => $direction,
            ],
        ]);
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

    public function create(): Response
    {
        $allPlayers = Player::where('hide', '=', '0')
            ->with(['groups', 'profiles:id,player_id,group_id,default', 'user'])
            ->withCount('gamePlayers')
            ->orderByRaw('game_players_count desc')
            ->get();

        return Inertia::render('Rounds/Create', [
            'players'          => CreateRoundPlayerResource::collection($allPlayers)->resolve(request()),
            'loggedInPlayerId' => auth()->user()->player->id,
        ]);
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
            $round->createLiveRound();
        }

        return redirect()->to($round->path());
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
        $roundsQuery = Round::whereHas('groups', function (Builder $query) use ($group) {
            $query->where('groups.id', '=', $group->id);
        });

        if (isset($player))
        {
            $roundsQuery->whereHas('players', function (Builder $query) use ($player) {
                $query->where('players.id', '=', $player->id);
            });
        }

        $rounds = $roundsQuery
            ->with('players', 'liveRound')
            ->withCount(['games', 'groups']);

        return Datatables::of($rounds)
            ->addColumn('players', function ($round) {
                return '<a href="' . $round->path . '">' . $round->players_string . '</a>';
            })
            ->addColumn('date', function ($round) {
                return $round->updated_at->format('d.m.Y');
            })
            ->addColumn('playerIDs', function ($round) {
                return $round->players->pluck('id');
            })
            ->addColumn('liveRound', function ($round) {
                return $round->liveRound ? '<i class="fas fa-dice tw-text-gray-800"></i>' : '';
            })
            ->escapeColumns([''])
            ->orderColumn('date', 'updated_at $1')
            ->make(true);
    }

}
