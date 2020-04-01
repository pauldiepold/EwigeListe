<?php

namespace App;

use App\Jobs\UpdateGroup;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * App\Group
 *
 * @property int $id
 * @property string $name
 * @property int $created_by
 * @property int|null $queued
 * @property int|null $closed
 * @property \Illuminate\Support\Collection|null $records
 * @property \Illuminate\Support\Collection|null $stats
 * @property \Illuminate\Support\Collection|null $mostGames
 * @property \Illuminate\Support\Collection|null $highestPoints
 * @property \Illuminate\Support\Collection|null $lowestPoints
 * @property \Illuminate\Support\Collection|null $highestWinrate
 * @property \Illuminate\Support\Collection|null $lowestWinrate
 * @property \Illuminate\Support\Collection|null $highestSoloWinrate
 * @property \Illuminate\Support\Collection|null $lowestSoloWinrate
 * @property \Illuminate\Support\Collection|null $highestSoloRate
 * @property \Illuminate\Support\Collection|null $lowestSoloRate
 * @property \Illuminate\Support\Collection|null $highestWinstreak
 * @property \Illuminate\Support\Collection|null $highestLosestreak
 * @property \Illuminate\Support\Collection|null $mostGamesDay
 * @property \Illuminate\Support\Collection|null $mostGamesMonth
 * @property int|null $totalGames
 * @property mixed|null $pointsPerGame
 * @property mixed|null $gamesPerRound
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Badge[] $badges
 * @property-read int|null $badges_count
 * @property-read \App\Player $creator
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @property-read int|null $players_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Profile[] $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Round[] $rounds
 * @property-read int|null $rounds_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereClosed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereGamesPerRound($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestLosestreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereHighestWinstreak($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestPoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestSoloRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestSoloWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereLowestWinrate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereMostGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereMostGamesDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereMostGamesMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group wherePointsPerGame($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereQueued($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereRecords($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereStats($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereTotalGames($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Group whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Group extends Model
{

    protected $fillable = [
        'name',
        'created_by'
    ];

    protected $hidden = [
        'queued',
        'records',
        'stats',
        'mostGames',
        'highestPoints',
        'lowestPoints',
        'highestWinrate',
        'lowestWinrate',
        'highestSoloWinrate',
        'lowestSoloWinrate',
        'highestSoloRate',
        'lowestSoloRate',
        'highestWinstreak',
        'highestLosestreak',
        'mostGamesDay',
        'mostGamesMonth',
        'totalGames',
        'pointsPerGame',
        'gamesPerRound'
    ];

    public function updateStats($date = null)
    {
        if (!$this->queued)
        {
            $this->queued = true;
            $this->save();
            UpdateGroup::dispatch($this, $date);
        }
    }

    public static function updateManyStats(Collection $groups, $date = null)
    {
        foreach ($groups as $group)
        {
            if (!$group->queued)
            {
                $group->queued = true;
                $group->save();
                UpdateGroup::dispatch($group, $date);
            }
        }
    }

    protected $dates = [

    ];

    protected $casts = [
        'records' => 'collection',
        'stats' => 'collection',
        'mostGames' => 'collection',
        'highestPoints' => 'collection',
        'lowestPoints' => 'collection',
        'highestWinrate' => 'collection',
        'lowestWinrate' => 'collection',
        'highestSoloWinrate' => 'collection',
        'lowestSoloWinrate' => 'collection',
        'highestSoloRate' => 'collection',
        'lowestSoloRate' => 'collection',
        'highestWinstreak' => 'collection',
        'highestLosestreak' => 'collection',
        'mostGamesDay' => 'collection',
        'mostGamesMonth' => 'collection',
        'pointsPerGame' => 'decimal:1',
        'gamesPerRound' => 'decimal:1'
    ];

    public function calculateBadges()
    {
        $currentMonth = $this->rounds()
            ->orderBy('created_at', 'asc')
            ->first()
            ->created_at
            ->startOfMonth();

        while ($currentMonth->lessThan(Carbon::now()))
        {
            $this->calculateBadgesByMonth($currentMonth);
            $currentMonth->addMonth();
        }
    }

    public function calculateBadgesByMonth(Carbon $date)
    {
        $pointsBadge = Badge::firstOrCreate([
            'group_id' => $this->id,
            'year' => $date->year,
            'month' => $date->month,
            'type' => 'points'
        ], [

        ]);
        $pointsBadge->calculate();

        $gamesBadge = Badge::firstOrCreate([
            'group_id' => $this->id,
            'year' => $date->year,
            'month' => $date->month,
            'type' => 'games'
        ], [

        ]);
        $gamesBadge->calculate();
    }

    public function calculate($date = null)
    {
        $date = isset($date) ? $date : Carbon::now();
        $this->calculateBadgesByMonth($date);

        $columns = collect([
            collect(['mostGames', 'games', 'max', 'Meiste Spiele:', 'games', 0, '']),
            collect(['highestPoints', 'highestPoints', 'max', 'Höchste Punktzahl:', 'games', 0, '']),
            collect(['lowestPoints', 'lowestPoints', 'min', 'Niedrigste Punktzahl:', 'games', 0, '']),
            collect(['highestWinrate', 'winrate', 'max', 'Höchste Gewinnrate:', 'games', 50, '%']),
            collect(['lowestWinrate', 'winrate', 'min', 'Niedrigste Gewinnrate:', 'games', 50, '%']),
            collect(['highestSoloWinrate', 'soloWinrate', 'max', 'Höchste Solo-Gewinnrate:', 'soli', 10, '%']),
            collect(['lowestSoloWinrate', 'soloWinrate', 'min', 'Niedrigste Solo-Gewinnrate:', 'soli', 10, '%']),
            collect(['lowestSoloRate', 'soloRate', 'min', 'Wenigste Spiele bis Solo:', 'soli', 10, '']),
            collect(['highestSoloRate', 'soloRate', 'max', 'Meiste Spiele bis Solo:', 'soli', 10, '']),
            collect(['highestWinstreak', 'winStreak', 'max', 'Längste Sieges-Strähne:', 'games', 0, '']),
            collect(['highestLosestreak', 'loseStreak', 'max', 'Längste Pech-Strähne:', 'games', 0, '']),
            collect(['mostGamesDay', 'mostGamesDay', 'max', 'Meiste Spiele an einem Tag:', 'games', 0, '']),
            collect(['mostGamesMonth', 'mostGamesMonth', 'max', 'Meiste Spiele in einem Monat:', 'games', 0, ''])
        ]);

        $col = collect();

        foreach ($columns as $column)
        {
            $columnName = $column->get(0);
            $highscoresCollection = $this->getHighscoreCollection($column);
            $this->$columnName = $highscoresCollection;
            if ($highscoresCollection->get(2) != '')
            {
                $col->push($highscoresCollection);
            }
        }

        $this->records = $col;
        $this->stats = $this->calcStats();

        $this->save();
    }

    private function getHighscoreCollection($column)
    {
        $minmax = $column->get(2);
        $value = $this->profiles()
            ->where('profiles.' . $column->get(4), '>', $column->get(5))
            ->$minmax($column->get(1));

        $profiles = $this->profiles()
            ->where('profiles.' . $column->get(4), '>', $column->get(5))
            ->where('profiles.' . $column->get(1), $value)
            ->with('player')
            ->get();

        $col = collect();
        $col->push($column->get(3));
        $col->push($value . $column->get(6));
        $col->push($this->getPlayerLink($profiles));

        return $col;
    }

    private function getPlayerLink($profiles)
    {
        $links = collect();

        foreach ($profiles as $profile)
        {
            $links->push('<a href="' . $profile->path() . '">' . $profile->player->surname . '</a>');
        }

        return niceCount($links);
    }

    private function calcStats() {

        $colStats = collect();
        $groupID = $this->id;

        /* ***** Spiele insgesamt *****/
        $gamesAll = Game::whereHas('round.groups', function (Builder $query) use ($groupID)
            {
                $query->where('groups.id', '=', $groupID);
            })
            ->count();

        $colRow = collect();
        $colRow->push('Spiele insgesamt:');
        $colRow->push($gamesAll);
        $colStats->push($colRow);


        /* ***** Punkte Durchschnitt *****/

        $pointsAvg = Game::whereHas('round.groups', function (Builder $query) use ($groupID)
            {
                $query->where('groups.id', '=', $groupID);
            })
            ->avg('points');

        $colRow = collect();
        $colRow->push('∅ Punktzahl pro Spiel:');
        $colRow->push(round($pointsAvg, 1));
        $colStats->push($colRow);

        /* ***** Spiele Schnitt pro Runde *****/

        $gamesAvg = Round::whereHas('groups', function (Builder $query) use ($groupID)
            {
                $query->where('groups.id', '=', $groupID);
            })
            ->withCount('games')
            ->get()
            ->avg('games_count');

        $colRow = collect();
        $colRow->push('∅ Anzahl Spiele pro Runde:');
        $colRow->push(round($gamesAvg, 1));
        $colStats->push($colRow);

        return $colStats;

    }

    public function addPlayer($player) {
        $this->players()->save($player);
    }

    public function addPlayers(Collection $players)
    {
        $this->players()->saveMany($players->diff($this->players));
    }

    public function path()
    {
        return route('groups.show', ['group' => $this->id]);
    }

    public function creator()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'profiles')->withTimestamps()->using(Profile::class);
    }

    public function rounds()
    {
        return $this->belongsToMany(Round::class)->withTimestamps();
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->where('player_id', '!=', 0);
    }

    public function games()
    {

    }
}
