<?php

use Illuminate\Support\Facades\Schedule;

// Telescope ist nur lokal registriert, auf Prod gibt es den Befehl nicht.
Schedule::command('telescope:prune')->dailyAt('03:55')->environments('local');

// Zum Monatswechsel, sonst zeigen Profile ohne neues Spiel weiter „Punkte in diesem Monat“ des Vormonats.
Schedule::command('stats:recalculate')->monthlyOn(1, '04:10');
