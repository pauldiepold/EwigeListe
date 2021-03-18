<?php

namespace App;

use App\Jobs\UpdateProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Profile extends Pivot
{
    use HasFactory;

    protected $table = 'profiles';
    public $incrementing = true;

    public function path()
    {
        return "/profil/{$this->player_id}/{$this->group_id}";
    }

    public function updateStats()
    {
        if (!$this->queued)
        {
            $this->queued = true;
            $this->save();
            UpdateProfile::dispatch($this);
        }
    }

    public static function updateManyStats(Collection $profiles)
    {
        foreach ($profiles as $profile)
        {
            if (!$profile->queued)
            {
                $profile->queued = true;
                $profile->save();
                UpdateProfile::dispatch($profile);
            }
        }
    }

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
        'pointsPerSolo' => 'decimal:2',
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

    public function badges()
    {
        return $this->player->badges()->where('group_id', $this->group_id)->get();
    }

    public function calculate()
    {
        $groupID = $this->group_id;
        $playerID = $this->player_id;

        $games = GamePlayer::whereHas('game.round.groups', function (Builder $query) use ($groupID)
        {
            $query->where('groups.id', '=', $groupID);
        })
            ->where('player_id', $playerID)
            ->orderBy('id', 'asc');


        if ($games->count() == 0)
        {
            $this->games = 0;
            $this->points = null;
            $this->pointsPerGame = null;
            $this->soli = null;
            $this->save();

            return;
        }
        $this->games = $games->count();
        $gamesThisMonth = clone $games;
        $this->gamesThisMonth = $gamesThisMonth->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('created_at', '<', Carbon::now()->startOfMonth()->addMonth())
            ->count();
        $gamesWon = clone $games;
        $gamesLost = clone $games;
        $this->gamesWon = $gamesWon->where('won', 1)->count();
        $this->gamesLost = $gamesLost->where('won', '0')->count();
        $this->gamesPerDay = $this->created_at->startOfDay()->diffInDays(Carbon::now()->startOfDay()->addDay()) < 5 ? null : $this->games / $this->created_at->startOfDay()->diffInDays(Carbon::now()->startOfDay()->addDay());
        $this->points = $games->sum('points');
        $pointsThisMonth = clone $games;
        $this->pointsThisMonth = $pointsThisMonth->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->where('created_at', '<', Carbon::now()->startOfMonth()->addMonth())
            ->sum('points');
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
        $this->pointsPerSolo = $this->soli == 0 ? null : $this->soloPoints / $this->soli;

        /* Meiste Spiele Tag ************************* */
        $groupedByDay = $this->player->games()->whereHas('round.groups', function (Builder $query) use ($groupID)
        {
            $query->where('groups.id', '=', $groupID);
        })->latest()->get()->groupBy(function ($item)
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
        $groupedByMonth = $this->player->games()->whereHas('round.groups', function (Builder $query) use ($groupID)
        {
            $query->where('groups.id', '=', $groupID);
        })->latest()->get()->groupBy(function ($item)
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
