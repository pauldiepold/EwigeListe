<?php

namespace App\Http\Controllers;

use App\Group;
use App\Round;
use App\Http\Resources\GroupHomeResource;
use App\Http\Resources\HomeRoundResource;
use App\Services\HomeGamesChartSeries;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(HomeGamesChartSeries $homeGamesChartSeries): Response
    {
        $group = Group::findOrFail(1);

        $currentRounds = auth()->check()
            ? Round::with(['players', 'games'])
                ->where('updated_at', '>=', Carbon::now()->subDay()->toDateTimeString())
                ->has('games')
                ->latest('updated_at')
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
