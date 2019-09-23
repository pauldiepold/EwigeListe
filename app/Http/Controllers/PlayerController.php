<?php

namespace App\Http\Controllers;

use App\Player;
use App\Group;
use App\Round;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlayerController extends Controller
{

    public function index()
    {
        $players = Player::all();

        return view('players.index', compact('players'));
    }

    public function show(Player $player, Group $group = null)
    {
        $selectedGroup = !$group ? Group::find(1) : $group;

        $player->load(['profiles.group', 'groups', 'rounds', 'games']);

        $profile = $player->profiles->where('group_id', $selectedGroup->id)->first();

        $rounds = $player->rounds()
            ->latest()
            ->with(['games', 'players', 'groups'])
            ->paginate(15);

        $groups = $player->groups;

        return view('players.show', compact('player', 'profile', 'rounds', 'groups', 'selectedGroup'));

    }

}
