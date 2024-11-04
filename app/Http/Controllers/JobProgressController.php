<?php

namespace App\Http\Controllers;

use App\Models\JobProgress;
use Illuminate\Http\Request;

class JobProgressController extends Controller
{
    public function show($jobId)
    {
        $progress = JobProgress::where('job_id', $jobId)->first();

        if ($progress) {
            return response()->json($progress);
        }

        return response()->json(['error' => 'Job not found'], 404);
    }
}
