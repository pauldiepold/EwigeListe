<?php

namespace App\Jobs;

use App\Group;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateGroup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $group;

    protected $date;

    /**
     * Create a new job instance.
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
