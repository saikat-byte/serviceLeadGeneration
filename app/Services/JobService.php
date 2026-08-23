<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ServiceJob;
use App\Enums\ServiceJobStatus;
use App\Enums\BookingStatus;
use App\Events\JobStarted;
use App\Events\JobCompleted;
use Illuminate\Support\Facades\DB;

class JobService
{
    /**
     * Initialize a job for a confirmed booking.
     */
    public function createForBooking(Booking $booking, int $actorId): ServiceJob
    {
        return DB::transaction(function () use ($booking, $actorId) {
            $job = ServiceJob::create([
                'booking_id' => $booking->id,
                'status'     => ServiceJobStatus::CREATED,
            ]);

            $job->transitionState(
                newState: ServiceJobStatus::CREATED,
                eventName: 'JobCreated',
                actorId: $actorId,
                reason: 'Operational job generated for confirmed booking.'
            );

            return $job;
        });
    }

    /**
     * Mark the job as started.
     */
    public function startWork(ServiceJob $job, int $providerId): void
    {
        DB::transaction(function () use ($job, $providerId) {
            $job->transitionState(
                newState: ServiceJobStatus::STARTED,
                eventName: 'JobStarted',
                actorId: $providerId,
                reason: 'Provider started the service at location.'
            );
            $job->update(['started_at' => now()]);

            // Sync with Booking
            $job->booking->transitionState(BookingStatus::WORK_STARTED, 'BookingWorkStarted', $providerId);

            JobStarted::dispatch($job);
        });
    }

    /**
     * Mark the job as completed with final value.
     */
    public function completeWork(ServiceJob $job, int $providerId, array $completionData): void
    {
        DB::transaction(function () use ($job, $providerId, $completionData) {
            $job->transitionState(
                newState: ServiceJobStatus::COMPLETED,
                eventName: 'JobCompleted',
                actorId: $providerId,
                reason: 'Provider marked the service as completed.'
            );
            
            $job->update([
                'completed_at'        => now(),
                'final_service_value' => $completionData['final_value'],
                'completion_notes'    => $completionData['notes'] ?? null,
                'completion_evidence' => $completionData['evidence'] ?? null,
            ]);

            // Sync with Booking
            $booking = $job->booking;
            $booking->update(['final_amount' => $completionData['final_value']]);
            $booking->transitionState(BookingStatus::WORK_COMPLETED, 'BookingWorkCompleted', $providerId);

            JobCompleted::dispatch($job);
        });
    }
}