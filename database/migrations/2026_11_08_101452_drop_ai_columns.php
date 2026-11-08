<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Die nie aktivierte KI ist aus der App entfernt (#98). Auf Prod gibt es keine KI-Spieler und keine KI-Spiele.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn('ai_path');
        });

        Schema::table('live_games', function (Blueprint $table) {
            $table->dropColumn('is_with_ai');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->string('ai_path')->nullable();
        });

        Schema::table('live_games', function (Blueprint $table) {
            $table->string('is_with_ai')->after('live_round_id')->nullable();
        });
    }
};
