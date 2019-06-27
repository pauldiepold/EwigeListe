<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Game;
use Carbon\Carbon;

class Profile extends Model {

    protected $dates = [
        'mostGamesMonthDate',
    ];

    protected $fillable = ['player_id'];

    public function updateProfile()
    {
        $games = DB::table('game_player')->where('player_id', $this->player_id);

        if ($games->count() == 0)
        {
            return;
        }

        $this->games = $games->count();
        $gamesThisMonth = clone $games;
        $this->gamesThisMonth = $gamesThisMonth->whereYear('created_at', date('Y'))->whereMonth('created_at', date('n'))->count();
        $gamesWon = clone $games;
        $gamesLost = clone $games;
        $this->gamesWon = $gamesWon->where('won', 1)->count();
        $this->gamesLost = $gamesLost->where('won', '0')->count();
		$this->gamesPerDay = $this->player->created_at->diffInDays(Carbon::now()) < 5 ? null : $this->games / $this->player->created_at->diffInDays(Carbon::now());

        $this->points = $games->sum('points');
        $this->pointsPerGame = $this->games == 0 ? null : $this->points / $this->games;
        $this->pointsPerWin = $this->gamesWon == 0 ? null : $gamesWon->sum('points') / $this->gamesWon;
        $this->pointsPerLose = $this->gamesLost == 0 ? null : $gamesLost->sum('points') / $this->gamesLost;
        $this->winrate = $this->games == 0 ? null : $this->gamesWon / $this->games * 100;

        /* Soli ************************************* */
        $soli = clone $games;
        $this->soli = $soli->where('soloist', 1)->count();

        $soliWon = clone $soli;
        $soliLost = clone $soli;
        $this->soliWon = $soliWon->where('won', 1)->count();
        $this->soliLost = $soliLost->where('won', '0')->count();
        $this->soloRate = $this->soli == 0 ? null : round($this->games / $this->soli);
        $this->soloWinrate = $this->soli == 0 ? null : $this->soliWon / $this->soli * 100;
        $this->soloPoints = $soli->sum('points');


        /* Meiste Spiele Tag ************************* */
        $groupedByDay = $this->player->games()->latest()->get()->groupBy(function ($item)
        {
            return $item->created_at->format('d-m-Y');
        });
        $groupedByDayCounted = $groupedByDay->map(function ($item, $key)
        {
            return collect($item)->count();
        })->sort();

        $this->mostGamesDay = $groupedByDayCounted->last();
        $this->mostGamesDayDate = date("Y-m-d H:i:s", strtotime($groupedByDayCounted->keys()->last()));

        /* Meiste Spiele Monat ************************* */
        $groupedByMonth = $this->player->games()->latest()->get()->groupBy(function ($item)
        {
            return $item->created_at->format('m-Y');
        });
        $groupedByMonthCounted = $groupedByMonth->map(function ($item, $key)
        {
            return collect($item)->count();
        })->sort();

        $this->mostGamesMonth = $groupedByMonthCounted->last();
        $this->mostGamesMonthDate = date("Y-m-d H:i:s", strtotime("01-" . $groupedByMonthCounted->keys()->last()));


        $points = 0;
        $this->lowestPoints = 0;
        $this->highestPoints = 0;

        $this->winStreak = 0;
        $this->loseStreak = 0;
        $winStreak = 0;
        $winStreakStart = null;
        $loseStreak = 0;
        $loseStreakStart = null;
        foreach ($games->get() as $game)
        {
            $points += $game->points;
            if ($points > $this->highestPoints)
            {
                $this->highestPoints = $points;
                $this->highestPointsDate = $game->created_at;
            }
            if ($points < $this->lowestPoints)
            {
                $this->lowestPoints = $points;
                $this->lowestPointsDate = $game->created_at;
            }

            if ($game->won)
            {
                $winStreak++;
                if ($loseStreak != 0)
                {
                    $loseStreak = 0;
                    $winStreakStart = $game->created_at;
                }
                if ($winStreak > $this->winStreak)
                {
                    $this->winStreak = $winStreak;
                    $this->winStreakStart = $winStreakStart;
                    $this->winStreakEnd = $game->created_at;
                }
            } else
            {
                $loseStreak++;
                if ($winStreak != 0)
                {
                    $winStreak = 0;
                    $loseStreakStart = $game->created_at;
                }
                if ($loseStreak > $this->loseStreak)
                {
                    $this->loseStreak = $loseStreak;
                    $this->loseStreakStart = $loseStreakStart;
                    $this->loseStreakEnd = $game->created_at;
                }
            }
        }

        $this->save();
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

}
