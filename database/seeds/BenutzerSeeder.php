<?php

use App\User;
use Illuminate\Database\Seeder;
use App\Player;
use App\Profile;
use Illuminate\Support\Facades\Hash;

class BenutzerSeeder extends Seeder {

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
            $player = Player::create([
                'old_id' => $row['id'],
                'hide' => $row['verstecken'],
                'surname' => $row['vorname'],
                'name' => $row['nachname'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['created_at'],
            ]);

            $profile = Profile::create();
            $profile->player()->associate($player)->save();

            $user = User::create([
                'player_id' => $player->id,
                'email' => $row['email'],
                'password' => $row['passwort'],
            ]);
            $user->player()->associate($player)->save();
        }
    }
}
