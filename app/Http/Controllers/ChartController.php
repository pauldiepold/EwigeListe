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
        $games = Game::select([
            DB::raw('Date(created_at) as date'),
            DB::raw('count(*) as counter')
        ])
            ->whereHas('round.groups', function (Builder $query) use ($group) {
                $query->where('groups.id', '=', $group->id);
            })
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $gameDates = $games->pluck('date');
        $counter = 0;
        $gameCounter = collect();
        foreach ($games as $game)
        {
            $gameCounter->push($counter += $game->counter);
        }

        $data = collect([
            'gameDates' => $gameDates,
            'gameCounter' => $gameCounter,
        ]);

        return ($data->toArray());
    }
}
