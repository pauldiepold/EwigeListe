<?php

namespace App\Policies;

use App\Models\LiveGame;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LiveGamePolicy
{
    use HandlesAuthorization;

    public function update(User $user, LiveGame $liveGame)
    {
        if ($liveGame->spielerIDs->contains($user->id)) {
            return true;
        }
    }
}
