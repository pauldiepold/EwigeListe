<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model {

    protected $fillable = ['points', 'solo', 'dealerIndex', 'misplay', 'created_by', 'round_id'];

    protected $attributes = [
        'misplay' => false,
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)->withTimestamps()->withPivot('points', 'soloist', 'won', 'misplayed');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }
}
