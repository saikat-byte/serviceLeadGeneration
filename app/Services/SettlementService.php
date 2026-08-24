<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Settlement;
use App\Enums\SettlementStatus;
use App\Enums\CommissionStatus;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    /**
     * Create a settlement record for a provider from an earned commission.
     */
    public function createForCommission(Commission $commission): Settlement
    {
        return DB::transaction(function () use ($commission) {
            
            $grossAmount = $commission->base_amount;
            $platformFee = $commission->amount;
            $netAmount = $grossAmount - $platformFee; 

            // Strict Enum usage added
            $settlement = Settlement::create([
                'provider_id'   => $commission->provider_id,
                'commission_id' => $commission->id,
                'gross_amount'  => $grossAmount,
                'platform_fee'  => $platformFee,
                'net_amount'    => $netAmount,
                'currency'      => 'INR',
                'status'        => SettlementStatus::PENDING,
            ]);

            // Mark commission as settled using proper Enum and State Transition
            $commission->transitionState(
                newState: CommissionStatus::SETTLED, 
                eventName: 'CommissionSettled', 
                reason: 'Settlement generated for provider payout.'
            );

            return $settlement;
        });
    }

    /**
     * Mark settlement as paid/settled to the provider.
     */
    public function processPayout(Settlement $settlement, string $payoutReference): void
    {
        DB::transaction(function () use ($settlement, $payoutReference) {
            $settlement->update([
                'payout_reference' => $payoutReference,
                'settled_at'       => now(),
            ]);

            // State Machine Transition instead of raw update
            $settlement->transitionState(
                newState: SettlementStatus::SETTLED,
                eventName: 'SettlementPaid',
                reason: 'Payout processed and transferred to provider account.'
            );
        });
    }
}