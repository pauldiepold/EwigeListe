<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Resources\BadgesResource;
use App\Http\Resources\GroupOptionResource;
use App\Http\Resources\PlayerShowResource;
use App\Http\Resources\ProfileStatisticsResource;
use App\Player;
use App\Services\ProfileChartSeries;
use App\Services\RoundArchive;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PlayerController extends Controller
{
    public function show(
        Request $request,
        RoundArchive $roundArchive,
        ProfileChartSeries $profileChartSeries,
        Player $player,
        ?Group $group = null,
    ): Response {
        $this->authorize('view', $player);

        $group ??= Group::findOrFail(Group::EWIGE_LISTE_ID);
        $profile = $player->profiles()->where('group_id', $group->id)->firstOrFail();
        $badges = $player->badges()->where('group_id', $group->id)->with('player')->get();
        $hasStatistics = $profile->games >= ProfileStatisticsResource::MINIMUM_GAMES;

        return Inertia::render('Players/Show', [
            'player' => new PlayerShowResource($player),
            'groupOptions' => GroupOptionResource::collection($player->groups)->resolve($request),
            'selectedGroupId' => $group->id,
            'profile' => new ProfileStatisticsResource($profile),
            'badges' => (new BadgesResource($badges))->withoutLinks(),
            'badgeCount' => $badges->count(),
            ...$roundArchive->forGroup($group, $request, $player),
            ...($hasStatistics ? ['profileChart' => Inertia::optional(fn (): array => $profileChartSeries->forProfile($profile))] : []),
        ]);
    }
}
