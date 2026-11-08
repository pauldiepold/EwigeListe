<?php

namespace App\Services;

use App\Models\SocialiteUser;
use App\Models\User;

/**
 * Eine Google- oder Facebook-Identität ohne Konto, die sich die Session vom OAuth-Callback bis
 * zum Login bzw. zur Registrierung merkt. Nur so ist belegt, dass sie der Person gehört; eine ID
 * aus URL oder Formular ließe sich fälschen (#56).
 *
 * Sie verfällt nach zehn Minuten: Bricht jemand an einem geteilten Gerät ab, soll nicht der
 * nächste Login dort die fremde Identität anhängen.
 */
class PendingSocialiteLink
{
    private const SESSION_KEY = 'pending_socialite_user';

    private const LIFETIME_IN_MINUTES = 10;

    public function remember(SocialiteUser $socialiteUser): void
    {
        session()->put(self::SESSION_KEY, [
            'id' => $socialiteUser->id,
            'expires_at' => now()->addMinutes(self::LIFETIME_IN_MINUTES)->getTimestamp(),
        ]);
    }

    public function socialiteUser(): ?SocialiteUser
    {
        /** @var array{id: int, expires_at: int}|null $pending */
        $pending = session()->get(self::SESSION_KEY);

        if ($pending === null || $pending['expires_at'] < now()->getTimestamp()) {
            return null;
        }

        return SocialiteUser::whereKey($pending['id'])->whereNull('user_id')->first();
    }

    public function linkTo(User $user): void
    {
        $socialiteUser = $this->socialiteUser();
        session()->forget(self::SESSION_KEY);

        $socialiteUser?->update(['user_id' => $user->id]);
    }
}
