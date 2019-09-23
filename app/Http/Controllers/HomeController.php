<?php

namespace App\Http\Controllers;

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

        $colStats = collect();

        /* ***** Spiele insgesamt *****/
        $gamesAll = Cache::remember('gamesAll', 60 * 30, function ()
        {
            return DB::table('games')->count();
        });
        $colRow = collect();
        $colRow->push('Spiele insgesamt:');
        $colRow->push($gamesAll);
        $colStats->push($colRow);

        /* ***** Spiele dieser Monat *****/
        $gamesThisMonth = Cache::remember('gamesThisMonth', 60 * 30, function ()
        {
            return DB::table('games')
                ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())')
                ->count();
        });
        if ($gamesThisMonth > 0)
        {
            $colRow = collect();
            $colRow->push('Spiele in diesem Monat:');
            $colRow->push($gamesThisMonth);
            $colRow->push('margin');
            $colStats->push($colRow);
        }

        /* ***** Punkte Durchschnitt *****/

        $pointsAvg = Cache::remember('pointsAvg', 60 * 60 * 24 * 7, function ()
        {
            return DB::table('games')->avg('points');
        });
        $colRow = collect();
        $colRow->push('∅ Punktzahl pro Spiel:');
        $colRow->push(round($pointsAvg, 1));
        $colStats->push($colRow);

        /* ***** Spiele Schnitt pro Runde *****/

        $gamesAvg = Cache::remember('gamesAvg', 60 * 60 * 24 * 7, function ()
        {
            return Round::withCount('games')->get()->avg('games_count');
        });
        $colRow = collect();
        $colRow->push('∅ Anzahl Spiele pro Runde:');
        $colRow->push(round($gamesAvg, 1));
        $colStats->push($colRow);

        //dd($colStats);


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

        return view('home.home', compact('colFP', 'colStats', 'currentRounds', 'comments'));
    }
}
