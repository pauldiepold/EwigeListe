<?php

namespace App\Policies;

use App\Player;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlayerPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Player $player)
    {
        return $user->player_id == $player->id;
    }
}
