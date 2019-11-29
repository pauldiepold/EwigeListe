<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialiteUser extends Model
{

    protected $fillable = ['user_id', 'provider', 'provider_id'];

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
            ]);

            return $socialiteUser;
        }
    }
}
