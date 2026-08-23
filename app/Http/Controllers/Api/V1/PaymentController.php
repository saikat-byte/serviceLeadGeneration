<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    /**
     * Customer initiates a payment.
     */
    public function initiate(Booking $booking, Request $request): JsonResponse
    {
        $customer = Auth::user();

        if ($booking->customer_id != $customer->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $gateway = $request->input('gateway', 'stripe'); // Mock gateway
        $amount = $booking->final_amount ?? $booking->estimated_amount;

        $payment = $this->paymentService->initiatePayment($booking, $amount, $gateway);

        return response()->json([
            'success' => true,
            'message' => 'Payment initiated successfully.',
            'data'    => $payment
        ]);
    }

    /**
     * Webhook/Mock endpoint to confirm a successful payment.
     */
    public function confirm(Payment $payment): JsonResponse
    {
        $customer = Auth::user();

        if ($payment->customer_id != $customer->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Mock a successful gateway reference transaction ID
        $reference = 'txn_mock_' . rand(100000, 999999);

        // This handles Payment update, Transaction creation, and Commission earning!
        $this->paymentService->confirmPayment($payment, $reference);

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed, transaction recorded, and commission calculated!',
        ]);
    }
}