<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fehlgeschlagene Jobs ließen `queued` früher dauerhaft auf `true`, wodurch das Profil oder die
 * Liste nie wieder neu berechnet wurde. Die Jobs setzen das Flag jetzt vor der Berechnung zurück;
 * bestehende Hänger einmalig lösen.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('profiles')->where('queued', true)->update(['queued' => false]);
        DB::table('groups')->where('queued', true)->update(['queued' => false]);
    }

    public function down(): void
    {
        //
    }
};
