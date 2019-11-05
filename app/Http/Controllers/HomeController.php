<?php

namespace App\Http\Controllers;

use App\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use App\Player;
use App\Game;
use App\Round;
use App\Comment;

class HomeController extends Controller {

    public function __construct()
    {

    }

    public function index()
    {
        $comments = Comment::where('created_at', '>=', Carbon::now()->subDays(3))->latest()->paginate(5);

        $group = Group::find(1);

        /* ****** Aktive Runden ******** */
        $currentGamesFiltered = Game::where('created_at', '>=', Carbon::now()->subDay()->toDateTimeString())
            ->orderBy('created_at', 'desc')
            ->with(['round.players', 'players'])
            ->get()
            ->unique('round_id');

        $currentGames = $currentGamesFiltered->values();
        $currentRounds = collect();
        $currentPlayers = collect();
        foreach ($currentGames as $game)
        {
            foreach ($game->players as $player)
            {
                if (!$currentPlayers->contains($player->id))
                {
                    $currentPlayers->push($player->id);
                } else
                {
                    continue 2;
                }
            }
            $currentRounds->push($game->round);
        }

        return view('home.home', compact('group', 'currentRounds', 'comments'));
    }
}
