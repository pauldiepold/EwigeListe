<?php

namespace App\Console\Commands;

use App\Player;
use App\Profile;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DeleteInactiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-inactive {--dry-run : Nur anzeigen, ohne zu löschen} {--force : Ohne Bestätigung löschen}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Löscht Benutzer, die genau ein Profil (group_id = 1) haben und 0 Spiele gespielt haben';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Suche nach inaktiven Benutzern...');

        // Finde Spieler mit genau einem Profil (group_id = 1) und 0 Spielen
        $players = Player::whereHas('profiles', function ($query) {
            $query->where('group_id', 1)->where('games', 0);
        })
            ->withCount('profiles')
            ->having('profiles_count', '=', 1)
            ->where(function ($query) {
                $query->whereNull('name')
                    ->orWhereRaw('LOWER(name) NOT LIKE ?', ['%test%']);
            })
            ->where(function ($query) {
                $query->whereNull('surname')
                    ->orWhereRaw('LOWER(surname) NOT LIKE ?', ['%test%']);
            })
            ->get();

        if ($players->isEmpty()) {
            $this->info('Keine zu löschenden Benutzer gefunden.');
            return 0;
        }

        $playerIds = $players->pluck('id')->toArray();
        $users = User::whereIn('player_id', $playerIds)->get();

        if ($users->isEmpty()) {
            $this->info('Keine zu löschenden Benutzer gefunden.');
            return 0;
        }

        $this->info('Gefundene zu löschende Benutzer: ' . $users->count());

        // Zeige Benutzer an
        $headers = ['ID', 'E-Mail', 'Player ID'];
        $rows = [];

        foreach ($users as $user) {
            $rows[] = [
                $user->id,
                $user->email,
                $user->player_id
            ];
        }

        $this->table($headers, $rows);

        // Wenn dry-run Option gesetzt ist, beende hier
        if ($this->option('dry-run')) {
            $this->info('Dry-Run Modus: Keine Benutzer wurden gelöscht.');
            return 0;
        }

        // Bestätigung einholen, es sei denn --force ist gesetzt
        if (!$this->option('force') && !$this->confirm('Möchtest du diese ' . $users->count() . ' Benutzer wirklich löschen?')) {
            $this->info('Vorgang abgebrochen.');
            return 0;
        }

        $this->info('Beginne mit dem Löschen der Benutzer...');

        // Starte Transaktion für sicheres Löschen
        DB::beginTransaction();
        try {
            $deletedCount = 0;

            foreach ($users as $user) {
                $playerId = $user->player_id;
                $userId = $user->id;
                $email = $user->email;

                // Lösche das Profil
                Profile::where('player_id', $playerId)->delete();
                $this->line("Profile für Player ID: {$playerId} wurden gelöscht.");

                // Lösche den User
                $user->delete();
                $this->line("User ID: {$userId}, E-Mail: {$email} wurde gelöscht.");

                // Lösche den Player
                Player::where('id', $playerId)->delete();
                $this->line("Player ID: {$playerId} wurde gelöscht.");

                $deletedCount++;
            }

            DB::commit();
            $this->info("{$deletedCount} Benutzer (mit ihren Profilen und Players) wurden erfolgreich gelöscht.");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Fehler beim Löschen der Benutzer: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}