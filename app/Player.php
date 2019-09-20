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

    public function rounds()
    {
        return $this->belongsToMany(Round::class)->withTimestamps()->withPivot('index');
    }

    public function games()
    {
        return $this->belongsToMany(Game::class)->withTimestamps()->withPivot('points', 'soloist', 'won', 'misplayed');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
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

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }

    public function createdGroups()
    {
        return $this->hasMany(Group::class, 'created_by');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class)->withTimestamps();
    }
}
