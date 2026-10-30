<?php

namespace App\Services;

use App\Profile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Punkteverlauf und kumulierte Spielanzahl eines Profils für die Charts (Zeitreihen).
 */
class ProfileChartSeries
{
    private const MAX_POINTS = 700;

    /**
     * Setzt voraus, dass das Profil mindestens ein Spiel hat.
     *
     * @return array{dates: list<string>, points: list<int>, gameDates: list<string>, gameCounter: list<int>}
     */
    public function forProfile(Profile $profile): array
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
        foreach ($games as $game) {
            $currentDate = Carbon::parse($game->created_at);
            if ($i == 0) {
                $points->push($game->pivot->points);
            } else {
                $points->push($points->last() + $game->pivot->points);
            }
            $dates->push($currentDate->isoFormat('D. MMM YYYY'));

            if ($i == 0) {
                $date = $currentDate->startOfDay();
                $gameDates->push($currentDate->isoFormat('D. MMM YYYY'));
                $gameCounter->push($i);
            }

            while ($date->lessThan($currentDate->startOfDay())) {
                $gameDates->push($date->isoFormat('D. MMM YYYY'));
                $gameCounter->push($i + 1);
                $date->addDay();
            }

            $i++;
        }
        $gameDates->push($currentDate->isoFormat('D. MMM YYYY'));
        $gameCounter->push($i);

        $step = (int) ceil($i / self::MAX_POINTS);

        return [
            'dates' => $dates->nth($step)->all(),
            'points' => $points->nth($step)->all(),
            'gameDates' => $gameDates->nth($step)->all(),
            'gameCounter' => $gameCounter->nth($step)->all(),
        ];
    }
}
