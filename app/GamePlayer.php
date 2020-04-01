<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * App\GamePlayer
 *
 * @property int $id
 * @property int $game_id
 * @property int $player_id
 * @property int|null $points
 * @property int $soloist
 * @property int $won
 * @property int $misplayed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Game $game
 * @property-read \App\Player $player
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereMisplayed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereSoloist($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\GamePlayer whereWon($value)
 * @mixin \Eloquent
 */
class GamePlayer extends Pivot
{

    protected $table = 'game_player';
    public $incrementing = true;

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

}
