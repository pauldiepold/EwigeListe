<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class Profile extends Pivot
{

    protected $table = 'profiles';
    public $incrementing = true;

    protected $dates = [
        'mostGamesDayDate',
        'mostGamesMonthDate',
        'highestPointsDate',
        'lowestPointsDate',
        'winStreakStart',
        'winStreakEnd',
        'loseStreakStart',
        'loseStreakEnd',
    ];

    protected $casts = [
        'pointsPerGame' => 'decimal:2',
        'pointsPerWin' => 'decimal:2',
        'pointsPerLose' => 'decimal:2',
        'gamesPerDay' => 'decimal:2',
        'winrate' => 'decimal:1',
        'soloWinrate' => 'decimal:1'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function games()
    {

    }

    public function calculate()
    {
        $games = DB::table('game_player as g')
            ->join('games', 'g.game_id', '=', 'games.id')
            ->join('rounds', 'games.round_id', '=', 'rounds.id')
            ->join('group_round', 'rounds.id', '=', 'group_round.round_id')
            ->where('group_round.group_id', $this->group_id)
            ->where('g.player_id', $this->player_id)
            ->select('g.id as id',
                'g.game_id as game_id',
                'g.player_id as player_id',
                'g.points as points',
                'g.soloist as soloist',
                'g.won as won',
                'g.misplayed as misplayed',
                'g.created_at as created_at',
                'g.updated_at as updated_at'
            );

        if ($games->count() == 0)
        {
            return;
        }
        $this->games = $games->count();
        $gamesThisMonth = clone $games;
        $this->gamesThisMonth = $gamesThisMonth->whereYear('g.created_at', date('Y'))->whereMonth('g.created_at', date('n'))->count();
        $gamesWon = clone $games;
        $gamesLost = clone $games;
        $this->gamesWon = $gamesWon->where('g.won', 1)->count();
        $this->gamesLost = $gamesLost->where('g.won', '0')->count();
        $this->gamesPerDay = $this->player->created_at->diffInDays(Carbon::now()) < 5 ? null : $this->games / $this->player->created_at->diffInDays(Carbon::now());
        $this->points = $games->sum('g.points');
        $this->pointsPerGame = $this->games == 0 ? null : $this->points / $this->games;
        $this->pointsPerWin = $this->gamesWon == 0 ? null : $gamesWon->sum('g.points') / $this->gamesWon;
        $this->pointsPerLose = $this->gamesLost == 0 ? null : $gamesLost->sum('g.points') / $this->gamesLost;
        $this->winrate = $this->games == 0 ? null : $this->gamesWon / $this->games * 100;

        /* Soli ************************************* */
        $soli = clone $games;
        $this->soli = $soli->where('g.soloist', 1)->count();
        $soliWon = clone $soli;
        $soliLost = clone $soli;
        $this->soliWon = $soliWon->where('g.won', 1)->count();
        $this->soliLost = $soliLost->where('g.won', '0')->count();
        $this->soloRate = $this->soli == 0 ? null : round($this->games / $this->soli);
        $this->soloWinrate = $this->soli == 0 ? null : $this->soliWon / $this->soli * 100;
        $this->soloPoints = $soli->sum('g.points');

        /* Meiste Spiele Tag ************************* */
        $groupedByDay = $this->player->games()->latest()->get()->groupBy(function ($item)
        {
            return $item->created_at->format('Y-m-d');
        });
        $groupedByDayCounted = $groupedByDay->map(function ($item, $key)
        {
            return collect($item)->count();
        })->sort();
        $this->mostGamesDay = $groupedByDayCounted->last();
        $this->mostGamesDayDate = $groupedByDayCounted->keys()->last();

        /* Meiste Spiele Monat ************************* */
        $groupedByMonth = $this->player->games()->latest()->get()->groupBy(function ($item)
        {
            return $item->created_at->format('Y-m');
        });
        $groupedByMonthCounted = $groupedByMonth->map(function ($item, $key)
        {
            return collect($item)->count();
        })->sort();
        $this->mostGamesMonth = $groupedByMonthCounted->last();
        $this->mostGamesMonthDate = $groupedByMonthCounted->keys()->last() . "-01";


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
}
