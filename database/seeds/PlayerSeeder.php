<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tables = [
            'players',
            'users',
            'rounds',
            'games',
        ];

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
        $this->command->info('players table seeded');


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
        $this->command->info('users table seeded');


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
        $this->command->info('invitations table seeded');


        $rows = DB::connection('seed')->table('rounds')->get();

        foreach ($rows as $row)
        {
            DB::table('rounds')->insert([
                'id' => $row->id,
                'created_by' => $row->created_by,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at
            ]);
        }
        $this->command->info('rounds table seeded');


        $rows = DB::connection('seed')->table('games')->get();

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
        }
        $this->command->info('games table seeded');


        $rows = DB::connection('seed')->table('game_player')->get();

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
        }
        $this->command->info('game_player table seeded');


        $rows = DB::connection('seed')->table('player_round')->get();

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
        }
        $this->command->info('player_round table seeded');


        $rows = DB::connection('seed')->table('password_resets')->get();

        foreach ($rows as $row)
        {
            DB::table('password_resets')->insert([
                'email' => $row->email,
                'token' => $row->token,
                'created_at' => $row->created_at,
            ]);
        }
        $this->command->info('password_resets table seeded');


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
        $this->command->info('profiles table seeded');
    }
}
