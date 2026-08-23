<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\User;
use App\Models\Interest;
use App\Enums\LeadStatus;
use App\Enums\MatchStatus;
use App\Enums\InterestStatus;
use App\Enums\InterestActorType;
use App\Events\ProviderExpressedInterest;
use App\Events\CustomerSelectedProvider;
use Illuminate\Support\Facades\DB;
use Exception;

class InterestService
{
    public function __construct(
        protected ConnectionService $connectionService
    ) {}

    /**
     * Provider accepts the lead opportunity.
     */
    public function recordProviderInterest(Lead $lead, User $provider): Interest
    {
        return DB::transaction(function () use ($lead, $provider) {
            $match = $lead->matches()->where('provider_id', $provider->id)->firstOrFail();

            if ($match->status->value !== MatchStatus::OFFERED->value) {
                throw new Exception("Opportunity is not in OFFERED state.");
            }

            // 1. Update Match Record
            $match->transitionState(
                newState: MatchStatus::ACCEPTED,
                eventName: 'MatchAccepted',
                actorId: $provider->id,
                reason: 'Provider accepted the lead.'
            );
            $match->update(['responded_at' => now()]);

            // 2. Create Interest Record
            $interest = Interest::create([
                'lead_id'      => $lead->id,
                'provider_id'  => $provider->id,
                'actor_type'   => InterestActorType::PROVIDER,
                'status'       => InterestStatus::ACTIVE,
                'expressed_at' => now(),
            ]);

            // 3. Update Lead Status
            $currentLeadState = $lead->status->value;
            if (in_array($currentLeadState, [LeadStatus::DISTRIBUTED->value, LeadStatus::RESPONDING->value])) {
                $lead->transitionState(
                    newState: LeadStatus::INTERESTED,
                    eventName: 'LeadInterested',
                    actorId: $provider->id,
                    reason: 'At least one provider expressed interest.'
                );
            }

            ProviderExpressedInterest::dispatch($lead, $provider);

            return $interest;
        });
    }

    /**
     * Customer selects a provider from the interested list.
     */
    public function customerSelectsProvider(Lead $lead, User $customer, User $provider): void
    {
        DB::transaction(function () use ($lead, $customer, $provider) {
            
            if ($lead->serviceRequest->customer_id !== $customer->id) {
                throw new Exception("Unauthorized customer selection.");
            }

            // 1. Mark Lead as SELECTED
            $lead->transitionState(
                newState: LeadStatus::SELECTED,
                eventName: 'CustomerSelected',
                actorId: $customer->id,
                reason: 'Customer selected a provider for the job.'
            );

            // 2. Update Match Records (Winner vs Losers)
            $lead->matches()->each(function ($match) use ($provider, $customer) {
                if ($match->provider_id === $provider->id) {
                    $match->transitionState(MatchStatus::SELECTED, 'ProviderSelected', $customer->id);
                } else {
                    // Only transition to NOT_SELECTED if they had accepted
                    if ($match->status->value === MatchStatus::ACCEPTED->value) {
                        $match->transitionState(MatchStatus::NOT_SELECTED, 'ProviderNotSelected', $customer->id);
                    }
                }
            });

            // 3. Dispatch Event
            CustomerSelectedProvider::dispatch($lead, $customer, $provider);

            // 4. Trigger Connection Creation
            $this->connectionService->establish($lead, $customer, $provider);
        });
    }
}