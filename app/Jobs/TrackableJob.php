<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JobProgress; // Import your model

class TrackableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobId)
    {
        $this->jobId = $jobId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Simulate job progress
        for ($i = 1; $i <= 10000; $i += 10) {
            // Update progress in database
            JobProgress::updateOrCreate(
                ['job_id' => $this->jobId],
                ['progress' => $i, 'status' => 'In Progress']
            );

            // Simulate work
            sleep(10);
        }

        // Final status update
        JobProgress::updateOrCreate(
            ['job_id' => $this->jobId],
            ['progress' => 100, 'status' => 'Completed']
        );
    }
}
