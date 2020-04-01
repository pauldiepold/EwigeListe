<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Badge
 *
 * @property int $id
 * @property int $group_id
 * @property int $year
 * @property int $month
 * @property string $type
 * @property int|null $player_id
 * @property int|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $date
 * @property-read \App\Group $group
 * @property-read \App\Player|null $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Badge whereYear($value)
 * @mixin \Eloquent
 */
class Badge extends Model
{
    protected $guarded = [];

    public function getDateAttribute()
    {
        return Carbon::createMidnightDate($this->year, $this->month, 1);
    }

    public function playerPath() {
        return $this->player->path() . '/' . $this->group_id . '#abzeichen';
    }

    public function calculate()
    {
        $group = $this->group;
        $highestPlayer = null;
        $highestValue = 0;
        foreach ($group->players as $player)
        {
            $gamePlayers = GamePlayer::where('player_id', $player->id)
                ->whereHas('game.round.groups', function (Builder $query) use ($group)
                {
                    $query->where('groups.id', '=', $group->id);
                })
                ->where('created_at', '>=', $this->date)
                ->where('created_at', '<', $this->date->addMonth());

            if ($this->type == 'games') {
                $value = $gamePlayers->count();
            } else if ($this->type == 'points') {
                $value = $gamePlayers->sum('points');
            } else {
                $value = 0;
            }

            if ($value > $highestValue) {
                $highestValue = $value;
                $highestPlayer = $player;
            }
        }

        if(isset($highestPlayer))
        {
            $this->value = $highestValue;
            $this->player_id = $highestPlayer->id;
            $this->save();
        }
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
