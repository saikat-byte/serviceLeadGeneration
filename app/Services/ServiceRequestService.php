<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Enums\ServiceRequestStatus;
use Illuminate\Support\Facades\DB;

class ServiceRequestService
{
    /**
     * Submit a draft service request.
     */
    public function submit(ServiceRequest $request, int $actorId): ServiceRequest
    {
        $request->transitionState(
            newState: ServiceRequestStatus::SUBMITTED,
            eventName: 'ServiceRequestSubmitted',
            actorId: $actorId,
            reason: 'Customer submitted the requirement.'
        );

        // Here we can trigger validation logic or jobs
        // Event::dispatch(new ServiceRequestSubmitted($request));

        return $request;
    }

    /**
     * Qualify the request and generate a lead.
     */
    public function qualify(ServiceRequest $request, int $actorId): ServiceRequest
    {
        DB::transaction(function () use ($request, $actorId) {
            
            // 1. Transition the request
            $request->transitionState(
                newState: ServiceRequestStatus::QUALIFIED,
                eventName: 'ServiceRequestQualified',
                actorId: $actorId,
                reason: 'Platform validated all requirements.'
            );

            // 2. Create the Lead (Business Engine Logic)
            // This could also be offloaded to a LeadService
            $lead = $request->lead()->create([
                'status' => 'created',
                // other default fields...
            ]);

            // 3. Fire explicit Event for listeners (e.g., Matching Engine)
            // Event::dispatch(new ServiceRequestQualified($request, $lead));
        });

        return $request;
    }
}