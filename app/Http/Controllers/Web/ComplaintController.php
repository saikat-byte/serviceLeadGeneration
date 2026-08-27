<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Complaint;
use App\Enums\ComplaintType;
use App\Enums\ComplaintStatus;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index()
    {
        $complaints = Complaint::where('complainant_id', auth()->id())
            ->with(['booking.serviceRequest.service', 'againstUser'])
            ->latest()
            ->get();

        return view('complaints.index', compact('complaints'));
    }

    public function create(Booking $booking)
    {
        // Check jeno sudhu booking er sathe jukto user e complaint korte pare
        if (auth()->id() !== $booking->customer_id && auth()->id() !== $booking->provider_id) {
            abort(403, 'Unauthorized action.');
        }

        return view('complaints.create', [
            'booking' => $booking,
            'types' => ComplaintType::cases()
        ]);
    }

    public function store(Request $request, Booking $booking)
    {
        if (auth()->id() !== $booking->customer_id && auth()->id() !== $booking->provider_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'type' => 'required|string',
            'description' => 'required|string|max:1000',
        ]);

        $isCustomer = auth()->id() === $booking->customer_id;

        Complaint::create([
            'booking_id' => $booking->id,
            'complainant_id' => auth()->id(),
            'against_user_id' => $isCustomer ? $booking->provider_id : $booking->customer_id,
            'type' => $validated['type'],
            'description' => $validated['description'],
            'status' => ComplaintStatus::CREATED,
            // Automatically grabbing the first default priority to avoid guessing Enum case names
            'priority' => \App\Enums\ComplaintPriority::cases()[0] ?? null, 
        ]);

        return redirect()->route('complaints.index')->with('success', 'Your complaint has been submitted. Our support team will review it shortly.');
    }
}