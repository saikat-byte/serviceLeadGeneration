<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function show($slug)
    {
        // Fetch the service with its category and active variants
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'variants' => function($query) {
                $query->where('is_active', true);
            }])
            ->firstOrFail();

        // Fetch user locations if logged in (required for service request)
        $locations = Auth::check() ? Auth::user()->locations : collect();

        return view('services.show', compact('service', 'locations'));
    }
}