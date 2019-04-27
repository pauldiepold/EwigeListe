<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
	protected $fillable = ['pin', 'valid_until', 'player_id'];
	
	public function player()
	{
		return $this->belongsTo(Player::class);	
	}
}
