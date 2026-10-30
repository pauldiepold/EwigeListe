<?php

namespace App\Http\Controllers;

use App\Group;
use App\Round;
use App\Http\Resources\GroupHomeResource;
use App\Http\Resources\HomeRoundResource;
use App\Services\HomeGamesChartSeries;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(HomeGamesChartSeries $homeGamesChartSeries): Response
    {
        $group = Group::findOrFail(Group::EWIGE_LISTE_ID);

        $currentRounds = auth()->check()
            ? Round::with('players')
                ->whereHas('games', fn (Builder $query) => $query->where('created_at', '>=', now()->subDay()))
                ->withMax('games', 'created_at')
                ->orderByDesc('games_max_created_at')
                ->get()
            : collect();

        return Inertia::render('Home/Index', [
            'group' => new GroupHomeResource($group),
            'currentRounds' => HomeRoundResource::collection($currentRounds)->toArray(request()),
            'homeGamesChart' => Inertia::defer(
                fn (): array => $homeGamesChartSeries->forGroup($group),
            ),
        ]);
    }
}
