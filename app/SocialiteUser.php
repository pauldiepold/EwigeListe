<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\SocialiteUser
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $provider
 * @property string $provider_id
 * @property string $name
 * @property string $email
 * @property string|null $avatar
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereProviderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SocialiteUser whereUserId($value)
 * @mixin \Eloquent
 */
class SocialiteUser extends Model
{

    protected $fillable = ['user_id', 'provider', 'provider_id', 'name', 'email', 'avatar'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function firstOrCreate($providerUser, $provider)
    {
        $socialiteUser = SocialiteUser::where('provider', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if($socialiteUser) {
            return $socialiteUser;
        } else {
            $socialiteUser = SocialiteUser::create([
                'provider' => $provider,
                'provider_id' => $providerUser->getId(),
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'avatar' => $providerUser->getAvatar(),
            ]);

            return $socialiteUser;
        }
    }
}
