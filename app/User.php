<?php

namespace App;

use App\Notifications\MyOwnResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'player_id', 'avatar_path'
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

    public function getAvatarPathAttribute($avatar)
    {
        return $avatar ? '/storage/avatars/' . $avatar : '/storage/avatars/default.jpg';
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
