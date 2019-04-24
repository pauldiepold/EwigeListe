<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Player;

class HomeController extends Controller {

    public function __construct()
    {

    }

    public function index()
    {
        $colFP = collect();

        $query = DB::table('profiles')
            ->join('players', 'profiles.player_id', '=', 'players.id')
            ->orderBy('players.surname', 'asc')
            ->select('players.surname', 'players.name', 'profiles.*');

        /* ***** Spiele *****/
        $mostGames = DB::table('profiles')->max('games');
        $queryTemp = clone $query;
        $mostGamesPlayer = $queryTemp->where('profiles.games', $mostGames)->get();
        $colRow = collect();
        $colRow->push('Meiste Spiele:');
        $colRow->push($mostGames);
        $players = collect();
        foreach ($mostGamesPlayer as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colFP->push($colRow);
		
		/* ***** Meiste Spiele dieser Monat *****/
        $mostGamesThisMonth = DB::table('game_player')
            ->selectRaw('Count(*) as games, player_id')
            ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())')
            ->groupBy('player_id')
            ->orderBy('games', 'desc')
            ->get();
		if ($mostGamesThisMonth->count() > 0 ) {
		$mostGamesThisMonth = $mostGamesThisMonth->where('games', $mostGamesThisMonth->first()->games);
		}
        $colRow = collect();
        $colRow->push('Meiste Spiele in diesem Monat:');
        $colRow->push($mostGamesThisMonth->first()->games);		
        $players = collect();
		foreach($mostGamesThisMonth as $playerID) {
        	$player = Player::find($playerID->player_id);
        	$players->push('<a href="/profiles/' . $player->id . '">' . $player->surname . '</a>');
		}
        $colRow->push(niceCount($players));
        $colFP->push($colRow);


        /* ***** Meiste Punkte dieser Monat *****/
        $mostPointsThisMonth = DB::table('game_player')
            ->selectRaw('SUM(points) as points, player_id')
            ->whereRaw('MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())')
            ->groupBy('player_id')
            ->orderBy('points', 'desc')
            ->get();
		if ($mostPointsThisMonth->count() > 0 ) {
		$mostPointsThisMonth = $mostPointsThisMonth->where('points', $mostPointsThisMonth->first()->points);
		}
        $colRow = collect();
        $colRow->push('Meiste Punkte in diesem Monat:');
        $colRow->push($mostPointsThisMonth->first()->points);
        $players = collect();
		foreach($mostPointsThisMonth as $playerID) {
        	$player = Player::find($playerID->player_id);
        	$players->push('<a href="/profiles/' . $player->id . '">' . $player->surname . '</a>');
		}
        $colRow->push(niceCount($players));
        $colRow->push('margin');
        $colFP->push($colRow);
		
        /* ***** Punkte hoch *****/
        $highestPoints = DB::table('profiles')->max('highestPoints');
        $queryTemp = clone $query;
        $highestPointsPlayers = $queryTemp->where('profiles.highestPoints', $highestPoints)->get();
        $colRow = collect();
        $colRow->push('Höchste Punktzahl:');
        $colRow->push($highestPoints);
        $players = collect();
        foreach ($highestPointsPlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colFP->push($colRow);

        /* ***** Punkte niedrig *****/
        $lowestPoints = DB::table('profiles')->min('lowestPoints');
        $queryTemp = clone $query;
        $lowestPointsPlayers = $queryTemp->where('profiles.lowestPoints', $lowestPoints)->get();
        $colRow = collect();
        $colRow->push('Niedrigste Punktzahl:');
        $colRow->push($lowestPoints);
        $players = collect();
        foreach ($lowestPointsPlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colRow->push('margin');
        $colFP->push($colRow);

        /* ***** Winrate hoch *****/
        $highestWinrate = DB::table('profiles')
            ->where('games', '>', 50)
            ->max('winrate');
        $queryTemp = clone $query;
        $highestWinratePlayers = $queryTemp->where('profiles.winrate', $highestWinrate)->get();
        $colRow = collect();
        $colRow->push('Höchste Gewinnrate:');
        $colRow->push($highestWinrate . '%');
        $players = collect();
        foreach ($highestWinratePlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colFP->push($colRow);

        /* ***** Winrate niedrig *****/
        $lowestWinrate = DB::table('profiles')
            ->where('games', '>', 50)
            ->min('winrate');
        $queryTemp = clone $query;
        $lowestWinratePlayers = $queryTemp->where('profiles.winrate', $lowestWinrate)->get();
        $colRow = collect();
        $colRow->push('Niedrigste Gewinnrate:');
        $colRow->push($lowestWinrate . '%');
        $players = collect();
        foreach ($lowestWinratePlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colRow->push('margin');
        $colFP->push($colRow);

        /* ***** Winrate Solo hoch *****/
        $highestSoloWinRate = DB::table('profiles')
            ->where('soli', '>', 10)
            ->max('soloWinrate');
        $queryTemp = clone $query;
        $highestSoloWinRatePlayers = $queryTemp->where('profiles.soloWinrate', $highestSoloWinRate)->get();
        $colRow = collect();
        $colRow->push('Höchste Solo-Gewinnrate:');
        $colRow->push($highestSoloWinRate . '%');
        $players = collect();
        foreach ($highestSoloWinRatePlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colFP->push($colRow);

        /* ***** Winrate Solo niedrig *****/
        $lowestSoloWinRate = DB::table('profiles')
            ->where('soli', '>', 10)
            ->min('soloWinrate');
        $queryTemp = clone $query;
        $lowestSoloWinRatePlayers = $queryTemp->where('profiles.soloWinrate', $lowestSoloWinRate)->get();
        $colRow = collect();
        $colRow->push('Niedrigste Solo-Gewinnrate:');
        $colRow->push($lowestSoloWinRate . '%');
        $players = collect();
        foreach ($lowestSoloWinRatePlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colRow->push('margin');
        $colFP->push($colRow);

        /* ***** Wenigste Spiele bis Solo *****/
        $lowestSoloRate = DB::table('profiles')
            ->where('soli', '>', 10)
            ->min('soloRate');
        $queryTemp = clone $query;
        $lowestSoloRatePlayers = $queryTemp->where('profiles.soloRate', $lowestSoloRate)->get();
        $colRow = collect();
        $colRow->push('Wenigste Spiele bis Solo:');
        $colRow->push($lowestSoloRate);
        $players = collect();
        foreach ($lowestSoloRatePlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colFP->push($colRow);

        /* ***** Meiste Spiele bis Solo *****/
        $highestSoloRate = DB::table('profiles')
            ->where('soli', '>', 10)
            ->max('soloRate');
        $queryTemp = clone $query;
        $highestSoloRatePlayers = $queryTemp->where('profiles.soloRate', $highestSoloRate)->get();
        $colRow = collect();
        $colRow->push('Meiste Spiele bis Solo:');
        $colRow->push($highestSoloRate);
        $players = collect();
        foreach ($highestSoloRatePlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colRow->push('margin');
        $colFP->push($colRow);

        /* ***** Sieges-Strähne *****/
        $highestWinStreak = DB::table('profiles')->max('winStreak');
        $queryTemp = clone $query;
        $highestWinStreakPlayers = $queryTemp->where('profiles.winStreak', $highestWinStreak)->get();
        $colRow = collect();
        $colRow->push('Längste Sieges-Strähne:');
        $colRow->push($highestWinStreak);
        $players = collect();
        foreach ($highestWinStreakPlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colFP->push($colRow);

        /* ***** Pech-Strähne *****/
        $highestLoseStreak = DB::table('profiles')->max('loseStreak');
        $queryTemp = clone $query;
        $highestLoseStreakPlayers = $queryTemp->where('profiles.loseStreak', $highestLoseStreak)->get();
        $colRow = collect();
        $colRow->push('Längste Pech-Strähne:');
        $colRow->push($highestLoseStreak);
        $players = collect();
        foreach ($highestLoseStreakPlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colRow->push('margin');
        $colFP->push($colRow);

        /* ***** Meiste Spiele Tag *****/
        $mostGamesDay = DB::table('profiles')->max('mostGamesDay');
        $queryTemp = clone $query;
        $mostGamesDayPlayers = $queryTemp->where('profiles.mostGamesDay', $mostGamesDay)->get();
        $colRow = collect();
        $colRow->push('Meiste Spiele an einem Tag:');
        $colRow->push($mostGamesDay);
        $players = collect();
        foreach ($mostGamesDayPlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colFP->push($colRow);

        /* ***** Meiste Spiele Monat *****/
        $mostGamesMonth = DB::table('profiles')->max('mostGamesMonth');
        $queryTemp = clone $query;
        $mostGamesMonthPlayers = $queryTemp->where('profiles.mostGamesMonth', $mostGamesMonth)->get();
        $colRow = collect();
        $colRow->push('Meiste Spiele in einem Monat:');
        $colRow->push($mostGamesMonth);
        $players = collect();
        foreach ($mostGamesMonthPlayers as $player)
        {
            $players->push('<a href="/profiles/' . $player->player_id . '">' . $player->surname . '</a>');
        }
        $colRow->push(niceCount($players));
        $colRow->push('margin');
        $colFP->push($colRow);


        //dd($colFP);

        return view('home.home', compact('colFP'));
    }
}
