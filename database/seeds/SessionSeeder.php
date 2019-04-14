<?php

use Illuminate\Database\Seeder;
use App\Player;
use App\Profile;

class SessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=' . env('DB_DATABASE_old'), env('DB_USERNAME_old'), env('DB_PASSWORD_old'));
		
		$statement = $pdo->prepare("SELECT * FROM session " . env('DB_SEED_LIMIT') . " " . env('DB_SEED_LIMIT_SESSION'));
        $statement->execute(array());
        while ($row = $statement->fetch())
        {
            $round = App\Round::create([
                'old_id' => $row['id'],
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
    }
}
