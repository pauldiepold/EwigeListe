<?php

use Illuminate\Database\Seeder;
use App\Player;
use App\Round;
use App\Profile;

class SpielSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=' . env('DB_DATABASE_old'), env('DB_USERNAME_old'), env('DB_PASSWORD_old'));
		
		$rounds = Round::all();	
		
		$statement = $pdo->prepare("SELECT * FROM spiel  " . env('DB_SEED_LIMIT') . " " . env('DB_SEED_LIMIT_SPIEL'));
        $statement->execute(array());
		
        while ($row = $statement->fetch())
        {
			$solo = ($row['solo'] == 1) ? true : false;
			$misplay = ($row['solo'] == 2) ? true : false;
			
            $game = App\Game::create([
                'solo' => $solo,
				'misplay' => $misplay,
                'points' => $row['punkte'],
                'dealerIndex' => $row['geber'] - 1,
                'created_by' => null,
                'created_at' => $row['created_at'],
                'updated_at' => $row['created_at']
            ]);
			
			$round_id = $rounds->filter(function($item) use ($row) {
    						return $item->old_id == $row['session'];
						})->first()->id;

            //$round_id = App\Round::firstOrFail()->where('old_id', $row['session'])->first()->id;

            $game->round()->associate($round_id)->save();

			
            for ($i = 1; $i <= 5; $i++)
            {
                if ($row['s' . $i] != 0)
                {
                    $statement_session = $pdo->prepare("SELECT * FROM session WHERE id = ?");
                    $statement_session->execute(array($row['session']));
                    $old_session = $statement_session->fetch();

                    $old_player_id = $old_session['spieler_' . $i];

                    $player = App\Player::firstOrFail()->where('old_id', $old_player_id)->first();

                    if ( ($row['s' . $i] == 3 || $row['s' . $i] == -3) && $row['solo'] == 1)
                    {
                        $soloist = true;
                    } else
                    {
                        $soloist = false;
                    }
					
					if ( $row['s' . $i] == -3 && $row['solo'] == 2)
                    {
                        $misplayed = true;
                    } else
                    {
                        $misplayed = false;
                    }

                    if ($row['s' . $i] == 1 || $row['s' . $i] == 3)
                    {
                        $won = true;
                    } else
                    {
                        $won = false;
                    }
                    $points = $row['s' . $i] * $row['punkte'];


                    $game->players()->attach($player->id, [
                        'won' => $won,
                        'soloist' => $soloist,
                        'points' => $points,
						'misplayed' => $misplayed,
                        'created_at' => $row['created_at'],
                        'updated_at' => $row['created_at']
                    ]);
                }
            }
            $game->updated_at = $row['created_at'];
            $game->save();
        }
    }
}
