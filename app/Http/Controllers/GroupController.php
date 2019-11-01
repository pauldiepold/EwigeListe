<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GroupController extends Controller
{

    public function index()
    {
        $groups = Group::with('players')->get();

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

        return redirect($group->path());
    }


    public function show(Group $group = null)
    {
        $group = isset($group) ? $group : Group::find(1);
        $group->load(['players.profiles', 'profiles.player']);

        $rounds = $group->rounds()
            ->whereHas('groups', function (Builder $query) use ($group)
            {
                $query->where('groups.id', '=', $group->id);
            })
            ->with('players')
            ->withCount('games')
            ->get();

        return view('groups.show', compact('group', 'rounds'));
    }


    public function edit(Group $group)
    {
        //
    }


    public function update(Request $request, Group $group)
    {
        //
    }


    public function destroy(Group $group)
    {
        //
    }
}
