<?php

namespace App\Http\Controllers;

use App\Group;
use App\Player;
use Illuminate\Database\Eloquent\Builder;

class PlayerController extends Controller
{
    public function show(Player $player, ?Group $group = null)
    {
        $selectedGroup = ! $group ? Group::find(1) : $group;

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
            ->whereHas('groups', function (Builder $query) use ($selectedGroup) {
                $query->where('groups.id', '=', $selectedGroup->id);
            })
            ->count();

        return view('players.show', compact('player', 'profile', 'rounds_count', 'selectedGroup', 'badges'));

    }

    public function calculateAll()
    {
        Player::all()->each(function ($player, $key) {
            $player->calculate();
        });

        return redirect('/liste/1');
    }

    public function calculate(Player $player)
    {
        $player->calculate();

        return redirect($player->path());
    }
}
