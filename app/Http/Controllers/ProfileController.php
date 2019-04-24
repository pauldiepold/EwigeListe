<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Player;
use Illuminate\Http\Request;
use App\Jobs\UpdateProfile;

class ProfileController extends Controller {

    public function updateAll()
    {
        $profiles = Profile::all();

        foreach ($profiles as $profile)
        {
            UpdateProfile::dispatch($profile);
			//$profile->updateProfile();
        }

        return view('profiles.updated');
    }

    public function show(Player $player)
    {
        $profile = $player->profile;

        $rounds = $player->rounds()
            ->latest()
            ->with(['games', 'players'])
            ->paginate(10);

        return view('profiles.show', compact('player', 'profile', 'rounds'));
    }
}
