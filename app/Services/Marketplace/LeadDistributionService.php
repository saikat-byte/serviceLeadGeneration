<?php

namespace App\Services\Marketplace;

use App\Models\Lead;
use App\Notifications\LeadOfferedNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeadDistributionService
{
    public function distribute(Lead $lead): bool
    {
        try {
            DB::beginTransaction();

            // Idempotency & Safety Check
            $leadStatus = $lead->status instanceof \BackedEnum ? $lead->status->value : $lead->status;
            if ($leadStatus !== 'matching') {
                Log::warning("Lead #{$lead->id} is not in matching state. Current state: {$leadStatus}. Cannot distribute.");
                DB::rollBack();
                return false;
            }

            // Fallback limits if settings are missing
            $maxProviders = config('marketplace.matching.max_providers', 5);
            $expiryHours = config('marketplace.matching.lead_expiry_hours', 24);
            
            // Adjust expiry based on urgency
            $urgency = $lead->serviceRequest->urgency ?? 'normal';
            if ($urgency === 'urgent') $expiryHours = 12;
            if ($urgency === 'emergency') $expiryHours = 4;

            // Fetch top ranked created matches
            $matches = $lead->matches()
                ->where('status', 'created')
                ->orderBy('rank')
                ->take($maxProviders)
                ->get();

            if ($matches->isEmpty()) {
                $lead->update(['status' => 'unfulfilled']);
                Log::info("Lead #{$lead->id} has no valid matches for distribution. Marked as unfulfilled.");
                DB::commit();
                return false;
            }

            // Update Lead state
            $lead->update([
                'status' => 'distributed',
                'distributed_at' => now(),
                'expires_at' => now()->addHours($expiryHours),
            ]);

            // Distribute to Providers
            foreach ($matches as $match) {
                // Update Match state
                $match->update([
                    'status' => 'offered',
                    'offered_at' => now(),
                ]);

                // Send Notification Safely
                try {
                    if ($match->provider) {
                        $match->provider->notify(new LeadOfferedNotification($lead, $match));
                    }
                } catch (\Exception $emailException) {
                    // Do not rollback marketplace workflow just because SMTP failed on shared hosting
                    Log::error("Failed to send notification to Provider #{$match->provider_id} for Lead #{$lead->id}: " . $emailException->getMessage());
                }
            }

            DB::commit();
            Log::info("Lead #{$lead->id} successfully distributed to {$matches->count()} providers.");
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Lead distribution failed for Lead #{$lead->id}: " . $e->getMessage());
            return false;
        }
    }
}