<?php

use Illuminate\Database\Seeder;
use App\Player;
use App\Profile;

class BenutzerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pdo = new PDO('mysql:host=localhost;dbname=' . env('DB_DATABASE_old'), env('DB_USERNAME_old'), env('DB_PASSWORD_old'));
		
		$statement = $pdo->prepare("SELECT * FROM benutzer");
        $statement->execute(array());
        while ($row = $statement->fetch())
        {
            $player = Player::create(['old_id' => $row['id'], 'hide' => $row['verstecken'], 'surname' => $row['vorname'], 'name' => $row['nachname']]);

            $profile = Profile::create();
            $profile->player()->associate($player)->save();
        }
    }
}
