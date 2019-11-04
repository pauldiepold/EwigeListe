<?php

namespace App;

use App\Jobs\UpdateGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Group extends Model
{

    protected $fillable = [
        'name',
        'created_by'
    ];

    public function updateStats()
    {
        if (!$this->queued)
        {
            $this->queued = true;
            $this->save();
            UpdateGroup::dispatch($this);
        }
    }

    public static function updateManyStats(Collection $groups)
    {
        foreach ($groups as $group)
        {
            if (!$group->queued)
            {
                $group->queued = true;
                $group->save();
                UpdateGroup::dispatch($group);
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

    public function calculate()
    {
        $columns = collect([
            collect(['mostGames', 'games', 'max', 'Meiste Spiele:', 'games', 0]),
            collect(['highestPoints', 'highestPoints', 'max', 'Höchste Punktzahl:', 'games', 0]),
            collect(['lowestPoints', 'lowestPoints', 'min', 'Niedrigste Punktzahl:', 'games', 0]),
            collect(['highestWinrate', 'winrate', 'max', 'Höchste Gewinnrate:', 'games', 50]),
            collect(['lowestWinrate', 'winrate', 'min', 'Niedrigste Gewinnrate:', 'games', 50]),
            collect(['highestSoloWinrate', 'soloWinrate', 'max', 'Höchste Solo-Gewinnrate:', 'soli', 10]),
            collect(['lowestSoloWinrate', 'soloWinrate', 'min', 'Niedrigste Solo-Gewinnrate:', 'soli', 10]),
            collect(['lowestSoloRate', 'soloRate', 'min', 'Wenigste Spiele bis Solo:', 'soli', 10]),
            collect(['highestSoloRate', 'soloRate', 'max', 'Meiste Spiele bis Solo:', 'soli', 10]),
            collect(['highestWinstreak', 'winStreak', 'max', 'Längste Sieges-Strähne:', 'games', 0]),
            collect(['highestLosestreak', 'loseStreak', 'max', 'Längste Pech-Strähne:', 'games', 0]),
            collect(['mostGamesDay', 'mostGamesDay', 'max', 'Meiste Spiele an einem Tag:', 'games', 0]),
            collect(['mostGamesMonth', 'mostGamesMonth', 'max', 'Meiste Spiele in einem Monat:', 'games', 0])
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
        $col->push($value);
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

    public function addPlayers(Collection $players)
    {
        $this->players()->saveMany($players->diff($this->players));
    }

    public function path()
    {
        return route('groups.show', ['group' => $this->id]);
    }

    public function created_by()
    {
        return $this->belongsTo(Player::class);
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

    public function badges() {
        return $this->hasMany(Badge::class);
    }
}
