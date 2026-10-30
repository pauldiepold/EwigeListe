<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Jeder Spieler gehört zu genau einem Benutzer. Dass jeder Spieler einen Benutzer hat, lässt sich
 * per Datenbank nicht erzwingen und wird hier nur einmalig geprüft. Bricht die Migration ab, wird
 * gezielt von Hand bereinigt, nicht automatisch.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->abortUnlessEveryPlayerHasExactlyOneUser();

        Schema::table('users', function (Blueprint $table): void {
            $table->unique('player_id');
        });
    }

    /**
     * MySQL nutzt den Unique-Index für den Fremdschlüssel, er lässt sich deshalb nur ohne ihn löschen.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropForeign(['player_id']);
            $table->dropUnique(['player_id']);
            $table->foreign('player_id')->references('id')->on('players');
        });
    }

    private function abortUnlessEveryPlayerHasExactlyOneUser(): void
    {
        $playersWithoutUser = DB::table('players')
            ->leftJoin('users', 'users.player_id', '=', 'players.id')
            ->whereNull('users.id')
            ->pluck('players.id');

        $playersWithSeveralUsers = DB::table('users')
            ->groupBy('player_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('player_id');

        if ($playersWithoutUser->isEmpty() && $playersWithSeveralUsers->isEmpty()) {
            return;
        }

        throw new RuntimeException(sprintf(
            'Spieler ohne Benutzer: %s. Spieler mit mehreren Benutzern: %s.',
            $playersWithoutUser->implode(', ') ?: 'keine',
            $playersWithSeveralUsers->implode(', ') ?: 'keine',
        ));
    }
};
