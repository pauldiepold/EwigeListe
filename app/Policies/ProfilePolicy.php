<?php

namespace App\Policies;

use App\Profile;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProfilePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Profile $profile)
    {
        return $user->player_id == $profile->player_id;
    }
}
