<?php

namespace App\Policies;

use App\Models\Round;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RoundPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Round $round)
    {
        return $round->players->contains($user->player);
    }
}
