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
		$this->call([
        	PlayerSeeder::class,
    	]);
		
        $profiles = Profile::all();
        foreach ($profiles as $profile)
        {
            $profile->updateProfile();
        }		
    }
}
