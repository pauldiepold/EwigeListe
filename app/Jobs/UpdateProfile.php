<?php

namespace App\Jobs;

use App\Profile;
use App\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProfile implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $profile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Profile $profile)
    {
        $this->profile = $profile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->profile->updateProfile();

        $this->profile->queued = false;
        $this->profile->save();
    }
}
