<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Transaction;
use App\Enums\PaymentStatus;
use App\Enums\BookingStatus;
use App\Enums\TransactionStatus;
use App\Events\PaymentCompleted;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(
        protected CommissionService $commissionService,
        protected SettlementService $settlementService 
    ) {}

    public function initiatePayment(Booking $booking, float $amount, string $gateway): Payment
    {
        return DB::transaction(function () use ($booking, $amount, $gateway) {
            $payment = Payment::create([
                'customer_id' => $booking->customer_id,
                'booking_id'  => $booking->id,
                'amount'      => $amount,
                'gateway'     => $gateway,
                'status'      => PaymentStatus::INITIATED,
            ]);

            $payment->transitionState(
                newState: PaymentStatus::PENDING,
                eventName: 'PaymentInitiated',
                reason: 'Payment requested from customer via ' . $gateway
            );

            $booking->transitionState(BookingStatus::PAYMENT_PENDING, 'BookingPaymentPending');

            return $payment;
        });
    }

    public function confirmPayment(Payment $payment, string $gatewayReference): void
    {
        DB::transaction(function () use ($payment, $gatewayReference) {
            
            $payment->update(['gateway_reference' => $gatewayReference, 'paid_at' => now()]);
            $payment->transitionState(
                newState: PaymentStatus::PAID,
                eventName: 'PaymentConfirmed',
                reason: 'Gateway confirmed the payment.'
            );

            $booking = $payment->booking;

            $transaction = Transaction::create([
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'type'       => 'customer_payment',
                'amount'     => $payment->amount,
                'status'     => TransactionStatus::COMPLETED,
                'reference'  => $gatewayReference,
            ]);

            $booking->transitionState(BookingStatus::PAID, 'BookingPaid');

            $commission = $this->commissionService->calculateAndEarn($booking, $transaction);

            $this->settlementService->createForCommission($commission);

            PaymentCompleted::dispatch($payment);
        });
    }
}