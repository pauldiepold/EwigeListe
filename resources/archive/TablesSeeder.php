<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class TablesSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $output = new ConsoleOutput();

        $this->command->comment('seeding: players table');

        $rows = DB::connection('seed')->table('players')->get();
        foreach ($rows as $row)
        {
            DB::table('players')->insert([
                'id' => $row->id,
                'surname' => $row->surname,
                'name' => $row->name,
                'hide' => $row->hide,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
        }

        $this->command->info('seeded: players table');
        $this->command->comment('seeding: users table');

        $rows = DB::connection('seed')->table('users')->get();
        foreach ($rows as $row)
        {
            DB::table('users')->insert([
                'id' => $row->id,
                'player_id' => $row->player_id,
                'email' => $row->email,
                'password' => $row->password,
                'remember_token' => $row->remember_token,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
        }

        $this->command->info('seeded: users table');
        $this->command->comment('seeding: invitations table');

        $rows = DB::connection('seed')->table('invitations')->get();
        foreach ($rows as $row)
        {
            DB::table('invitations')->insert([
                'id' => $row->id,
                'pin' => $row->pin,
                'player_id' => $row->player_id,
                'valid_until' => $row->valid_until,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
        }

        $this->command->info('seeded: invitations table');
        $this->command->comment('seeding: rounds table');

        $rows = DB::connection('seed')->table('rounds')->get();
        $bar = new ProgressBar($output, $rows->count());
        $bar->start();
        foreach ($rows as $row)
        {
            DB::table('rounds')->insert([
                'id' => $row->id,
                'created_by' => $row->created_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
            $bar->advance();
        }
        $bar->finish();

        $this->command->info(' seeded: rounds table');
        $this->command->comment('seeding: games table');

        $rows = DB::connection('seed')->table('games')->get();
        $bar = new ProgressBar($output, $rows->count());
        $bar->start();
        foreach ($rows as $row)
        {
            DB::table('games')->insert([
                'id' => $row->id,
                'round_id' => $row->round_id,
                'points' => $row->points,
                'solo' => $row->solo,
                'misplay' => $row->misplay,
                'dealerIndex' => $row->dealerIndex,
                'created_by' => $row->created_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
            $bar->advance();
        }
        $bar->finish();

        $this->command->info(' seeded: games table');
        $this->command->comment('seeding: game_player table');

        $rows = DB::connection('seed')->table('game_player')->get();
        $bar = new ProgressBar($output, $rows->count());
        $bar->start();
        foreach ($rows as $row)
        {
            DB::table('game_player')->insert([
                'id' => $row->id,
                'game_id' => $row->game_id,
                'player_id' => $row->player_id,
                'points' => $row->points,
                'soloist' => $row->soloist,
                'won' => $row->won,
                'misplayed' => $row->misplayed,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
            $bar->advance();
        }
        $bar->finish();

        $this->command->info('seeded: game_player table');
        $this->command->comment('seeding: player_round table');

        $rows = DB::connection('seed')->table('player_round')->get();
        $bar = new ProgressBar($output, $rows->count());
        $bar->start();
        foreach ($rows as $row)
        {
            DB::table('player_round')->insert([
                'id' => $row->id,
                'round_id' => $row->round_id,
                'player_id' => $row->player_id,
                'index' => $row->index,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
            $bar->advance();
        }
        $bar->finish();

        $this->command->info(' seeded: player_round table');
        $this->command->comment('seeding: password_resets table');


        $rows = DB::connection('seed')->table('password_resets')->get();
        foreach ($rows as $row)
        {
            DB::table('password_resets')->insert([
                'email' => $row->email,
                'token' => $row->token,
                'created_at' => $row->created_at,
            ]);
        }

        $this->command->info('seeded: password_resets table');
        $this->command->comment('seeding: profiles table');

        $rows = DB::connection('seed')->table('profiles')->get();
        foreach ($rows as $row)
        {
            DB::table('profiles')->insert([
                'id' => $row->id,
                'player_id' => $row->player_id,
                'queued' => $row->queued,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
        }
        $this->command->info('seeded: profiles table');

    }
}
