<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Fetch recent service requests by this customer
        $serviceRequests = ServiceRequest::with('service')
            ->where('customer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Fetch recent bookings for this customer
        $bookings = Booking::with(['service', 'provider'])
            ->where('customer_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('serviceRequests', 'bookings'));
    }
}