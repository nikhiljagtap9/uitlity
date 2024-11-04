<?php

namespace App\Http\Controllers;

use App\Jobs\TrackableJob;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function startJob()
    {
        $jobId = uniqid();
        TrackableJob::dispatch($jobId);
        return response()->json(['job_id' => $jobId]);
    }

    public function showJobProgress($jobId)
    {
        return view('job-progress', compact('jobId'));
    }
}
