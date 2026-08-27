<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Services\JobService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderJobController extends Controller
{
    public function __construct(protected JobService $jobService) {}

    public function start(ServiceJob $job)
    {
        $provider = Auth::user();
        
        // Security check
        if ($job->booking->provider_id !== $provider->id) {
            abort(403, 'Unauthorized');
        }

        // Use the existing JobService
        $this->jobService->startJob($job, $provider->id);

        return back()->with('success', 'Job marked as Started successfully.');
    }

    public function complete(ServiceJob $job, Request $request)
    {
        $provider = Auth::user();
        
        if ($job->booking->provider_id !== $provider->id) {
            abort(403, 'Unauthorized');
        }

        $notes = $request->input('completion_notes', 'Job completed successfully.');

        // Use the existing JobService
        $this->jobService->completeJob($job, $provider->id, $notes);

        return back()->with('success', 'Job marked as Completed! Waiting for customer verification/payment.');
    }
}