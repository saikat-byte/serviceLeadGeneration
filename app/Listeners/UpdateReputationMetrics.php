<?php

namespace App\Listeners;

use App\Events\ReviewPublished;
use App\Events\ReviewRemoved;
use App\Models\ProviderProfile;
use App\Services\ReviewService;
use App\Services\TrustScoreService;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateReputationMetrics implements ShouldQueue
{
    public function __construct(
        protected ReviewService $reviewService,
        protected TrustScoreService $trustScoreService
    ) {}

    /**
     * Handle both Published and Removed review events
     */
    public function handle(ReviewPublished|ReviewRemoved $event): void
    {
        $review = $event->review;

        // Check if the reviewee is a provider (as customers might also get reviews, but we only calculate rating for providers currently)
        $isRevieweeProvider = ProviderProfile::where('user_id', $review->reviewee_id)->exists();

        if ($isRevieweeProvider) {
            // 1. Recalculate basic provider rating average
            $this->reviewService->recalculateProviderRating($review->reviewee_id);

            // 2. Trigger Trust Score recalculation from Batch 8
            if (method_exists($this->trustScoreService, 'recalculateScore')) {
                $this->trustScoreService->recalculateScore($review->reviewee_id);
            } elseif (method_exists($this->trustScoreService, 'calculateTrustScore')) {
                $this->trustScoreService->calculateTrustScore($review->reviewee_id);
            }
        }
    }
}