<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function create(Request $request)
    {
        // Capture the previous URL so we can send the user back after adding the location
        $redirect = $request->query('redirect', route('dashboard'));
        
        return view('customer.locations.create', compact('redirect'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'label'       => 'required|string|max:255',
            'address'     => 'required|string|max:1000',
            'locality'    => 'required|string|max:255',
            'city'        => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
        ]);

        $user = Auth::user();
        
        // Make it default if it's their first location
        $isFirst = $user->locations()->count() === 0;

        $user->locations()->create([
            'label'       => $validated['label'],
            'address'     => $validated['address'],
            'locality'    => $validated['locality'],
            'city'        => $validated['city'],
            'postal_code' => $validated['postal_code'],
            'is_default'  => $isFirst,
        ]);

        // Redirect back to the service page or dashboard
        $redirectUrl = $request->input('redirect', route('dashboard'));
        
        return redirect($redirectUrl)->with('success', 'Location added successfully! You can now book the service.');
    }
}