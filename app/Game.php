<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Game
 *
 * @property int $id
 * @property int $round_id
 * @property int|null $live_game_id
 * @property int|null $points
 * @property int $solo
 * @property int $misplay
 * @property int|null $dealerIndex
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Player|null $createdBy
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GamePlayer[] $gamePlayers
 * @property-read int|null $game_players_count
 * @property-read \App\LiveGame|null $liveGame
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Player[] $players
 * @property-read int|null $players_count
 * @property-read \App\Round $round
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereDealerIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereLiveGameId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereMisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game wherePoints($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereRoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereSolo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Game whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Game extends Model
{

    protected $guarded = [];

    protected $touches = ['round'];

    protected $attributes = [
        'misplay' => false,
    ];

    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class)
            ->withTimestamps()
            ->withPivot('points', 'soloist', 'won', 'misplayed')
            ->using(GamePlayer::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Player::class, 'created_by');
    }

    public function gamePlayers()
    {
        return $this->hasMany(GamePlayer::class);
    }

    public function liveGame()
    {
        return $this->belongsTo(LiveGame::class);
    }
}
