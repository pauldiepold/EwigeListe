<?php

namespace App\Policies;

use App\User;
use App\Round;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoundPolicy
{

    use HandlesAuthorization;

    public function update(User $user, Round $round)
    {
        return $round->players->contains($user->player);
    }

}
