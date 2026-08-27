<?php

namespace App\Services\Marketplace;

use App\Models\Lead;
use App\Models\Match as MatchRecord; // assuming the model is App\Models\Match or MatchRecord
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MatchingEngine
{
    public function process(ServiceRequest $serviceRequest): ?Lead
    {
        return DB::transaction(function () use ($serviceRequest) {
            
            Log::info("MatchingEngine started for ServiceRequest #{$serviceRequest->id}");

            // 1. Qualification
            if (!$this->isQualified($serviceRequest)) {
                Log::info("ServiceRequest #{$serviceRequest->id} failed qualification.");
                return null;
            }

            // 2. Lead Creation / Retrieval (Idempotent)
            $lead = Lead::updateOrCreate(
                ['service_request_id' => $serviceRequest->id],
                [
                    'status' => 'qualified',
                    // Inherit context if Lead model requires explicitly copying some data
                ]
            );

            // 3. Find Eligible Providers (Hard Filters)
            $providers = $this->findEligibleProviders($serviceRequest);

            if ($providers->isEmpty()) {
                $lead->update(['status' => 'unfulfilled']);
                Log::info("Lead #{$lead->id} has 0 eligible providers. Marked unfulfilled.");
                return $lead;
            }

            // 4. Scoring & Ranking
            $scoredMatches = $this->scoreAndRankProviders($providers, $serviceRequest);

            // 5. Persist Matches
            $this->persistMatches($scoredMatches, $lead);

            // 6. Update Lead State
            $lead->update(['status' => 'matching']); // Ready for distribution next batch
            
            Log::info("Lead #{$lead->id} matched with {$providers->count()} providers.");

            return $lead;
        });
    }

    private function isQualified(ServiceRequest $request): bool
    {
        if (!$request->service_id || !$request->location_id || !$request->customer_id) {
            return false;
        }

        // Must not be cancelled
        $status = $request->status instanceof \BackedEnum ? $request->status->value : $request->status;
        if (in_array($status, ['cancelled', 'expired'])) {
            return false;
        }

        return true;
    }

    private function findEligibleProviders(ServiceRequest $request)
    {
        $query = User::where('role', 'provider')
            ->where('status', 'active') // Assuming active user status
            ->whereHas('providerProfile')
            ->whereHas('providerServices', function ($q) use ($request) {
                // Hard Filter: Exact Service & Approved
                $q->where('service_id', $request->service_id)
                  ->whereIn('status', ['approved', 'active']);
            })
            ->whereHas('providerServiceAreas', function ($q) use ($request) {
                // Hard Filter: Location Match safely via requested location string relations
                // Assuming request->location holds city/locality details.
                $q->where('locality', $request->location->locality ?? '')
                  ->orWhere('city', $request->location->city ?? '');
            });

        // Hard Filter: Verification if required
        if ($request->service->definition && $request->service->definition->requires_provider_verification) {
            $query->whereHas('verifications', function ($q) {
                $q->where('status', 'verified')
                  ->where('expires_at', '>', now());
            });
        }

        // Eager load for scoring
        return $query->with([
            'providerProfile', 
            'providerServices' => function($q) use ($request) {
                $q->where('service_id', $request->service_id);
            }, 
            'providerServiceAreas'
        ])->get();
    }

    private function scoreAndRankProviders($providers, ServiceRequest $request): array
    {
        $matches = [];

        foreach ($providers as $provider) {
            
            // 1. Location Score (0-100)
            $locationScore = 70; // Base city match
            $providerArea = $provider->providerServiceAreas->first();
            if ($providerArea && $providerArea->locality === ($request->location->locality ?? '')) {
                $locationScore = 100; // Exact locality match
            }

            // 2. Availability Score (0-100)
            $availabilityScore = 100; // Simplified for this batch if preferred_at logic is loose

            // 3. Quality Score (0-100)
            $rating = $provider->providerProfile->rating_average ?? 0;
            $jobs = $provider->providerProfile->completed_jobs_count ?? 0;
            $qualityScore = ($rating / 5) * 100;
            if ($jobs > 10) $qualityScore = min(100, $qualityScore + 10);

            // 4. Price Score (0-100)
            $priceScore = 50; // Neutral default
            $startingPrice = $provider->providerServices->first()->starting_price ?? null;
            if ($startingPrice && $request->budget_max) {
                if ($startingPrice <= $request->budget_max) {
                    $priceScore = 100;
                } else {
                    $priceScore = max(0, 100 - (($startingPrice - $request->budget_max) / 10)); // Arbitrary safe decay
                }
            }

            // 5. Skill Score
            $skillScore = 100;

            // Total Score calculation based on weights
            $matchScore = ($locationScore * 0.30) 
                        + ($qualityScore * 0.30) 
                        + ($availabilityScore * 0.20) 
                        + ($priceScore * 0.10) 
                        + ($skillScore * 0.10);

            $matches[] = [
                'provider_id' => $provider->id,
                'match_score' => round($matchScore, 2),
                'location_score' => $locationScore,
                'availability_score' => $availabilityScore,
                'skill_score' => $skillScore,
                'trust_score' => round($qualityScore, 2), // Maps to trust/quality
                'price_score' => round($priceScore, 2),
                'rating_average' => $rating, // For sorting only
                'completed_jobs' => $jobs,   // For sorting only
            ];
        }

        // Rank them
        usort($matches, function ($a, $b) {
            if ($a['match_score'] == $b['match_score']) {
                if ($a['rating_average'] == $b['rating_average']) {
                    if ($a['completed_jobs'] == $b['completed_jobs']) {
                        return $a['provider_id'] <=> $b['provider_id']; // Deterministic
                    }
                    return $b['completed_jobs'] <=> $a['completed_jobs'];
                }
                return $b['rating_average'] <=> $a['rating_average'];
            }
            return $b['match_score'] <=> $a['match_score'];
        });

        // Assign rank
        foreach ($matches as $index => &$match) {
            $match['rank'] = $index + 1;
            // Clean up helper keys
            unset($match['rating_average'], $match['completed_jobs']);
        }

        return $matches;
    }

    private function persistMatches(array $matches, Lead $lead): void
    {
        foreach ($matches as $matchData) {
            $matchData['status'] = 'created';
            
            // Using updateOrCreate for Idempotency
            // Assumes App\Models\Match represents the matches table
            MatchRecord::updateOrCreate(
                [
                    'lead_id' => $lead->id,
                    'provider_id' => $matchData['provider_id'],
                ],
                $matchData
            );
        }
    }
}