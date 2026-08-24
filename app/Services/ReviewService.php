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

            // Rule 2: Check if reviewer is the booking customer
            if ($booking->customer_id !== $reviewerId) {
                throw new Exception('Unauthorized. Only the booking customer can review this job.');
            }

            // Rule 3: Check duplicate review
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
                'reviewee_id' => $booking->provider_id,
                'rating'      => $data['rating'],
                'comment'     => $data['comment'] ?? null,
                'status'      => ReviewStatus::SUBMITTED,
            ]);

            // Transition State (Duplicate removed)
            $review->transitionState(
                newState: ReviewStatus::PUBLISHED,
                eventName: 'ReviewPublished',
                actorId: $reviewerId,
                reason: 'Review automatically published.'
            );

            // 2. Recalculate Provider's Average Rating
            $this->recalculateProviderRating($booking->provider_id);

            return $review;
        });
    }

    /**
     * Recalculate and update provider average rating.
     */
    protected function recalculateProviderRating(int $providerId): void
    {
        $averageRating = Review::where('reviewee_id', $providerId)
            ->where('status', ReviewStatus::PUBLISHED)
            ->avg('rating');

        ProviderProfile::where('user_id', $providerId)->update([
            'rating_average' => round($averageRating, 2),
        ]);
    }
}