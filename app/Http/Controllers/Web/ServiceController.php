<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    // Batch 17: Notun Index Method for Search & Filtering
    public function index(Request $request)
    {
        $query = Service::where('is_active', true)->with('category');

        // 1. Keyword Search (Name or Description)
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // 2. Category Filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Fetch paginated results & preserve query string for pagination links
        $services = $query->paginate(12)->withQueryString();

        // Fetch categories for the sidebar filter
        $categories = ServiceCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function show($slug)
    {
        $service = Service::where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'category.services' => function($q) use ($slug) {
                    // Fetch related services in the same category
                    $q->where('is_active', true)->where('slug', '!=', $slug)->take(3);
                },
                'variants' => function($q) {
                    $q->where('is_active', true);
                },
                'definition',
                'providerServices' => function($q) {
                    // Fetch providers offering this service
                    $q->where('status', 'active')->with('provider');
                }
            ])
            ->firstOrFail();

        // Fetch reviews specifically for this service via bookings
        $reviews = \App\Models\Review::with(['reviewer'])
            ->whereHas('booking', function($query) use ($service) {
                $query->where('service_id', $service->id);
            })
            ->where('rating', '>=', 4)
            ->latest()
            ->take(4)
            ->get();

        $locations = Auth::check() ? Auth::user()->locations : collect();

        return view('services.show', compact('service', 'locations', 'reviews'));
    }
}
