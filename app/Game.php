<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    protected $fillable = ['points', 'solo', 'dealerIndex', 'misplay', 'created_by', 'round_id'];

    protected $touches = ['round'];

    protected $attributes = [
        'misplay' => false,
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withTimestamps()
            ->withPivot('points', 'soloist', 'won', 'misplayed')
            ->using(GamePlayer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Player::class, 'created_by');
    }

    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function liveGame()
    {
        return $this->belongsTo(LiveGame::class);
    }
}
