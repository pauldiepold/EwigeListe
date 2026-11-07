<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ohne diese Tabelle scheitert der Worker beim Protokollieren eines fehlgeschlagenen Jobs, und die
 * eigentliche Fehlerursache geht verloren. Auf Prod könnte sie ohne Migration bereits existieren;
 * der Rollback lässt sie deshalb stehen, statt womöglich fremde Daten zu löschen.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('failed_jobs')) {
            return;
        }

        Schema::create('failed_jobs', function (Blueprint $table): void {
            $table->id();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        //
    }
};
