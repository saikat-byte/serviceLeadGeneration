<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreServiceRequest;
use App\Http\Resources\Api\V1\ServiceRequestResource;
use App\Models\ServiceRequest;
use App\Services\ServiceRequestService;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;

class ServiceRequestController extends Controller
{
    public function __construct(
        protected ServiceRequestService $serviceRequestService,
        protected LeadService $leadService
    ) {}

    public function store(StoreServiceRequest $request): JsonResponse
    {
        // 1. Create raw request
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $request->user()->id,
            ...$request->validated()
        ]);

        // 2. Submit via Service (Transitions to SUBMITTED)
        $this->serviceRequestService->submit($serviceRequest, $request->user()->id);

        // 3. Qualify & Trigger Matching via LeadService
        $this->serviceRequestService->qualify($serviceRequest, $request->user()->id);
        $this->leadService->createFromRequest($serviceRequest);

        // 4. Return formatted response
        $serviceRequest->load(['service', 'serviceVariant']);
        
        return response()->json([
            'success' => true,
            'message' => 'Service request submitted and matching started.',
            'data'    => new ServiceRequestResource($serviceRequest)
        ], 201);
    }
}