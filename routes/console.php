<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Schedule::command('telescope:prune')->dailyAt('03:55');

// Zum Monatswechsel, sonst zeigen Profile ohne neues Spiel weiter „Punkte in diesem Monat“ des Vormonats.
Schedule::command('stats:recalculate')->monthlyOn(1, '04:10');
