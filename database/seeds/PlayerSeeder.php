<?php

use App\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class PlayerSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $testUser = factory('App\User')->create(['email' => 'test@test.de']);
        $testUser->player->profile()->save(factory('App\Profile')->create());

        factory('App\User', 25)->create()
            ->each(function ($user)
            {
                $user->player->profile()->save(factory('App\Profile')->create());
            });

        $allPlayers = App\Player::all();

        for ($i = 0; $i<10; $i++) {
            factory('App\Group')->create(['created_by' => $allPlayers->random()])->addPlayers($allPlayers->random(rand(3,7)));
        }

        for ($i = 0; $i < 20; $i++)
        {
            $playerInRound = $allPlayers->random(rand(4, 7));

            $round = factory('App\Round')->create(['created_by' => $playerInRound->random()]);

            $index = 0;
            foreach ($playerInRound as $player)
            {
                $round->players()->attach($player, [
                    'index' => $index
                ]);
                $index++;
            }


            for ($k = 0; $k < rand(10, 25); $k++)
            {
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
                    ]);
                }

            }
        }

        $profiles = App\Profile::all();

        foreach ($profiles as $profile)
        {
            $profile->updateProfile();
        }
    }
}
