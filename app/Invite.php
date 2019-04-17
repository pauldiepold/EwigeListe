<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
	protected $fillable = ['pin', 'valid_until'];
	
	public function player()
	{
		return $this->belongsTo(Player::class);	
	}
}
