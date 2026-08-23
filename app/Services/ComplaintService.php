<?php
namespace App\Services;

use App\Models\Booking;
use App\Models\Complaint;
use App\Models\User;
use App\Enums\ComplaintStatus;
use App\Events\ComplaintResolved;
use Illuminate\Support\Facades\DB;

class ComplaintService
{
    public function __construct(protected TrustScoreService $trustScoreService) {}

    public function fileComplaint(User $complainant, string $category, string $description, ?Booking $booking = null, ?User $againstUser = null): Complaint
    {
        return DB::transaction(function () use ($complainant, $category, $description, $booking, $againstUser) {
            
            $complaint = Complaint::create([
                'booking_id'      => $booking?->id,
                'complainant_id'  => $complainant->id,
                'against_user_id' => $againstUser?->id,
                'category'        => $category,
                'description'     => $description,
                'status'          => ComplaintStatus::CREATED,
            ]);

            $complaint->transitionState(
                newState: ComplaintStatus::ACKNOWLEDGED,
                eventName: 'ComplaintAcknowledged',
                reason: 'System received the complaint.'
            );

            return $complaint;
        });
    }

    public function resolveComplaint(Complaint $complaint, string $resolutionDetails, int $adminId): void
    {
        DB::transaction(function () use ($complaint, $resolutionDetails, $adminId) {
            
            $complaint->update([
                'resolution'  => $resolutionDetails,
                'resolved_at' => now(),
            ]);

            $complaint->transitionState(
                newState: ComplaintStatus::RESOLVED,
                eventName: 'ComplaintResolved',
                actorId: $adminId,
                reason: 'Admin resolved the complaint.'
            );

            ComplaintResolved::dispatch($complaint);

            // Recalculate Trust Score for the user against whom the complaint was filed
            if ($complaint->against_user_id) {
                $againstUser = User::find($complaint->against_user_id);
                $this->trustScoreService->recalculateFor($againstUser);
            }
        });
    }

public function createComplaint(\App\Models\Booking $booking, int $complainantId, array $data): \App\Models\Complaint
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($booking, $complainantId, $data) {
            
            $isCustomer = $booking->customer_id === $complainantId;
            $againstUserId = $isCustomer ? $booking->provider_id : $booking->customer_id;

            $complaint = \App\Models\Complaint::create([
                'booking_id'      => $booking->id,
                'complainant_id'  => $complainantId,
                'against_user_id' => $againstUserId,
                'category'        => $data['category'],
                'description'     => $data['description'],
                'status'          => 'created',
            ]);

            return $complaint;
        });
    }
}