<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GroupController extends Controller
{

    public function index()
    {
        $groups = Group::withCount('rounds')
            ->withCount('players')
            ->orderByRaw('rounds_count desc')
            ->get();

        return view('groups.index', compact('groups'));
    }


    public function create()
    {
        return view('groups.create');
    }


    public function store(Request $request)
    {
        $attributes = request()->validate([
            'name' => 'required'
        ]);

        $group = auth()->user()->player->createdGroups()->create($attributes);

        $group->players()->save(auth()->user()->player);

        $group->calculate();

        return redirect($group->path());
    }


    public function show(Group $group = null)
    {
        $group = isset($group) ? $group : Group::find(1);
        $selectedGroup = $group;
        $group->load(['players.profiles', 'profiles.player', 'badges.player']);

        $rounds_count = $group->rounds()
            ->whereHas('groups', function (Builder $query) use ($group)
            {
                $query->where('groups.id', '=', $group->id);
            })
            ->count();

        $badges = $group->badges->groupBy(['type', 'year']);

        return view('groups.show', compact('group', 'rounds_count', 'selectedGroup', 'badges'));
    }

    public function update(Group $group)
    {
        $player = auth()->user()->player;

        if ($group->players->contains($player))
        {
            return redirect($group->path());
        }

        $group->addPlayer($player);

        return redirect($group->path());
    }
}
