<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `surname` enthielt den Vornamen und `name` den Nachnamen, englisch gelesen also verkehrt herum (#38).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->renameColumn('surname', 'first_name');
            $table->renameColumn('name', 'last_name');
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->renameColumn('first_name', 'surname');
            $table->renameColumn('last_name', 'name');
        });
    }
};
