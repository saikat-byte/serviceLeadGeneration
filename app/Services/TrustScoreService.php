<?php
namespace App\Services;

use App\Models\User;
use App\Models\Review;
use App\Models\TrustProfile;
use App\Models\ProviderProfile;
use App\Enums\ReviewStatus;
use Illuminate\Support\Facades\DB;

class TrustScoreService
{
    /**
     * Recalculate metrics and trust score for a user (Provider or Customer).
     */
    public function recalculateFor(User $user): void
    {
        DB::transaction(function () use ($user) {
            
            // 1. Calculate Average Rating from published reviews
            $averageRating = Review::where('reviewee_id', $user->id)
                ->where('status', ReviewStatus::PUBLISHED->value)
                ->avg('rating') ?? 0;

            // 2. Count Total Complaints against this user
            $complaintsCount = $user->complaintsAgainst()->count(); // Assuming relation exists

            // 3. Update Trust Profile
            $trustProfile = TrustProfile::firstOrCreate(['user_id' => $user->id]);
            $trustProfile->update([
                'rating_average' => round($averageRating, 2),
                'complaints_count' => $complaintsCount,
                // logic for response_rate, cancellation_rate can be added here
            ]);

            // 4. Sync with Provider Profile if user is a provider
            if ($user->role->value === 'provider' && $user->providerProfile) {
                $user->providerProfile->update([
                    'rating_average' => round($averageRating, 2),
                ]);
            }
        });
    }
}