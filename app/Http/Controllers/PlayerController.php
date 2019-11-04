<?php

namespace App\Http\Controllers;

use App\Player;
use App\Group;
use App\Round;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{

    public function show(Player $player, Group $group = null)
    {
        $selectedGroup = !$group ? Group::find(1) : $group;

        $player->load(['profiles.group', 'groups', 'rounds', 'games', 'badges']);

        $badges = $player->badges()
            ->where('group_id', $selectedGroup->id)
            ->with(['player', 'group'])
            ->get();

        $profile = $player->profiles()
            ->where('group_id', $selectedGroup->id)
            ->first();

        $rounds = $player->rounds()
            ->whereHas('groups', function (Builder $query) use ($selectedGroup)
            {
                $query->where('groups.id', '=', $selectedGroup->id);
            })
            ->with('players')
            ->withCount('games')
            ->get();

        return view('players.show', compact('player', 'profile', 'rounds', 'selectedGroup', 'badges'));

    }

}
