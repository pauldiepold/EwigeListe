<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Round extends Model {

    protected $fillable = ['created_by'];

    protected $attributes = [];

    public function getLastGame()
    {
        return $this->games()->latest()->first();
    }

    public function getDealerIndex()
    {
        return $this->games->where('solo', 0)->where('misplay', 0)->count() % $this->players->count();
    }

    public function getActivePlayers()
    {
        $dealerIndex = $this->getDealerIndex();
$countPlayers = $this->players->count();

     /*   $playerIndices = collect();

        for ($i = 0; $i < 4; $i++)
        {
            if ($dealerIndex < $this->players->count() - 1)
            {
                $dealerIndex++;
            } else
            {
                $dealerIndex = 0;
            }
            $playerIndices->push($dealerIndex);
        }
 */

$playerIndices = collect();

if ( $countPlayers == 4 || $countPlayers == 5)
{
$increments = collect([1,2,3,4]);
} elseif ( $countPlayers == 6)
{
$increments = collect([1,2,4,5]);
} elseif($countPlayers == 7)
{
$increments = collect([1,3,5,6]);
}

foreach ($increments as $increment) {
$currentDealerIndex = $increment + $dealerIndex;
if ($currentDealerIndex >= $countPlayers) {
$currentDealerIndex -= $countPlayers;
}
$playerIndices->push($currentDealerIndex);
}

        $playerIndicesSorted = $playerIndices->sort();
        $playerIndicesSorted->values()->all();

        $players = collect();

        foreach ($playerIndicesSorted as $playerIndex)
        {
            $playerID = DB::table('player_round')
                ->where('round_id', $this->id)
                ->where('index', $playerIndex)
                ->first()->player_id;

            $players->push(Player::find($playerID));
        }

        return $players;
    }

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Player', 'created_by');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)->withTimestamps()->withPivot('index')->orderBy('pivot_index');
    }
	
	public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }
}
