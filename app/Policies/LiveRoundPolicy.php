<?php

namespace App\Policies;

use App\Models\LiveRound;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LiveRoundPolicy
{
    use HandlesAuthorization;

    public function update(User $user, LiveRound $liveRound)
    {
        return $liveRound->round->players->contains($user->player);
    }
}
