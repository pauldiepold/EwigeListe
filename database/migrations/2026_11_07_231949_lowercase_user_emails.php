<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Fortify schreibt E-Mails beim Login, bei der Registrierung und beim Reset klein
 * (`lowercase_usernames`). Bei einer Sortierung ohne Unterschied von Groß- und Kleinschreibung
 * ändert das nichts; die Sortierung auf Prod ist aber nicht geprüft (#46).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->update(['email' => DB::raw('LOWER(email)')]);
    }
};
