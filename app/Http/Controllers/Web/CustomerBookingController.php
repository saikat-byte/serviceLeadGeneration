<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\PaymentService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerBookingController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService,
        protected ReviewService $reviewService
    ) {}

    public function show(Booking $booking)
    {
        $user = Auth::user();
        if ($booking->customer_id !== $user->id) {
            abort(403);
        }

        $booking->load(['service', 'provider.providerProfile', 'serviceJob', 'payment', 'review']);

        return view('customer.bookings.show', compact('booking'));
    }

    public function pay(Booking $booking, Request $request)
    {
        $user = Auth::user();
        if ($booking->customer_id !== $user->id) {
            abort(403);
        }

        // Mock payment process using existing PaymentService
        $amount = $booking->final_amount ?? $booking->estimated_amount;
        $payment = $this->paymentService->initiatePayment($booking, $amount, 'mock_gateway');
        
        // Auto confirm for demo purposes
        $this->paymentService->confirmPayment($payment, 'txn_mock_' . rand(10000, 99999));

        return back()->with('success', 'Payment successful! Thank you.');
    }

    public function review(Booking $booking, Request $request)
    {
        $user = Auth::user();
        if ($booking->customer_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $this->reviewService->createReview(
            $booking,
            $user->id,
            $booking->provider_id,
            $validated['rating'],
            $validated['comment']
        );

        return back()->with('success', 'Thank you for your feedback!');
    }
}