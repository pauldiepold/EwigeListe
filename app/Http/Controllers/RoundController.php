<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Requests\StoreRound;
use App\Http\Requests\UpdateRound;
use App\Http\Resources\CreateRoundPlayerResource;
use App\Http\Resources\GroupOptionResource;
use App\Http\Resources\Round as RoundResource;
use App\Http\Resources\RoundShowResource;
use App\Http\Toast;
use App\Player;
use App\Profile;
use App\Round;
use App\Services\RoundArchive;
use App\Services\RoundChartSeries;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function show(Request $request, RoundChartSeries $roundChartSeries, Round $round): Response
    {
        $round->load(['games.players', 'games.createdBy', 'groups', 'players.groups', 'players.user', 'createdBy']);

        return Inertia::render('Rounds/Show', [
            'round' => (new RoundShowResource($round))->resolve($request),
            ...($round->games->count() >= RoundChartSeries::MINIMUM_GAMES
                ? ['roundChart' => Inertia::optional(fn (): array => $roundChartSeries->forRound($round))]
                : []),
        ]);
    }

    public function fetchData(Round $round)
    {
        return new RoundResource($round->load(['games.players', 'games.createdBy', 'groups', 'players.groups', 'players.user']));
    }

    public function current()
    {
        $lastRound = auth()->user()->player->rounds()->latest()->first();

        return $lastRound
            ? redirect()->route('rounds.show', $lastRound)
            : redirect()->route('rounds.create');
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

        return redirect()->route('rounds.show', $round);
    }

    public function update(UpdateRound $request, Round $round): RedirectResponse
    {
        $previousGroups = $round->groups;
        $groups = Group::findMany($request->groupIds());

        $round->groups()->sync($groups);

        foreach ($groups as $group) {
            $group->addPlayers($round->players);
        }

        $affectedGroups = $previousGroups->merge($groups);
        Profile::updateManyStats($round->profilesIn($affectedGroups));
        Group::updateManyStats($affectedGroups, $round->updated_at);

        return redirect()->route('rounds.show', $round);
    }

    public function destroy(Round $round): RedirectResponse
    {
        $this->authorize('update', $round);

        if ($round->games->isNotEmpty()) {
            Toast::error('Du kannst nur eine Runde ohne Spiele löschen!');

            return back();
        }

        $round->delete();

        Toast::success('Die Runde wurde gelöscht.');

        return redirect()->route('rounds.create');
    }
}
