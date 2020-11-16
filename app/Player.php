<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Player
 *
 * @property int $id
 * @property string $surname
 * @property string $name
 * @property int|null $payment
 * @property int $hide
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Badge[] $badges
 * @property-read int|null $badges_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Comment[] $comments
 * @property-read int|null $comments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $createdGames
 * @property-read int|null $created_games_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $createdGroups
 * @property-read int|null $created_groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Round[] $createdRounds
 * @property-read int|null $created_rounds_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\GamePlayer[] $gamePlayers
 * @property-read int|null $game_players_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Game[] $games
 * @property-read int|null $games_count
 * @property-read mixed $avatar_path
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Group[] $groups
 * @property-read int|null $groups_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Profile[] $profiles
 * @property-read int|null $profiles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Round[] $rounds
 * @property-read int|null $rounds_count
 * @property-read \App\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereHide($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereSurname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Player whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Player extends Model
{

    protected $attributes = [
        'hide' => false,
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
        return $this->user->avatar_path;
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
