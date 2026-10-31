<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Resources\BadgesResource;
use App\Http\Resources\GroupIndexResourceCollection;
use App\Http\Resources\GroupMemberResource;
use App\Http\Resources\GroupShowResource;
use App\Http\Resources\GroupStatisticsResource;
use App\Http\Toast;
use App\Services\GamesChartSeries;
use App\Services\RoundArchive;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    public function index(): Response
    {
        $groups = Group::withCount('rounds')
            ->withCount('players')
            ->orderByRaw('rounds_count desc')
            ->get();

        return Inertia::render('Groups/Index', [
            'groups' => new GroupIndexResourceCollection($groups),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Groups/Create');
    }

    public function store(Request $request)
    {
        $attributes = request()->validate([
            'name' => 'required',
        ]);

        $group = auth()->user()->player->createdGroups()->create($attributes);

        $group->players()->save(auth()->user()->player);

        $group->calculate();

        return redirect()->route('groups.show', $group);
    }

    public function show(
        Request $request,
        RoundArchive $roundArchive,
        GamesChartSeries $gamesChartSeries,
        ?Group $group = null,
    ): Response {
        $group ??= Group::findOrFail(Group::EWIGE_LISTE_ID);
        $group->load(['profiles.player.user', 'badges.player']);

        $player = $request->user()->player;
        $archive = $roundArchive->forGroup($group, $request);
        $hasGamesChart = $archive['rounds']->total() >= 5;

        return Inertia::render('Groups/Show', [
            'group' => new GroupShowResource($group),
            'members' => GroupMemberResource::collection($group->profiles->sortByDesc('games')->values())->resolve($request),
            'statistics' => new GroupStatisticsResource($group),
            'badges' => new BadgesResource($group->badges),
            ...$archive,
            'hasGamesChart' => $hasGamesChart,
            ...($hasGamesChart ? ['gamesChart' => Inertia::optional(fn (): array => $gamesChartSeries->forGroup($group))] : []),
            'canJoin' => $group->canBeJoinedBy($player),
            'canLeave' => $group->canBeLeftBy($player),
            'canClose' => $group->canBeClosedBy($request->user()),
        ]);
    }

    public function calculateAll()
    {
        Group::all()->each(function ($group, $key) {
            $group->calculate();
        });

        return redirect('/listen');
    }

    public function calculate(Group $group)
    {
        $group->calculate();

        return redirect($group->path().'#statistiken');
    }

    public function calculateBadges(Group $group)
    {
        $group->calculateBadges();

        return redirect($group->path().'#abzeichen');
    }

    public function join(Request $request, Group $group): RedirectResponse
    {
        $player = $request->user()->player;

        if (! $group->canBeJoinedBy($player)) {
            Toast::error('Du kannst dieser Liste nicht beitreten.');

            return redirect()->route('groups.show', $group);
        }

        $group->addPlayer($player);
        Toast::success('Du bist der Liste beigetreten.');

        return redirect()->route('groups.show', $group);
    }

    public function leave(Request $request, Group $group): RedirectResponse
    {
        $player = $request->user()->player;

        if (! $group->canBeLeftBy($player)) {
            Toast::error('Du kannst diese Liste nicht verlassen.');

            return redirect()->route('groups.show', $group);
        }

        $group->players()->detach($player->id);
        Toast::success('Du hast die Liste verlassen.');

        return redirect()->route('groups.show', $group);
    }

    public function close(Request $request, Group $group): RedirectResponse
    {
        $attributes = $request->validate([
            'closed' => ['required', 'boolean'],
        ]);

        if (! $group->canBeClosedBy($request->user())) {
            Toast::error('Du kannst diese Liste nicht schließen oder öffnen.');

            return redirect()->route('groups.show', $group);
        }

        $group->closed = $attributes['closed'];
        $group->save();
        Toast::success($group->closed ? 'Die Liste wurde geschlossen.' : 'Die Liste wurde geöffnet.');

        return redirect()->route('groups.show', $group);
    }
}
