<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\ServiceJob;
use App\Services\InterestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderDashboardController extends Controller
{
    public function index()
    {
        $provider = Auth::user();

        // 1. Fetch available leads
        $leads = Lead::whereHas('matches', function ($query) use ($provider) {
                $query->where('provider_id', $provider->id)
                      ->whereIn('status', ['created', 'offered', 'not_selected']); 
            })
            ->with(['serviceRequest.service', 'serviceRequest.location'])
            ->whereIn('status', ['distributed', 'responding'])
            ->latest()
            ->get();

        // 2. Fetch Active Jobs (Bookings assigned to this provider)
        $activeJobs = ServiceJob::with(['booking.service', 'booking.customer', 'booking.connection.lead.serviceRequest.location'])
            ->whereHas('booking', function($query) use ($provider) {
                $query->where('provider_id', $provider->id);
            })
            ->whereNotIn('status', ['completed', 'verified', 'closed', 'cancelled'])
            ->latest()
            ->get();

        return view('provider.dashboard', compact('leads', 'activeJobs'));
    }

    public function expressInterest(Lead $lead, \App\Services\InterestService $interestService)
    {
        $provider = \Illuminate\Support\Facades\Auth::user();

        try {
            $interestService->expressInterest($lead, $provider->id);
            
            // --- NOTIFICATION LOGIC ---
            $customer = $lead->serviceRequest->customer;
            if ($customer) {
                $customer->notify(new \App\Notifications\ProviderInterestedNotification($lead, $provider));
            }
            // --------------------------

            return back()->with('success', 'You have expressed interest! Waiting for the customer to confirm.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
