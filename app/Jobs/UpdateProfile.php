<?php

namespace App\Jobs;

use App\Profile;
use App\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProfile implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Profile $profile)
    {
        $games = DB::table('game_player')->where('player_id', $this->profile->player_id);

        if ($games->count() == 0)
        {
            return;
        }

        $this->profile->games = $games->count();
        $gamesThisMonth = clone $games;
        $this->profile->gamesThisMonth = $gamesThisMonth->whereYear('created_at', date('Y'))->whereMonth('created_at', date('n'))->count();
        $gamesWon = clone $games;
        $gamesLost = clone $games;
        $this->profile->gamesWon = $gamesWon->where('won', 1)->count();
        $this->profile->gamesLost = $gamesLost->where('won', '0')->count();

        $this->profile->points = $games->sum('points');
        $this->profile->pointsPerGame = $this->profile->games == 0 ? null : $this->profile->points / $this->profile->games;
        $this->profile->pointsPerWin = $this->profile->gamesWon == 0 ? null : $gamesWon->sum('points') / $this->profile->gamesWon;
        $this->profile->pointsPerLose = $this->profile->gamesLost == 0 ? null : $gamesLost->sum('points') / $this->profile->gamesLost;
        $this->profile->winrate = $this->profile->games == 0 ? null : $this->profile->gamesWon / $this->profile->games * 100;

        /* Soli ************************************* */
        $soli = clone $games;
        $this->profile->soli = $soli->where('soloist', 1)->count();

        $soliWon = clone $soli;
        $soliLost = clone $soli;
        $this->profile->soliWon = $soliWon->where('won', 1)->count();
        $this->profile->soliLost = $soliLost->where('won', '0')->count();
        $this->profile->soloRate = $this->profile->soli == 0 ? null : round($this->profile->games / $this->profile->soli);
        $this->profile->soloWinrate = $this->profile->soli == 0 ? null : $this->profile->soliWon / $this->profile->soli * 100;
        $this->profile->soloPoints = $soli->sum('points');


        /* Meiste Spiele Tag ************************* */
        $groupedByDay = $this->profile->player->games()->latest()->get()->groupBy(function ($item)
        {
            return $item->created_at->format('d-m-Y');
        });
        $groupedByDayCounted = $groupedByDay->map(function ($item, $key)
        {
            return collect($item)->count();
        })->sort();

        $this->profile->mostGamesDay = $groupedByDayCounted->last();
        $this->profile->mostGamesDayDate = date("Y-m-d H:i:s", strtotime($groupedByDayCounted->keys()->last()));

        /* Meiste Spiele Monat ************************* */
        $groupedByMonth = $this->profile->player->games()->latest()->get()->groupBy(function ($item)
        {
            return $item->created_at->format('m-Y');
        });
        $groupedByMonthCounted = $groupedByMonth->map(function ($item, $key)
        {
            return collect($item)->count();
        })->sort();

        $this->profile->mostGamesMonth = $groupedByMonthCounted->last();
        $this->profile->mostGamesMonthDate = date("Y-m-d H:i:s", strtotime("01-" . $groupedByMonthCounted->keys()->last()));


        $points = 0;
        $this->profile->lowestPoints = 0;
        $this->profile->highestPoints = 0;

        $this->profile->winStreak = 0;
        $this->profile->loseStreak = 0;
        $winStreak = 0;
        $winStreakStart = null;
        $loseStreak = 0;
        $loseStreakStart = null;
        foreach ($games->get() as $game)
        {
            $points += $game->points;
            if ($points > $this->profile->highestPoints)
            {
                $this->profile->highestPoints = $points;
                $this->profile->highestPointsDate = $game->created_at;
            }
            if ($points < $this->profile->lowestPoints)
            {
                $this->profile->lowestPoints = $points;
                $this->profile->lowestPointsDate = $game->created_at;
            }

            if ($game->won)
            {
                $winStreak++;
                if ($loseStreak != 0)
                {
                    $loseStreak = 0;
                    $winStreakStart = $game->created_at;
                }
                if ($winStreak > $this->profile->winStreak)
                {
                    $this->profile->winStreak = $winStreak;
                    $this->profile->winStreakStart = $winStreakStart;
                    $this->profile->winStreakEnd = $game->created_at;
                }
            } else
            {
                $loseStreak++;
                if ($winStreak != 0)
                {
                    $winStreak = 0;
                    $loseStreakStart = $game->created_at;
                }
                if ($loseStreak > $this->profile->loseStreak)
                {
                    $this->profile->loseStreak = $loseStreak;
                    $this->profile->loseStreakStart = $loseStreakStart;
                    $this->profile->loseStreakEnd = $game->created_at;
                }

            }
        }

        $this->profile->queued = false;
        $this->profile->save();


    }
}
