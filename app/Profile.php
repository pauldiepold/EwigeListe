<?php

namespace App;

use App\Jobs\UpdateProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * App\Profile
 *
 * @property int $id
 * @property int $player_id
 * @property int $group_id
 * @property int|null $queued
 * @property int|null $default
 * @property int|null $points
 * @property int|null $pointsThisMonth
 * @property mixed|null $pointsPerGame
 * @property mixed|null $pointsPerWin
 * @property mixed|null $pointsPerLose
 * @property int|null $games
 * @property int|null $gamesThisMonth
 * @property mixed|null $gamesPerDay
 * @property int|null $gamesWon
 * @property int|null $gamesLost
 * @property mixed|null $winrate
 * @property int|null $soli
 * @property int|null $soliWon
 * @property int|null $soliLost
 * @property int|null $soloRate
 * @property mixed|null $soloWinrate
 * @property mixed|null $pointsPerSolo
 * @property int|null $soloPoints
 * @property int|null $mostGamesDay
 * @property \Illuminate\Support\Carbon|null $mostGamesDayDate
 * @property int|null $mostGamesMonth
 * @property \Illuminate\Support\Carbon|null $mostGamesMonthDate
 * @property int|null $highestPoints
 * @property \Illuminate\Support\Carbon|null $highestPointsDate
 * @property int|null $lowestPoints
 * @property \Illuminate\Support\Carbon|null $lowestPointsDate
 * @property int|null $winStreak
 * @property \Illuminate\Support\Carbon|null $winStreakStart
 * @property \Illuminate\Support\Carbon|null $winStreakEnd
 * @property int|null $loseStreak
 * @property \Illuminate\Support\Carbon|null $loseStreakStart
 * @property \Illuminate\Support\Carbon|null $loseStreakEnd
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Group $group
 * @property-read \App\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesPerDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGamesWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereHighestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLoseStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLoseStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLoseStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereLowestPointsDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesDayDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereMostGamesMonthDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerLose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsPerWin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile wherePointsThisMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoli($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoliLost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoliWon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoloPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinStreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinStreakEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinStreakStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Profile whereWinrate($value)
 * @mixin \Eloquent
 */
class Profile extends Pivot
{

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
