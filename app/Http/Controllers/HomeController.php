<?php

namespace App\Http\Controllers;

use App\Group;
use App\Http\Resources\GroupStatisticsResource;
use App\Http\Resources\HomeRoundResource;
use App\Round;
use App\Services\GamesChartSeries;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(GamesChartSeries $gamesChartSeries): Response
    {
        $group = Group::with('profiles.player')->findOrFail(Group::EWIGE_LISTE_ID);

        $currentRounds = auth()->check()
            ? Round::with('players')
                ->whereHas('games', fn (Builder $query) => $query->where('created_at', '>=', now()->subDay()))
                ->withMax('games', 'created_at')
                ->orderByDesc('games_max_created_at')
                ->get()
            : collect();

        return Inertia::render('Home/Index', [
            'group' => new GroupStatisticsResource($group),
            'currentRounds' => HomeRoundResource::collection($currentRounds)->toArray(request()),
            'homeGamesChart' => Inertia::defer(
                fn (): array => $gamesChartSeries->forGroup($group),
            ),
        ]);
    }
}
