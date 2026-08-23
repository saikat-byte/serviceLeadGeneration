<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Commission;
use App\Enums\CommissionStatus;
use App\Enums\CommissionModel;
use App\Events\CommissionEarned;
use Illuminate\Support\Facades\DB;

class CommissionService
{
    /**
     * Calculate and record platform commission based on Service Definition.
     */
    public function calculateAndEarn(Booking $booking, Transaction $transaction): Commission
    {
        return DB::transaction(function () use ($booking, $transaction) {
            
            // Look up the business rules for this specific service
            $serviceDefinition = $booking->service->definition;
            
            // Get the raw string or value from the revenue model enum safely
            $revenueModelValue = $serviceDefinition->revenue_model instanceof \UnitEnum 
                ? $serviceDefinition->revenue_model->value 
                : $serviceDefinition->revenue_model;
            
            $baseAmount = $transaction->amount;
            $commissionAmount = 0;
            $rate = null;

            // Calculate based on configured revenue model string value
            if ($revenueModelValue === 'commission' || $revenueModelValue === CommissionModel::COMMISSION->value) {
                $rate = $serviceDefinition->commission_rate; // e.g. 10.00 (%)
                $commissionAmount = ($baseAmount * $rate) / 100;
            } elseif ($revenueModelValue === 'lead_fee' || $revenueModelValue === CommissionModel::LEAD_FEE->value) {
                $commissionAmount = $serviceDefinition->fixed_lead_fee;
            }

            // 1. Create Commission Record (passing string or proper CommissionModel enum)
            $commission = Commission::create([
                'booking_id'     => $booking->id,
                'transaction_id' => $transaction->id,
                'provider_id'    => $booking->provider_id,
                'model'          => CommissionModel::tryFrom($revenueModelValue) ?? CommissionModel::COMMISSION,
                'base_amount'    => $baseAmount,
                'rate'           => $rate,
                'amount'         => $commissionAmount,
                'status'         => CommissionStatus::PENDING,
            ]);

            // 2. Transition to EARNED
            $commission->transitionState(
                newState: CommissionStatus::EARNED,
                eventName: 'CommissionEarned',
                reason: 'Commission calculated and earned from successful payment.'
            );
            $commission->update(['earned_at' => now()]);

            CommissionEarned::dispatch($commission);

            return $commission;
        });
    }
}