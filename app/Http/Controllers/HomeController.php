<?php

namespace App\Http\Controllers;

use App\Http\Resources\GroupStatisticsResource;
use App\Http\Resources\HomeRoundResource;
use App\Models\Group;
use App\Models\Round;
use App\Services\GamesChartSeries;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(GamesChartSeries $gamesChartSeries): Response
    {
        $group = Group::with('profiles.player')->findOrFail(Group::EWIGE_LISTE_ID);

        $currentRounds = auth()->check()
            ? Round::with(['players' => fn (BelongsToMany $players) => $players
                ->with('user')
                ->withSum(
                    ['games as round_points' => fn (Builder $games) => $games->whereColumn('games.round_id', 'player_round.round_id')],
                    'game_player.points',
                ),
            ])
                ->whereHas('games', fn (Builder $query) => $query->where('created_at', '>=', now()->subDay()))
                ->withCount('games')
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
