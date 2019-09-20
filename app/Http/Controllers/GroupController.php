<?php

namespace App\Http\Controllers;

use App\Group;
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


    public function show(Group $group)
    {
        return view('groups.show', compact('group'));
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
