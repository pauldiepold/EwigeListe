<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * App\Round
 *
 * @property int $id
 * @property int|null $live_round_id
 * @property int $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \App\Player $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read mixed $path
 * @property-read mixed $players_string
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \App\LiveRound|null $liveRound
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @property-read int|null $players_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereLiveRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Round whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Round extends Model
{

    protected $fillable = ['created_by', 'liveGame'];

    protected $appends = ['players_string', 'path'];

    protected $attributes = [];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model)
        {
            $model->created_by = auth()->user()->player->id;
        });
    }

    public function path()
    {
        return route('rounds.show', ['round' => $this->id]);
    }

    public function getPlayersStringAttribute()
    {
        return nice_count($this->players->pluck('surname')->toArray());
    }

    public function getPathAttribute()
    {
        return $this->path();
    }

    public function startLiveRound()
    {
        $liveRound = $this->liveRound()->create();
        $this->liveRound()->associate($liveRound)->save();

        $liveRound->starteNeuesSpiel();

        return;
    }

    public function getLastGame()
    {
        return $this->games->last();
    }

    public function getDealerIndex()
    {
        return $this
                   ->games()
                   ->where('solo', 0)
                   ->where('misplay', 0)
                   ->count()
               % $this
                   ->players()
                   ->count();
    }

    public function getInactivePlayers()
    {
        return $this->players->diff($this->getActivePlayers());
    }

    public function getActivePlayers($sortPlayers = true)
    {
        $dealerIndex = $this->getDealerIndex();
        $countPlayers = $this->players->count();

        if ($countPlayers == 4 || $countPlayers == 5)
        {
            $increments = collect([1, 2, 3, 4]);
        } elseif ($countPlayers == 6)
        {
            $increments = collect([1, 2, 4, 5]);
        } elseif ($countPlayers == 7)
        {
            $increments = collect([1, 3, 5, 6]);
        }

        $playerIndices = collect();
        foreach ($increments as $increment)
        {
            $currentDealerIndex = $increment + $dealerIndex;
            if ($currentDealerIndex >= $countPlayers)
            {
                $currentDealerIndex -= $countPlayers;
            }
            $playerIndices->push($currentDealerIndex);
        }

        if ($sortPlayers)
        {
            $playerIndices = $playerIndices->sort();
            $playerIndices = $playerIndices->values()->all();
        }

        return $this->players()->wherePivotIn('index', $playerIndices)->get();
    }

    public function addNewGame($winners, $pointsRound, $misplay = false)
    {
        $players = $this->getActivePlayers();
        $solo = (count($winners) != 2 ? true : false);
        $solo = $misplay ? false : $solo;

        $game = Game::create([
            'points' => $pointsRound,
            'solo' => $solo,
            'misplay' => $misplay,
            'dealerIndex' => $this->getDealerIndex(),
            'created_by' => Auth::user()->player->id,
            'round_id' => $this->id,
        ]);

        foreach ($players as $player)
        {
            if (count($winners) == 1 &&
                in_array($player->id, $winners))           // Solo gewonnen
            {
                $soloist = true;
                $won = true;
                $points = 3 * $pointsRound;
                $misplayed = false;
            } elseif (count($winners) == 3 &&
                      !in_array($player->id, $winners))    // Solo verloren
            {
                $soloist = $misplay ? false : true;
                $won = false;
                $points = -3 * $pointsRound;
                $misplayed = $misplay ? true : false;
            } elseif ((count($winners) == 2 &&
                       in_array($player->id, $winners)) ||
                      (count($winners) == 3 &&
                       in_array($player->id, $winners)))    // Normalspiel gewonnen - Gegen Solo gewonnen
            {
                $soloist = false;
                $won = true;
                $points = 1 * $pointsRound;
                $misplayed = false;
            } elseif ((count($winners) == 2 &&
                       !in_array($player->id, $winners)) ||
                      (count($winners) == 1 &&
                       !in_array($player->id, $winners)))   // Normalspiel verloren - Gegen Solo verloren
            {
                $soloist = false;
                $won = false;
                $points = -1 * $pointsRound;
                $misplayed = false;
            }

            $game->players()->attach($player->id, [
                'won' => $won,
                'soloist' => $soloist,
                'points' => $points,
                'misplayed' => $misplayed,
            ]);
        }

        return $game;
    }

    public function games()
    {
        return $this->hasMany(Game::class)->orderBy('id', 'asc');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withTimestamps()
            ->withPivot('index')
            ->orderBy('pivot_index');
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)
            ->withTimestamps()
            ->withCount('rounds')
            ->orderByRaw('rounds_count desc');
    }

    public function profiles()
    {
        return Profile::whereIn('group_id', $this->groups->pluck('id'))
            ->whereIn('player_id', $this->players->pluck('id'))
            ->get();
    }

    public function liveRound()
    {
        return $this->belongsTo(LiveRound::class);
    }
}
