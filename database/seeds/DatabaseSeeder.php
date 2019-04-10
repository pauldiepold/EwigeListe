<?php

use Illuminate\Database\Seeder;
use App\Player;
use App\Profile;

class DatabaseSeeder extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=ewigelistealt', 'root', '');

        $statement = $pdo->prepare("SELECT * FROM benutzer");
        $statement->execute(array());
        while ($row = $statement->fetch())
        {
            $player = Player::create(['old_id' => $row['id'], 'hide' => $row['verstecken'], 'surname' => $row['vorname'], 'name' => $row['nachname']]);

            $profile = Profile::create();
            $profile->player()->associate($player)->save();
        }

        $statement = $pdo->prepare("SELECT * FROM session LIMIT 500");
        $statement->execute(array());
        while ($row = $statement->fetch())
        {
            $round = App\Round::create([
                'old_id' => $row['id'],
                'active' => $row['aktiv'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['created_at']
            ]);

            for ($i = 1; $i <= 5; $i++)
            {
                if ($row['spieler_' . $i] != 0)
                {
                    $player = App\Player::firstOrFail()->where('old_id', $row['spieler_' . $i])->first();
                    $round->players()->attach($player->id, [
                        'index' => ($i - 1)
                    ]);
                }
            }
        }

        $statement = $pdo->prepare("SELECT * FROM spiel LIMIT 100");
        $statement->execute(array());
        while ($row = $statement->fetch())
        {
            $game = App\Game::create([
                'solo' => $row['solo'],
                'points' => $row['punkte'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['created_at']
            ]);

            $round_id = App\Round::firstOrFail()->where('old_id', $row['session'])->first()->id;

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

                    if ($row['s' . $i] == 3 || $row['s' . $i] == -3)
                    {
                        $soloist = true;
                    } else
                    {
                        $soloist = false;
                    }

                    if ($row['s' . $i] > 0)
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
                        'points' => $points
                    ]);
                }
            }
        }

        $profiles = Profile::all();

        foreach ($profiles as $profile)
        {
            $profile->updateProfile();
        }

        // $this->call(UsersTableSeeder::class);
    }
}
