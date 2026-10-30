<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\StoreRound;
use App\Http\Requests\UpdateRound;
use App\Http\Resources\CreateRoundPlayerResource;
use App\Http\Resources\GroupOptionResource;
use App\Http\Resources\Round as RoundResource;
use App\Player;
use App\Profile;
use App\Round;
use App\Services\RoundArchive;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class RoundController extends Controller
{
    public function index(Request $request, RoundArchive $roundArchive, ?Group $group = null): Response
    {
        $selectedGroup = $group ?? Group::findOrFail(Group::EWIGE_LISTE_ID);

        $groups = Group::query()->orderBy('name')->get();

        return Inertia::render('Rounds/Index', [
            'groupOptions' => array_values(GroupOptionResource::collection($groups)->resolve($request)),
            'selectedGroup' => (new GroupOptionResource($selectedGroup))->resolve($request),
            ...$roundArchive->forGroup($selectedGroup, $request),
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
        if ($lastRound) {
            return redirect($lastRound->path());
        } else {
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
            'players' => CreateRoundPlayerResource::collection($allPlayers)->resolve(request()),
        ]);
    }

    public function store(StoreRound $request)
    {
        $validated = collect($request->validated());

        $groups = Group::find(
            collect($validated->get('groups', []))->push(Group::EWIGE_LISTE_ID)->unique()
        );

        $round = Round::create();

        foreach ($validated->get('players') as $key => $playerID) {
            $round->players()->attach($playerID, [
                'index' => $key,
            ]);
        }

        $round->groups()->saveMany($groups);

        foreach ($groups as $group) {
            $group->addPlayers($round->players);
        }

        if ($validated->get('liveGame')) {
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

        foreach ($groups as $group) {
            $group->addPlayers($players);
        }

        Profile::updateManyStats($round->profiles());
        Group::updateManyStats($groups, $round->updated_at);

        return 'success';
    }

    public function destroy(Round $round)
    {
        $this->authorize('update', $round);

        if ($round->games->count() != 0) {
            return Redirect::back()->with('deleteError', 'Du kannst nur eine Runde ohne Spiele löschen!');
        }

        $round->delete();

        return redirect()->route('rounds.create');
    }

    public function changeDates(Round $round, Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $dateDiff = Carbon::create($request->input('date'))->diffInDays($round->created_at, false);

        if ($dateDiff > 0) {
            $case = 'subDays';
        } elseif ($dateDiff < 0) {
            $case = 'addDays';
            $dateDiff = abs($dateDiff) + 1;
        } else {
            return 'false';
        }

        $round->created_at = $round->created_at->$case($dateDiff);
        $round->save();

        foreach ($round->games as $game) {
            $game->created_at = $game->created_at->$case($dateDiff);
            $game->save();
        }

        return 'success';
    }
}
