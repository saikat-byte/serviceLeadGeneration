<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\ServiceRequest;
use App\Enums\LeadStatus;
use App\Enums\MatchStatus;
use App\Events\LeadCreated;
use App\Events\LeadMatched;
use App\Events\LeadDistributed;
use Illuminate\Support\Facades\DB;

class LeadService
{
    public function __construct(
        protected MatchingService $matchingService
    ) {}

    /**
     * Create a qualified lead from a service request and trigger matching.
     */
    public function createFromRequest(ServiceRequest $serviceRequest): Lead
    {
        return DB::transaction(function () use ($serviceRequest) {
            
            // 'open' bad diye LeadStatus::CREATED Enum use kora holo
            $lead = Lead::create([
                'service_request_id' => $serviceRequest->id,
                'status'             => LeadStatus::CREATED,
            ]);

            LeadCreated::dispatch($lead);

            // Transition strictly state machine onujayi kora holo
            $lead->transitionState(
                newState: LeadStatus::QUALIFIED,
                eventName: 'LeadQualified',
                reason: 'Lead automatically qualified from service request.'
            );

            $this->processMatching($lead);

            return $lead;
        });
    }

    /**
     * Run matching algorithm and distribute lead.
     */
    public function processMatching(Lead $lead): void
    {
        $lead->transitionState(
            newState: LeadStatus::MATCHING,
            eventName: 'LeadMatchingStarted',
            reason: 'Matching engine searching for suitable providers.'
        );

        $scoredProviders = $this->matchingService->findAndRankProviders($lead);

        if ($scoredProviders->isEmpty()) {
            $lead->transitionState(
                newState: LeadStatus::UNFULFILLED,
                eventName: 'LeadUnfulfilled',
                reason: 'No suitable verified provider found in this area.'
            );
            return;
        }

        // Create match records
        $matchedProviderIds = $this->matchingService->createMatches($lead, $scoredProviders);
        
        LeadMatched::dispatch($lead, $matchedProviderIds);

        // Distribute Lead to Matched Providers
        $this->distribute($lead);
    }

    /**
     * Distribute lead to offered providers.
     */
    public function distribute(Lead $lead): void
    {
        DB::transaction(function () use ($lead) {
            $lead->transitionState(
                newState: LeadStatus::DISTRIBUTED,
                eventName: 'LeadDistributed',
                reason: 'Lead sent to shortlisted providers.'
            );

            $lead->update([
                'distributed_at' => now(),
                'expires_at'     => now()->addMinutes(30), // Configurable via ServiceDefinition
            ]);

            // Update individual match records to OFFERED
            $lead->matches()->each(function ($match) {
                $match->transitionState(
                    newState: MatchStatus::OFFERED, // Needs MatchStatus enum
                    eventName: 'MatchOffered',
                    reason: 'Opportunity offered to provider.'
                );
                $match->update(['offered_at' => now()]);
            });

            LeadDistributed::dispatch($lead);
        });
    }
}