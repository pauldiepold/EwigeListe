<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {

    protected $fillable = ['points', 'solo'];

    protected $attributes = [

    ];

    public function getDealerIndex()
    {
        return $this->round->games
                   ->where('created_at', '<', $this->created_at)
                   ->where('solo', 0)->count()
               % $this->round->players->count();

    }

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)->withTimestamps()->withPivot('points', 'soloist', 'won');
    }
}
