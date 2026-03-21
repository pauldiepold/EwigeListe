<?php

namespace App\Services;

use App\Game;
use App\Group;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Kumulierte Spielanzahl pro Gruppe für das Startseiten-Chart (Zeitreihe).
 *
 * @return array{gameDates: list<string>, gameCounter: list<int>}
 */
class HomeGamesChartSeries
{
    public function forGroup(Group $group): array
    {
        $firstGame = Game::whereHas('round.groups', function (Builder $query) use ($group) {
            $query->where('groups.id', '=', $group->id);
        })->oldest()->first();

        $lastGame = Game::whereHas('round.groups', function (Builder $query) use ($group) {
            $query->where('groups.id', '=', $group->id);
        })->latest()->first();

        if (!$firstGame || !$lastGame) {
            return [
                'gameDates' => [],
                'gameCounter' => [],
            ];
        }

        $totalDays = $firstGame->created_at->diffInDays($lastGame->created_at);

        if ($totalDays > 2000) {
            $bucketSize = 'month';
        } elseif ($totalDays > 500) {
            $bucketSize = 'week';
        } else {
            $bucketSize = 'day';
        }

        $dateFormat = match ($bucketSize) {
            'month' => "DATE_FORMAT(created_at, '%Y-%m-01')",
            'week' => 'DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY))',
            'day' => 'DATE(created_at)',
        };

        $games = Game::select([
            DB::raw("$dateFormat as date_bucket"),
            DB::raw('count(*) as counter'),
        ])
            ->whereHas('round.groups', function (Builder $query) use ($group) {
                $query->where('groups.id', '=', $group->id);
            })
            ->groupBy('date_bucket')
            ->orderBy('date_bucket')
            ->get();

        $firstDate = Carbon::parse($firstGame->created_at);
        $lastDate = Carbon::parse($lastGame->created_at);

        if ($bucketSize === 'month') {
            $startDate = $firstDate->copy()->startOfMonth();
            $endDate = $lastDate->copy()->endOfMonth();
        } elseif ($bucketSize === 'week') {
            $startDate = $firstDate->copy()->startOfWeek();
            $endDate = $lastDate->copy()->endOfWeek();
        } else {
            $startDate = $firstDate->copy()->startOfDay();
            $endDate = $lastDate->copy()->endOfDay();
        }

        $allDates = collect();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $allDates->push($currentDate->copy());
            if ($bucketSize === 'month') {
                $currentDate->addMonth();
            } elseif ($bucketSize === 'week') {
                $currentDate->addWeek();
            } else {
                $currentDate->addDay();
            }
        }

        $gamesMap = $games->keyBy('date_bucket');

        $gameDates = collect();
        $gameCounter = collect();
        $cumulative = 0;

        foreach ($allDates as $date) {
            if ($bucketSize === 'month') {
                $dateKey = $date->format('Y-m-01');
            } else {
                $dateKey = $date->format('Y-m-d');
            }

            $gamesCount = $gamesMap->get($dateKey, (object) ['counter' => 0])->counter;
            $cumulative += $gamesCount;

            $gameDates->push($date->isoFormat('D. MMM YYYY'));
            $gameCounter->push($cumulative);
        }

        return [
            'gameDates' => $gameDates->all(),
            'gameCounter' => $gameCounter->all(),
        ];
    }
}
