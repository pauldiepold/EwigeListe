<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Listenstatistiken liegen ab jetzt in eigenen Spalten statt im JSON `stats`; bestehende Listen
 * einmalig befüllen. Danach hält `Group::calculate()` sie aktuell.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('groups')->orderBy('id')->each(function (object $group): void {
            $games = DB::table('games')
                ->join('group_round', 'group_round.round_id', '=', 'games.round_id')
                ->where('group_round.group_id', $group->id)
                ->selectRaw('COUNT(*) as total, AVG(games.points) as average')
                ->first();

            $roundsCount = DB::table('group_round')->where('group_id', $group->id)->count();

            DB::table('groups')->where('id', $group->id)->update([
                'totalGames' => $games->total,
                'pointsPerGame' => round($games->average ?? 0, 1),
                'gamesPerRound' => $roundsCount === 0 ? 0 : round($games->total / $roundsCount, 1),
            ]);
        });
    }

    public function down(): void
    {
        DB::table('groups')->update([
            'totalGames' => null,
            'pointsPerGame' => null,
            'gamesPerRound' => null,
        ]);
    }
};
