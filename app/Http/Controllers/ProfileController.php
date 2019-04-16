<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Player;
use Illuminate\Http\Request;

class ProfileController extends Controller {

    public function updateAll()
    {
        $profiles = Profile::all();

        foreach ($profiles as $profile)
        {
            $profile->updateProfile();
        }

        return view('profiles.updated');
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Player $player)
    {
        $profile = $player->profile;

        $rounds = $player->rounds()->orderBy('created_at', 'asc')->with(['games', 'players'])->paginate(10);

        return view('players.show', compact('player', 'profile', 'rounds'));
    }

    public function edit(Profile $profile)
    {
        //
    }

    public function update(Request $request, Profile $profile)
    {
        //
    }

    public function destroy(Profile $profile)
    {
        //
    }
}
