<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->integer('normalGames')->nullable()->after('winrate');
            $table->integer('normalGamesWon')->nullable()->after('normalGames');
            $table->integer('normalGamesLost')->nullable()->after('normalGamesWon');
            $table->float('normalGameWinrate', 4, 1)->nullable()->after('normalGamesLost');
            $table->integer('gamesCreated')->nullable()->after('loseStreakEnd');
            $table->float('gamesCreateRate', 4, 1)->nullable()->after('gamesCreated');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['normalGames', 'normalGamesWon', 'normalGamesLost', 'normalGameWinrate', 'gamesCreated', 'gamesCreateRate']);
        });
    }
};
