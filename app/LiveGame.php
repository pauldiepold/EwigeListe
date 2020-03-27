<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveGame extends Model
{
    public function game()
    {
        return $this->hasOne(Game::class);
    }

    public function round()
    {
        return $this->hasOneThrough(Round::class, Game::class);
    }

    public function liveRound() {
        return $this->belongsTo(LiveRound::class);
    }
}
