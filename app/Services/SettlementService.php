<?php

namespace App\Services;

use App\Models\Commission;
use App\Models\Settlement;
use Illuminate\Support\Facades\DB;

class SettlementService
{
    /**
     * Create a settlement record for a provider from an earned commission.
     */
    public function createForCommission(Commission $commission): Settlement
    {
        return DB::transaction(function () use ($commission) {
            
            // Gross amount is the total transaction amount or base amount
            $grossAmount = $commission->base_amount;
            $platformFee = $commission->amount; // Commission earned by platform
            $netAmount = $grossAmount - $platformFee; // Amount to pay to provider

            $settlement = Settlement::create([
                'provider_id'   => $commission->provider_id,
                'commission_id' => $commission->id,
                'gross_amount'  => $grossAmount,
                'platform_fee'  => $platformFee,
                'net_amount'    => $netAmount,
                'currency'      => 'INR',
                'status'        => 'pending',
            ]);

            // Mark commission as settled if needed or link it
            $commission->transitionState('settled', 'SettlementCreated', null, 'Settlement generated for provider payout.');

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
                'status'           => 'settled',
            ]);
        });
    }
}