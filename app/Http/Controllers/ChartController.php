<?php

namespace App\Http\Controllers;

use App\Group;
use App\Round;
use App\Player;
use App\Profile;
use App\Game;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{

    public function roundChart(Round $round)
    {
        $points = collect();
        $names = collect();

        //Kopfzeile
        foreach ($round->players as $player)
        {
            $points->put($player->id, collect(0));
            $names->push($player->surname);
        }

        //Spiele
        $games = $round->games()
            ->oldest()
            ->with('players')
            ->get();

        foreach ($games as $game)
        {
            foreach ($game->players as $player)
            {
                $currentPlayerPoints = $points->get($player->id);
                $currentPlayerPoints->push($currentPlayerPoints->last() + $player->pivot->points);
            }

            foreach ($round->players as $player)
            {
                if (!$game->players->contains($player))
                {
                    $currentPlayerPoints = $points->get($player->id);
                    if ($currentPlayerPoints->count() == 0)
                    {
                        $currentPlayerPoints->push(0);
                    } else
                    {
                        $currentPlayerPoints->push($currentPlayerPoints->last());
                    }
                }
            }
        }
        $points = $points->values();

        $data = collect();
        $data->put('names', $names);
        $data->put('points', $points);

        return $data->toArray();
    }

    public function profileChart(Profile $profile)
    {
        $games = $profile->player
            ->games()
            ->whereHas('round.groups', function (Builder $query) use ($profile) {
                $query->where('groups.id', '=', $profile->group_id);
            })
            ->oldest()
            ->get();

        $dates = collect();
        $points = collect();
        $gameDates = collect();
        $gameCounter = collect();
        $i = 0;
        foreach ($games as $game)
        {
            $currentDate = Carbon::parse($game->created_at);
            if ($i == 0)
            {
                $points->push($game->pivot->points);
            } else
            {
                $points->push($points->last() + $game->pivot->points);
            }
            $dates->push(($currentDate->isoFormat('D. MMM YYYY')));

            if ($i == 0)
            {
                $date = $currentDate->startOfDay();
                $gameDates->push(($currentDate->isoFormat('D. MMM YYYY')));
                $gameCounter->push($i);
            }

            while ($date->lessThan($currentDate->startOfDay()))
            {
                $gameDates->push(($date->isoFormat('D. MMM YYYY')));
                $gameCounter->push($i + 1);
                $date->addDay();
            }

            $i++;
        }
        $gameDates->push(($currentDate->isoFormat('D. MMM YYYY')));
        $gameCounter->push($i);

        $n = ceil($i / 700);

        $data = collect();
        $data->put('dates', $dates->nth($n));
        $data->put('points', $points->nth($n));
        $data->put('gameDates', $gameDates->nth($n));
        $data->put('gameCounter', $gameCounter->nth($n));


        return ($data->toArray());
    }

    public function homeChart(Group $group)
    {
        // Zeitraum ermitteln
        $firstGame = Game::whereHas('round.groups', function (Builder $query) use ($group) {
            $query->where('groups.id', '=', $group->id);
        })->oldest()->first();

        $lastGame = Game::whereHas('round.groups', function (Builder $query) use ($group) {
            $query->where('groups.id', '=', $group->id);
        })->latest()->first();

        if (!$firstGame || !$lastGame) {
            return [
                'gameDates' => [],
                'gameCounter' => []
            ];
        }

        $totalDays = $firstGame->created_at->diffInDays($lastGame->created_at);
        
        // Intelligente Bucket-Größe basierend auf Datenmenge
        if ($totalDays > 2000) {
            $bucketSize = 'month';
        } elseif ($totalDays > 500) {
            $bucketSize = 'week';
        } else {
            $bucketSize = 'day';
        }
        
        // MySQL-kompatible Abfrage für Spiele pro Zeitraum
        if ($bucketSize === 'month') {
            $games = Game::select([
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-01') as date_bucket"),
                DB::raw('count(*) as counter')
            ])
                ->whereHas('round.groups', function (Builder $query) use ($group) {
                    $query->where('groups.id', '=', $group->id);
                })
                ->groupBy('date_bucket')
                ->orderBy('date_bucket')
                ->get();
        } elseif ($bucketSize === 'week') {
            $games = Game::select([
                DB::raw("DATE(DATE_SUB(created_at, INTERVAL WEEKDAY(created_at) DAY)) as date_bucket"),
                DB::raw('count(*) as counter')
            ])
                ->whereHas('round.groups', function (Builder $query) use ($group) {
                    $query->where('groups.id', '=', $group->id);
                })
                ->groupBy('date_bucket')
                ->orderBy('date_bucket')
                ->get();
        } else {
            $games = Game::select([
                DB::raw('DATE(created_at) as date_bucket'),
                DB::raw('count(*) as counter')
            ])
                ->whereHas('round.groups', function (Builder $query) use ($group) {
                    $query->where('groups.id', '=', $group->id);
                })
                ->groupBy('date_bucket')
                ->orderBy('date_bucket')
                ->get();
        }

        // Vollständige Zeitreihe generieren
        if ($bucketSize === 'month') {
            $startDate = Carbon::parse($firstGame->created_at)->startOfMonth();
            $endDate = Carbon::parse($lastGame->created_at)->endOfMonth();
        } elseif ($bucketSize === 'week') {
            $startDate = Carbon::parse($firstGame->created_at)->startOfWeek();
            $endDate = Carbon::parse($lastGame->created_at)->endOfWeek();
        } else {
            $startDate = Carbon::parse($firstGame->created_at)->startOfDay();
            $endDate = Carbon::parse($lastGame->created_at)->endOfDay();
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

        // Spiele-Daten in Map für schnellen Zugriff
        $gamesMap = $games->keyBy('date_bucket');
        
        // Vollständige Zeitreihe mit kumulativen Werten
        $gameDates = collect();
        $gameCounter = collect();
        $cumulative = 0;
        
        foreach ($allDates as $date) {
            if ($bucketSize === 'month') {
                $dateKey = $date->format('Y-m-01');
            } else {
                $dateKey = $date->format('Y-m-d');
            }
            
            $gamesCount = $gamesMap->get($dateKey, (object)['counter' => 0])->counter;
            $cumulative += $gamesCount;
            
            $gameDates->push($date->isoFormat('D. MMM YYYY'));
            $gameCounter->push($cumulative);
        }

        $data = collect([
            'gameDates' => $gameDates,
            'gameCounter' => $gameCounter,
        ]);

        return ($data->toArray());
    }
}
