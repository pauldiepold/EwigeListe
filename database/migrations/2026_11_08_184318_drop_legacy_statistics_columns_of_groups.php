<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekorde leitet die Liste aus den Profilen ab, Statistiken liegen in eigenen Spalten (#30). Die
 * alten Text-Spalten des Blade-Frontends sind seitdem ungenutzt. Ein Rollback auf `master` braucht
 * ohnehin das DB-Backup vom Go-Live.
 */
return new class extends Migration
{
    private const COLUMNS = [
        'records',
        'stats',
        'mostGames',
        'highestPoints',
        'lowestPoints',
        'highestWinrate',
        'lowestWinrate',
        'highestSoloWinrate',
        'lowestSoloWinrate',
        'highestSoloRate',
        'lowestSoloRate',
        'highestWinstreak',
        'highestLosestreak',
        'mostGamesDay',
        'mostGamesMonth',
    ];

    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table): void {
            $table->dropColumn(self::COLUMNS);
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table): void {
            foreach (self::COLUMNS as $column) {
                $table->text($column)->nullable();
            }
        });
    }
};
