<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model {

    protected $attributes = [
        'hide' => false,
		'old_id' => 0,
    ];
	
	protected $fillable = ['surname', 'name'];

    public function rounds()
    {
        return $this->belongsToMany(Round::class)->withTimestamps()->withPivot('index');
    }

    public function games()
    {
        return $this->belongsToMany(Game::class)->withTimestamps()->withPivot('points', 'soloist', 'won');
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
	
	public function invites()
	{
		return $this->hasMany(Invite::class);	
	}
}
