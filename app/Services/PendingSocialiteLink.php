<?php

namespace App\Services;

use App\Models\SocialiteUser;
use App\Models\User;

/**
 * Eine Google- oder Facebook-Identität ohne Konto, die sich die Session vom OAuth-Callback bis
 * zum Login bzw. zur Registrierung merkt. Nur so ist belegt, dass sie der Person gehört; eine ID
 * aus URL oder Formular ließe sich fälschen (#56).
 */
class PendingSocialiteLink
{
    private const SESSION_KEY = 'pending_socialite_user_id';

    public function remember(SocialiteUser $socialiteUser): void
    {
        session()->put(self::SESSION_KEY, $socialiteUser->id);
    }

    public function socialiteUser(): ?SocialiteUser
    {
        $socialiteUserId = session()->get(self::SESSION_KEY);

        if ($socialiteUserId === null) {
            return null;
        }

        return SocialiteUser::whereKey($socialiteUserId)->whereNull('user_id')->first();
    }

    public function linkTo(User $user): void
    {
        $socialiteUser = $this->socialiteUser();
        session()->forget(self::SESSION_KEY);

        $socialiteUser?->update(['user_id' => $user->id]);
    }
}
