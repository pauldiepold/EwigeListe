<?php

namespace App\Http\Controllers;

use App\Profile;
use App\Player;
use Illuminate\Http\Request;
use App\Jobs\UpdateProfile;

class ProfileController extends Controller {

	public function index()
	{
		$profiles = Profile::join('players', 'players.id', '=', 'profiles.player_id')
            ->orderBy('profiles.games', 'desc')
            ->where('profiles.games', '>=', '10')
            ->where('players.hide', '0')
			->with('player')
			->get();

		$columns = collect();

		$columns->push(collect(['Spiele', 'games']));
		$columns->push(collect(['Punkte', 'points']));
		$columns->push(collect(['Punkteschnitt', 'pointsPerGame']));
		$columns->push(collect(['Punkte pro Sieg', 'pointsPerWin']));
		$columns->push(collect(['Punkte pro Niederlage', 'pointsPerLose']));
		$columns->push(collect(['Siege', 'gamesWon']));
		$columns->push(collect(['Niederlagen', 'gamesLost']));
		$columns->push(collect(['Gewinnrate', 'winrate']));
		$columns->push(collect(['Soli', 'soli']));
		$columns->push(collect(['Soli gewonnen', 'soliWon']));
		$columns->push(collect(['Soli verloren', 'soliLost']));
		$columns->push(collect(['Spiele bis Solo', 'soloRate']));
		$columns->push(collect(['Solo Gewinnrate', 'soloWinrate']));
		$columns->push(collect(['Solopunkte', 'soloPoints']));
		$columns->push(collect(['Meiste Spiele Tag', 'mostGamesDay']));
		$columns->push(collect(['Meiste Spiele Monat', 'mostGamesMonth']));
		$columns->push(collect(['Höchste Punktzahl', 'highestPoints']));
		$columns->push(collect(['Niedrigste Punktzahl', 'lowestPoints']));
		$columns->push(collect(['Siegsträhne', 'winStreak']));
		$columns->push(collect(['Pechsträhne', 'loseStreak']));
		
		return view('profiles.index', compact('profiles', 'columns'));
	}

    public function updateAll()
    {
        $profiles = Profile::all();

        foreach ($profiles as $profile)
        {
            //UpdateProfile::dispatch($profile);
			$profile->updateProfile();
        }

        return view('profiles.updated');
    }

    public function show(Player $player)
    {
        $profile = $player->profile;

        $rounds = $player->rounds()
            ->latest()
            ->with(['games', 'players'])
            ->paginate(15);

        return view('profiles.show', compact('player', 'profile', 'rounds'));
    }
}
