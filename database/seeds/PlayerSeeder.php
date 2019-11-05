<?php

use App\Game;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
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
        factory('App\User')->create(['email' => 'paul@paul.de', 'password' => Hash::make('paul')]);
        factory('App\User')->create(['email' => 'test@test.de']);

        factory('App\User', intval(config('database.seed.players')))->create();

        $allPlayers = App\Player::all();

        $group = factory('App\Group')->create([
            'created_by' => $allPlayers->random(),
            'name' => 'Ewige Liste',
            'created_at' => Carbon::now()->subMinutes(60*24*300),
            'updated_at' => Carbon::now()->subMinutes(60*24*300),
            ]);
        $group->addPlayers($allPlayers);

        $groups = factory('App\Group', intval(config('database.seed.groups')))->create([
            'created_by' => $allPlayers->random(),
            'created_at' => Carbon::now()->subMinutes(60*24*300),
            'updated_at' => Carbon::now()->subMinutes(60*24*300),
            ]);

        for ($i = 0; $i < intval(config('database.seed.rounds')); $i++)
        {
            $date = Carbon::now()->subMinutes(rand(0, 60 * 24 * 300));

            $playerInRound = $allPlayers->random(rand(4, 7));

            $round = factory('App\Round')->create([
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
                    'updated_at' => $date
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
        }

        App\Profile::all()->each(function ($profile, $key)
        {
            $profile->calculate();
        });

        App\Group::all()->each(function ($group, $key)
        {
            $group->calculate();
            $group->calculateBadges();
        });
    }
}
