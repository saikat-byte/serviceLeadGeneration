<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreServiceRequest;
use App\Models\Booking;
use App\Models\Interest;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Notifications\BookingConfirmedNotification;
use App\Services\InterestService;
use App\Services\JobService;
use App\Services\LeadService;
use App\Services\ServiceRequestService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerServiceRequestController extends Controller
{
    public function __construct(
        protected ServiceRequestService $serviceRequestService,
        protected LeadService $leadService
    ) {}

    public function store(StoreServiceRequest $request)
    {
        $serviceRequest = ServiceRequest::create([
            'customer_id' => $request->user()->id,
            ...$request->validated()
        ]);

        $this->serviceRequestService->submit($serviceRequest, $request->user()->id);
        $this->serviceRequestService->qualify($serviceRequest, $request->user()->id);
        $this->leadService->createFromRequest($serviceRequest);

        return redirect()->route('dashboard')->with('success', 'Service request submitted successfully! We are matching you with the best professionals.');
    }

    public function show(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        if ($serviceRequest->customer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $serviceRequest->load(['service', 'location', 'lead.interests.provider.providerProfile']);

        return view('customer.service-requests.show', compact('serviceRequest'));
    }

    public function book(ServiceRequest $serviceRequest, User $provider, InterestService $interestService, JobService $jobService)
    {
        $user = Auth::user();
        if ($serviceRequest->customer_id !== $user->id) {
            abort(403);
        }

        $lead = $serviceRequest->lead;
        if (!$lead) {
            return back()->with('error', 'Lead not found for this request.');
        }

        $interest = Interest::where('lead_id', $lead->id)->where('provider_id', $provider->id)->firstOrFail();

        return DB::transaction(function () use ($serviceRequest, $interest, $user, $interestService, $jobService) {
            
            // 1. Select Provider (Creates Connection)
            $connection = $interestService->selectProvider($interest, $user->id);

            // 2. Create Booking
            $booking = Booking::create([
                'customer_id'        => $connection->customer_id,
                'provider_id'        => $connection->provider_id,
                'service_id'         => $serviceRequest->service_id,
                'service_request_id' => $serviceRequest->id,
                'lead_id'            => $connection->lead_id,
                'connection_id'      => $connection->id,
                'status'             => 'confirmed',
                'scheduled_at'       => $serviceRequest->preferred_at ?? now()->addDays(1),
                'estimated_amount'   => $serviceRequest->budget_max ?? 500, // Fallback amount
                'currency'           => 'INR',
            ]);

            // 3. Generate Service Job
            $jobService->createForBooking($booking, $user->id);

            // 4. Update Connection State
            $connection->transitionState('active', 'BookingCreated', $user->id, 'Customer confirmed the booking.');

            $provider->notify(new BookingConfirmedNotification($booking, 'provider'));
            $user->notify(new BookingConfirmedNotification($booking, 'customer'));

            return redirect()->route('dashboard')->with('success', 'Booking confirmed successfully with ' . $connection->provider->name . '!');
        });
    }
}