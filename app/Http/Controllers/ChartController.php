<?php

namespace App\Http\Controllers;

use App\Round;
use App\Player;
use App\Profile;
use App\Game;
use Carbon\Carbon;
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
        foreach ($round->games()->oldest()->with('players')->get() as $game)
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
        $games = DB::table('game_player as g')
            ->join('games', 'g.game_id', '=', 'games.id')
            ->join('rounds', 'games.round_id', '=', 'rounds.id')
            ->join('group_round', 'rounds.id', '=', 'group_round.round_id')
            ->where('group_round.group_id', $profile->group_id)
            ->where('g.player_id', $profile->player_id)
            ->select('games.created_at as created_at', 'g.points as points')
            ->orderBy('games.created_at', 'asc')
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
                $points->push($game->points);

            } else
            {
                $points->push($points->last() + $game->points);
            }
            $dates->push(($currentDate->formatLocalized('%e. %h %Y')));

            if ($i == 0)
            {
                $date = $currentDate->startOfDay();
                $gameDates->push(($currentDate->formatLocalized('%e. %h %Y')));
                $gameCounter->push($i);
            }

            while ($date->lessThan($currentDate->startOfDay()))
            {
                $gameDates->push(($date->formatLocalized('%e. %h %Y')));
                $gameCounter->push($i + 1);
                $date->addDay();
            }

            $i++;
        }
        $gameDates->push(($currentDate->formatLocalized('%e. %h %Y')));
        $gameCounter->push($i);

        $data = collect();
        $data->put('dates', $dates);
        $data->put('points', $points);
        $data->put('gameDates', $gameDates);
        $data->put('gameCounter', $gameCounter);

        return ($data->toArray());
    }

    public function homeChart()
    {
        $games = Game::all();

        $gameDates = collect();
        $gameCounter = collect();
        $i = 0;
        foreach ($games as $game)
        {
            $currentDate = Carbon::parse($game->created_at);

            if ($i == 0)
            {
                $date = $currentDate->startOfDay();
                $gameDates->push(($currentDate->formatLocalized('%e. %h %Y')));
                $gameCounter->push($i);
            }

            while ($date->lessThan($currentDate->startOfDay()))
            {
                $gameDates->push(($date->formatLocalized('%e. %h %Y')));
                $gameCounter->push($i + 1);
                $date->addDay();
            }

            $i++;
        }
        $gameDates->push(($currentDate->formatLocalized('%e. %h %Y')));
        $gameCounter->push($i);

        $data = collect();
        $data->put('gameDates', $gameDates);
        $data->put('gameCounter', $gameCounter);

        return ($data->toArray());
    }
}
