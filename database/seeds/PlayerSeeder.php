<?php

use App\Game;
use App\Group;
use App\Player;
use App\Round;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PlayerSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $date300 = Carbon::now()->subMinutes(60 * 24 * 300);
        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Diepold', 'surname' => 'Paul'])->id;
            },
            'email' => 'test1@test.de',
            'password' => Hash::make('test')]);

        /* **** Jakobs KI-Spieler ***** */

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Monte', 'surname' => 'AI 1', 'ai_path' => 'lib_doko.so'])->id;
            },
            'email' => '1@ai.de',
            'password' => Hash::make('test')]);

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Carlo', 'surname' => 'AI 2', 'ai_path' => 'lib_doko.so'])->id;
            },
            'email' => '2@ai.de',
            'password' => Hash::make('test')]);

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Leclerc', 'surname' => 'AI 3', 'ai_path' => 'lib_doko.so'])->id;
            },
            'email' => '3@ai.de',
            'password' => Hash::make('test')]);

        /* ******* Nicos KI-Spieler ******** */

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Hafermilf', 'surname' => 'AI 4', 'ai_path' => 'nico'])->id;
            },
            'email' => '4@ai.de',
            'password' => Hash::make('test')]);

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Kaugummi', 'surname' => 'AI 5', 'ai_path' => 'nico'])->id;
            },
            'email' => '5@ai.de',
            'password' => Hash::make('test')]);

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Hackerman', 'surname' => 'AI 6', 'ai_path' => 'nico'])->id;
            },
            'email' => '6@ai.de',
            'password' => Hash::make('test')]);




        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Haide', 'surname' => 'Isi'])->id;
            },
            'email' => 'test2@test.de',
            'password' => Hash::make('test')]);

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Kuhn', 'surname' => 'Lina'])->id;
            },
            'email' => 'test3@test.de',
            'password' => Hash::make('test')]);

        User::factory()->create([
            'player_id' => function () {
                return Player::factory()->create(['name' => 'Klauda', 'surname' => 'Chrissi'])->id;
            },
            'email' => 'test4@test.de',
            'password' => Hash::make('test')]);

        User::factory()->count(intval(config('database.seed.players')))->create([
            'created_at' => $date300,
            'updated_at' => $date300
        ]);

        DB::table('players')->update(['created_at' => $date300, 'updated_at' => $date300]);

        $allPlayers = App\Player::all();

        $group = Group::factory()->create([
            'created_by' => $allPlayers->random(),
            'name' => 'Ewige Liste',
            'created_at' => $date300,
            'updated_at' => $date300,
        ]);
        $group->addPlayers($allPlayers);
        DB::table('profiles')->update(['created_at' => $date300, 'updated_at' => $date300]);

        $groups = Group::factory()->count(intval(config('database.seed.groups')))->create([
            'created_by' => $allPlayers->random(),
            'created_at' => $date300,
            'updated_at' => $date300,
        ]);

        for ($i = 0; $i < intval(config('database.seed.rounds')); $i++)
        {
            $date = Carbon::now()->subMinutes(rand(0, 60 * 24 * 300));

            $playerInRound = $allPlayers->random(rand(4, 7));

            $round = Round::factory()->create([
                'created_by' => $playerInRound->random(),
                'created_at' => $date,
                'updated_at' => $date
            ]);
            $round->groups()->save($group);


            $index = 0;
            foreach ($playerInRound as $player)
            {
                $round->players()->attach($player, [
                    'index' => $index,
                    'created_at' => $date,
                    'updated_at' => $date
                ]);
                $index++;
            }

            for ($u = 0; $u < rand(0, 1); $u++)
            {
                $group_temp = $groups->random()->load('players');
                $round->groups()->save($group_temp);
                $group_temp->addPlayers($playerInRound);
            }

            for ($k = 0; $k < rand(10, 25); $k++)
            {
                $date->addMinutes(8);

                $activePlayers = $round->getActivePlayers();
                $winners = $activePlayers->random(round(rand(140, 260) / 100))->pluck('id')->toArray();
                $pointsRound = rand(-1, 10);

                $misplay = (count($winners) == 3 && round(rand(0, 60) / 100) == 1) ? true : false;
                $solo = (count($winners) != 2 ? true : false);
                $solo = $misplay ? false : $solo;

                $game = Game::create([
                    'points' => $pointsRound,
                    'solo' => $solo,
                    'misplay' => $misplay,
                    'dealerIndex' => $round->getDealerIndex(),
                    'created_by' => $playerInRound->first()->id,
                    'round_id' => $round->id,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                foreach ($activePlayers as $player)
                {
                    if (count($winners) == 1 &&
                        in_array($player->id, $winners))           // Solo gewonnen
                    {
                        $soloist = true;
                        $won = true;
                        $points = 3 * $pointsRound;
                        $misplayed = false;
                    } elseif (count($winners) == 3 &&
                        !in_array($player->id, $winners))    // Solo verloren
                    {
                        $soloist = $misplay ? false : true;
                        $won = false;
                        $points = -3 * $pointsRound;
                        $misplayed = $misplay ? true : false;
                    } elseif ((count($winners) == 2 &&
                            in_array($player->id, $winners)) ||
                        (count($winners) == 3 &&
                            in_array($player->id, $winners)))    // Normalspiel gewonnen - Gegen Solo gewonnen
                    {
                        $soloist = false;
                        $won = true;
                        $points = 1 * $pointsRound;
                        $misplayed = false;
                    } elseif ((count($winners) == 2 &&
                            !in_array($player->id, $winners)) ||
                        (count($winners) == 1 &&
                            !in_array($player->id, $winners)))   // Normalspiel verloren - Gegen Solo verloren
                    {
                        $soloist = false;
                        $won = false;
                        $points = -1 * $pointsRound;
                        $misplayed = false;
                    }

                    $game->players()->attach($player->id, [
                        'won' => $won,
                        'soloist' => $soloist,
                        'points' => $points,
                        'misplayed' => $misplayed,
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);

                    App\GamePlayer::where('game_id', $game->id)->update([
                        'created_at' => $date,
                        'updated_at' => $date
                    ]);
                }

            }
            $round->updated_at = $date;
            $round->save();
        }

        App\Profile::all()->each(function ($profile, $key) {
            $profile->calculate();
        });

        App\Group::all()->each(function ($group, $key) {
            $group->calculate();
            $group->calculateBadges();
        });
    }
}
