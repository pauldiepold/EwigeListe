<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GamePlayer extends Pivot
{

    protected $table = 'game_player';
    public $incrementing = true;

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

}
