<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveRound extends Model
{
    public function round()
    {
        return $this->hasOne(Round::class);
    }

    public function liveGames()
    {
        return $this->hasMany(LiveGame::class);
    }

    public function games()
    {
        return $this->hasManyThrough(Game::class, LiveGame::class);
    }
}
