<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Player extends Model
{
    use HasFactory;

    protected $attributes = [
        'hide' => false,
    ];

    protected $appends = [
        'avatar_path',
        'is_ai',
    ];

    protected $fillable = ['surname', 'name'];

    public function getPathAttribute()
    {
        return $this->path();
    }

    public function path()
    {
        return "/profil/{$this->id}";
    }

    public function getAvatarPathAttribute()
    {
        return $this->user ? $this->user->avatar_path : '';
    }

    public function getIsAiAttribute()
    {
        return !is_null($this->ai_path);
    }

    public function calculate()
    {
        foreach ($this->profiles as $profile)
        {
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
            ->using(Profile::class)
            ->withCount('rounds')
            ->orderByRaw('rounds_count desc');
    }

    public function badges()
    {
        return $this->hasMany(Badge::class)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->where('player_id', '!=', 0);
    }

    public function profiles()
    {
        return $this->hasMany(Profile::class);
    }

    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }
}
