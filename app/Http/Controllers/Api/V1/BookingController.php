<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Connection;
use App\Models\Booking;
use App\Services\JobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function __construct(protected JobService $jobService) {}

    /**
     * Customer confirms a booking from an established connection.
     */
    public function store(Request $request, Connection $connection): JsonResponse
    {
        $request->validate([
            'scheduled_at'     => 'required|date|after:now',
            'estimated_amount' => 'required|numeric|min:0',
        ]);

        $customer = Auth::user();

        // 🛑 Security Check: Ensure only the connection owner can book
        if ($connection->customer_id !== $customer->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        return DB::transaction(function () use ($request, $connection, $customer) {
            
            // 1. Create the Booking
            $booking = Booking::create([
                'customer_id'        => $connection->customer_id,
                'provider_id'        => $connection->provider_id,
                'service_id'         => $connection->lead->serviceRequest->service_id,
                'service_request_id' => $connection->lead->serviceRequest->id,
                'lead_id'            => $connection->lead_id,
                'connection_id'      => $connection->id,
                'status'             => 'confirmed', // Initial Booking Status
                'scheduled_at'       => $request->scheduled_at,
                'estimated_amount'   => $request->estimated_amount,
                'currency'           => 'INR',
            ]);

            // 2. Automatically generate the ServiceJob using your existing JobService
            $this->jobService->createForBooking($booking, $customer->id);

            // 3. Update Connection state to 'active' since work is scheduled
            $connection->transitionState('active', 'BookingCreated', $customer->id, 'Customer confirmed the booking.');

            return response()->json([
                'success' => true,
                'message' => 'Booking confirmed and Service Job generated successfully.',
                'data'    => [
                    'booking_id' => $booking->id,
                    'status'     => $booking->status,
                ]
            ], 201);
        });
    }
}