<?php

namespace App\Services;

use App\Models\Connection;
use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\LeadStatus;
use App\Events\BookingCreated;
use App\Events\BookingConfirmed;
use Illuminate\Support\Facades\DB;
use Exception;

class BookingService
{
    public function __construct(
        protected JobService $jobService
    ) {}

    /**
     * Create a booking from an established connection.
     */
    public function createFromConnection(Connection $connection, array $bookingDetails, int $actorId): Booking
    {
        return DB::transaction(function () use ($connection, $bookingDetails, $actorId) {
            
            $lead = $connection->lead;
            $request = $lead->serviceRequest;

            // 1. Create Booking Record
            $booking = Booking::create([
                'customer_id'        => $connection->customer_id,
                'provider_id'        => $connection->provider_id,
                'service_id'         => $request->service_id,
                'service_request_id' => $request->id,
                'lead_id'            => $lead->id,
                'connection_id'      => $connection->id,
                'status'             => BookingStatus::PENDING,
                'scheduled_at'       => $bookingDetails['scheduled_at'] ?? $request->preferred_at,
                'estimated_amount'   => $bookingDetails['estimated_amount'] ?? null,
                'notes'              => $bookingDetails['notes'] ?? null,
            ]);

            // 2. Transition State for Audit Trail
            $booking->transitionState(
                newState: BookingStatus::PENDING,
                eventName: 'BookingCreated',
                actorId: $actorId,
                reason: 'Booking initiated after connection established.'
            );

            BookingCreated::dispatch($booking);

            return $booking;
        });
    }

    /**
     * Confirm the booking and trigger the Job creation.
     */
    public function confirm(Booking $booking, int $actorId): void
    {
        DB::transaction(function () use ($booking, $actorId) {
            
            $booking->transitionState(
                newState: BookingStatus::CONFIRMED,
                eventName: 'BookingConfirmed',
                actorId: $actorId,
                reason: 'Booking terms agreed and confirmed.'
            );

            // MISSING LOGIC ADDED: Lead ke finally CONVERTED mark kora holo
            $lead = $booking->lead;
            if ($lead && $lead->status->value !== LeadStatus::CONVERTED->value) {
                $lead->transitionState(
                    newState: LeadStatus::CONVERTED,
                    eventName: 'LeadConverted',
                    actorId: $actorId,
                    reason: 'Booking confirmed, lead successfully converted.'
                );
                $lead->update(['converted_at' => now()]);
            }

            // Once confirmed, a Service Job (field operation) is generated
            $this->jobService->createForBooking($booking, $actorId);

            BookingConfirmed::dispatch($booking);
        });
    }


    /**
    * Cancel the booking and record cancellation details.
    */
    public function cancel(Booking $booking, int $actorId, string $reason, float $fee = 0.00): void
    {
        DB::transaction(function () use ($booking, $actorId, $reason, $fee) {
            
            // 1. Transition Booking state to cancelled
            $booking->transitionState(
                newState: BookingStatus::CANCELLED,
                eventName: 'BookingCancelled',
                actorId: $actorId,
                reason: $reason
            );

            // 2. Create Cancellation Record
            \App\Models\BookingCancellation::create([
                'booking_id'   => $booking->id,
                'cancelled_by' => $actorId,
                'reason'       => $reason,
                'fee'          => $fee,
            ]);

            // 3. If ServiceJob exists and not closed/completed, cancel it too
            if ($booking->serviceJob && !in_array($booking->serviceJob->status->value, ['completed', 'closed', 'cancelled'])) {
                $booking->serviceJob->transitionState(
                    newState: \App\Enums\ServiceJobStatus::CANCELLED,
                    eventName: 'JobCancelled',
                    actorId: $actorId,
                    reason: 'Associated booking was cancelled: ' . $reason
                );
            }
        });
    }
}