<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Auf Prod sind die Spalten noch `float(3,1)` und fassen höchstens 99,9. Listen mit nur einer
 * langen Runde liegen darüber. Muss vor dem Befüllen der Spalten laufen.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('groups', function (Blueprint $table): void {
            $table->decimal('pointsPerGame', 6, 1)->nullable()->change();
            $table->decimal('gamesPerRound', 6, 1)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table): void {
            $table->float('pointsPerGame')->nullable()->change();
            $table->float('gamesPerRound')->nullable()->change();
        });
    }
};
