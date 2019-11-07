<?php

namespace App\Jobs;

use App\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateGroup implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group;
    protected $date;

    /**
     * Create a new job instance.
     *
     * @param $group
     */
    public function __construct(Group $group, $date)
    {
        $this->group = $group;
        $this->date = $date;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->group->calculate($this->date);

        $this->group->queued = false;
        $this->group->save();
    }
}
