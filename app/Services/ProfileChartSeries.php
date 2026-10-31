<?php

namespace App\Services;

use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Punkteverlauf und kumulierte Spielanzahl eines Profils für die Charts (Zeitreihen).
 */
class ProfileChartSeries
{
    private const MAX_POINTS = 700;

    /**
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

        if ($games->isEmpty()) {
            return ['dates' => [], 'points' => [], 'gameDates' => [], 'gameCounter' => []];
        }

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
                $gameCounter->push($i);
                $date->addDay();
            }

            $i++;
        }
        $gameDates->push($currentDate->isoFormat('D. MMM YYYY'));
        $gameCounter->push($i);

        return [
            'dates' => $this->thinOut($dates),
            'points' => $this->thinOut($points),
            'gameDates' => $this->thinOut($gameDates),
            'gameCounter' => $this->thinOut($gameCounter),
        ];
    }

    /**
     * Begrenzt die Anzahl der Punkte, der neueste bleibt immer erhalten.
     *
     * @template TValue
     *
     * @param  Collection<int, TValue>  $series
     * @return list<TValue>
     */
    private function thinOut(Collection $series): array
    {
        $step = (int) ceil($series->count() / self::MAX_POINTS);
        $thinnedOut = $series->nth($step);

        if (($series->count() - 1) % $step !== 0) {
            $thinnedOut->push($series->last());
        }

        return $thinnedOut->all();
    }
}
