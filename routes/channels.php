<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use App\LiveRound;
use App\Round;
use App\Player;
use App\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('round.{round}', function ($user, Round $round) // Verbunden mit Round.vue - jeder darf
{
    if ($user->id)
    {
        return ['id' => $user->id];
    }
});

Broadcast::channel('liveRound.{liveRound}', function ($user, LiveRound $liveRound)
{
    if ($user->can('update', $liveRound))
    {
        return ['id' => $user->id];
    }
});

Broadcast::channel(
    'liveRound.{liveRound}.{player}',
    function ($user, LiveRound $liveRound, Player $player)
    {
        if ($user->id == $player->id)
        {
            if ($user->can('update', $liveRound))
            {
                return [
                    'id' => $user->id,
                    'name' => $user->player->surname . ' ' . $user->player->name
                ];
            }
        }
    }
);
