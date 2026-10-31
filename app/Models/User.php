<?php

namespace App\Models;

use App\Notifications\MyOwnResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'player_id', 'avatar_path',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'email',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin()
    {
        return $this->id == 1;
    }

    /**
     * Versteckte Spieler können sich nicht einloggen. Der Admin ist ausgenommen, damit er sich nicht selbst aussperrt.
     */
    public function isLockedOut(): bool
    {
        return $this->player->hide && ! $this->isAdmin();
    }

    public function getAvatarPathAttribute($avatar)
    {
        return $avatar ? '/storage/avatars/'.$avatar : null;
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify((new MyOwnResetPassword($token))->locale('de'));
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
