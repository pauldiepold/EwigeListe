<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmail;
use App\Http\Requests\UpdateListen;
use App\Http\Requests\UpdateName;
use App\Http\Requests\UpdatePassword;
use App\Player;
use App\Group;
use App\Profile;
use App\Round;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PlayerController extends Controller
{

    public function show(Player $player, Group $group = null)
    {
        $selectedGroup = !$group ? Group::find(1) : $group;

        $player->load(['profiles.group', 'groups', 'rounds', 'games', 'badges']);

        $badges = $player->badges()
            ->where('group_id', $selectedGroup->id)
            ->with(['player', 'group'])
            ->get()
            ->groupBy('type');

        $profile = $player->profiles()
            ->where('group_id', $selectedGroup->id)
            ->first();

        $rounds_count = $player->rounds()
            ->whereHas('groups', function (Builder $query) use ($selectedGroup)
            {
                $query->where('groups.id', '=', $selectedGroup->id);
            })
            ->count();

        return view('players.show', compact('player', 'profile', 'rounds_count', 'selectedGroup', 'badges'));

    }

}
