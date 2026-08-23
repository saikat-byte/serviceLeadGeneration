<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ServiceJob;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceJobController extends Controller
{
    public function __construct(protected JobService $jobService) {}

    /**
     * Provider starts the work.
     */
    public function start(ServiceJob $job): JsonResponse
    {
        $provider = Auth::user();

        // 🛑 Security Check (Assuming ServiceJob is related to Booking -> Provider)
        if ($job->booking->provider_id !== $provider->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Not your job.'], 403);
        }

        // Call your existing JobService
        $this->jobService->startWork($job, $provider->id);

        return response()->json([
            'success' => true,
            'message' => 'Job started successfully.',
        ]);
    }

    /**
     * Provider completes the work.
     */
    public function complete(Request $request, ServiceJob $job): JsonResponse
    {
        $request->validate([
            'final_value' => 'required|numeric|min:0',
            'notes'       => 'nullable|string',
            'evidence'    => 'nullable|string', // Could be an image URL
        ]);

        $provider = Auth::user();

        if ($job->booking->provider_id !== $provider->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Not your job.'], 403);
        }

        // Prepare completion data as expected by your JobService
        $completionData = [
            'final_value' => $request->final_value,
            'notes'       => $request->notes,
            'evidence'    => $request->evidence,
        ];

        // Call your existing JobService
        $this->jobService->completeWork($job, $provider->id, $completionData);

        return response()->json([
            'success' => true,
            'message' => 'Job completed successfully. Booking amount updated.',
        ]);
    }
}