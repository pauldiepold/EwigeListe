<?php

namespace App\Policies;

use App\User;
use App\LiveGame;
use Illuminate\Auth\Access\HandlesAuthorization;

class LiveGamePolicy
{

    use HandlesAuthorization;

    public function update(User $user, LiveGame $liveGame)
    {
        if ($liveGame->spielerIDs->contains($user->id))
        {
            return true;
        }
    }

}
