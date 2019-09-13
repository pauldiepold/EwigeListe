<?php

use Illuminate\Database\Seeder;
use App\Profile;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class DatabaseSeederOld extends Seeder {

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TablesSeeder::class,
        ]);
        $this->command->comment('updating: profiles');
        $profiles = Profile::all();
        $output = new ConsoleOutput();
        $bar = new ProgressBar($output, $profiles->count());
        $bar->start();

        foreach ($profiles as $profile)
        {
            $profile->updateProfile();
            $bar->advance();
        }
        $bar->finish();
        $this->command->info(' updated: profiles');
    }
}
