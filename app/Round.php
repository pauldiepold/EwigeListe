<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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
