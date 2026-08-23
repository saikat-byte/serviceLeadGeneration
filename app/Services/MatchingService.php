<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\MatchRecord;
use App\Models\User;
use App\Enums\MatchStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MatchingService
{
    /**
     * Find and rank the best matching providers for a lead.
     */
    public function findAndRankProviders(Lead $lead, int $limit = 5): Collection
    {
        $request = $lead->serviceRequest;
        $serviceId = $request->service_id;
        $location = $request->location;

        // 1. Find providers who offer this service and are active
        $query = User::query()
            ->where('role', 'provider')
            ->where('status', 'active')
            ->whereHas('providerProfile', function ($q) {
                $q->whereIn('availability_status', ['available', 'busy']);
            })
            ->whereHas('providerServices', function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId)
                  ->where('status', 'approved');
            });

        // 2. Filter by Location/Service Area if location exists
        if ($location && $location->city) {
            $query->whereHas('providerServiceAreas', function ($q) use ($location) {
                // Using case-insensitive or direct match for city
                $q->where('city', 'LIKE', '%' . trim($location->city) . '%');
            });
        }

        $potentialProviders = $query->with('providerProfile')->get();

        // Fallback: If strict city match fails in local testing, grab active providers offering the service
        if ($potentialProviders->isEmpty()) {
            $potentialProviders = User::query()
                ->where('role', 'provider')
                ->where('status', 'active')
                ->whereHas('providerServices', function ($q) use ($serviceId) {
                    $q->where('service_id', $serviceId)
                      ->where('status', 'approved');
                })
                ->with('providerProfile')
                ->get();
        }

        // 3. Score and Rank Providers
        $scoredProviders = $potentialProviders->map(function ($provider) {
            $profile = $provider->providerProfile;

            $ratingScore = $profile ? ($profile->rating_average / 5) * 40 : 20; 
            $responseScore = $profile ? ($profile->response_rate / 100) * 30 : 15; 
            $locationScore = 30; 

            $totalScore = round($ratingScore + $responseScore + $locationScore, 2);

            return [
                'provider' => $provider,
                'match_score' => $totalScore,
                'location_score' => $locationScore,
                'rating_score' => $ratingScore,
            ];
        })->sortByDesc('match_score')->take($limit);

        return $scoredProviders;
    }

    /**
     * Create match records in database.
     */
    public function createMatches(Lead $lead, Collection $scoredProviders): array
    {
        $createdProviderIds = [];
        $rank = 1;

        foreach ($scoredProviders as $item) {
            $provider = $item['provider'];

            MatchRecord::create([
                'lead_id'            => $lead->id,
                'provider_id'        => $provider->id,
                'status'             => MatchStatus::CREATED,
                'match_score'        => $item['match_score'],
                'location_score'     => $item['location_score'],
                'trust_score'        => $item['rating_score'],
                'rank'               => $rank++,
            ]);

            $createdProviderIds[] = $provider->id;
        }

        return $createdProviderIds;
    }
}