<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
