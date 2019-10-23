<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Group extends Model
{

    protected $fillable = ['name', 'created_by'];

    public function addPlayers(Collection $players)
    {
        $this->players()->saveMany($players->diff($this->players));
    }

    public function path()
    {
        return "/groups/{$this->id}";
    }

    public function created_by()
    {
        return $this->belongsTo(Player::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'profiles')->withTimestamps()->using(Profile::class);
    }

    public function rounds()
    {
        return $this->belongsToMany(Round::class)->withTimestamps();
    }

    public function profiles() {
        return $this->hasMany(Profile::class);
    }
}
