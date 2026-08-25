<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Review;
use App\Models\ProviderProfile;
use App\Enums\ReviewStatus;
use Illuminate\Support\Facades\DB;
use Exception;

class ReviewService
{
    /**
     * Store a review for a completed booking with eligibility checks.
     */
    public function storeReview(Booking $booking, int $reviewerId, array $data): Review
    {
        return DB::transaction(function () use ($booking, $reviewerId, $data) {
            
            // Rule 1: Check if job is completed
            if (!in_array($booking->status->value, ['work_completed', 'paid', 'closed'])) {
                throw new Exception('Reviews can only be submitted for completed jobs.');
            }

            // Rule 2: Check participant roles
            $isCustomer = $booking->customer_id === $reviewerId;
            $isProvider = $booking->provider_id === $reviewerId;

            if (!$isCustomer && !$isProvider) {
                throw new Exception('Unauthorized. Only booking participants can submit a review.');
            }

            $revieweeId = $isCustomer ? $booking->provider_id : $booking->customer_id;

            // Rule 3: Check duplicate review from the same actor
            $existingReview = Review::where('booking_id', $booking->id)
                ->where('reviewer_id', $reviewerId)
                ->exists();

            if ($existingReview) {
                throw new Exception('You have already submitted a review for this booking.');
            }

            // 1. Create Review
            $review = Review::create([
                'booking_id'  => $booking->id,
                'reviewer_id' => $reviewerId,
                'reviewee_id' => $revieweeId,
                'rating'      => $data['rating'],
                'comment'     => $data['comment'] ?? null,
                'status'      => ReviewStatus::SUBMITTED->value,
            ]);

            // Transition State (Auto-publish) - This will trigger ReviewPublished event
            $review->transitionState(
                newState: ReviewStatus::PUBLISHED->value,
                eventName: 'App\Events\ReviewPublished',
                actorId: $reviewerId,
                reason: 'Review automatically published.'
            );

            // Removed direct calculation here. Listener will handle it.

            return $review;
        });
    }

    /**
     * Recalculate and update provider average rating.
     */
    public function recalculateProviderRating(int $providerId): void
    {
        // Average is calculated based on PUBLISHED reviews only
        $averageRating = Review::where('reviewee_id', $providerId)
            ->where('status', ReviewStatus::PUBLISHED->value)
            ->avg('rating');

        ProviderProfile::where('user_id', $providerId)->update([
            'rating_average' => round((float) $averageRating, 2),
        ]);
    }
}