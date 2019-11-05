<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{

    protected $attributes = [
        'hide' => false,
    ];

    protected $fillable = ['surname', 'name'];

    public function path()
    {
        return "/profil/{$this->id}";
    }

    public function calculate() {
        foreach ($this->profiles as $profile) {
            $profile->calculate();
        }
    }

    public function rounds()
    {
        return $this->belongsToMany(Round::class)->withTimestamps()->withPivot('index');
    }

    public function games()
    {
        return $this->belongsToMany(Game::class)
            ->withTimestamps()
            ->withPivot('points', 'soloist', 'won', 'misplayed')
            ->using(GamePlayer::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function createdRounds()
    {
        return $this->hasMany('App\Round', 'created_by');
    }

    public function createdGames()
    {
        return $this->hasMany('App\Game', 'created_by');
    }

    public function comments()
    {
        return $this->hasMany('App\Comment', 'created_by');
    }

    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'profiles')
            ->withTimestamps()
            ->using(Profile::class);
    }

    public function badges() {
        return $this->hasMany(Badge::class);
    }

    public function profiles() {
        return $this->hasMany(Profile::class);
    }
}
