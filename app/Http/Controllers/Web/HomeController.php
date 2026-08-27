<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\ProviderProfile;
use App\Models\Review;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Fetch active categories with active services (limit 4 for UI) AND actual total count
        $categories = ServiceCategory::where('is_active', true)
            ->withCount(['services' => function ($query) {
                $query->where('is_active', true);
            }])
            ->with(['services' => function ($query) {
                $query->where('is_active', true)->take(4);
            }])
            ->orderBy('sort_order')
            ->get();

        // 2. Fetch Top Professionals (Ensuring user exists)
        $topProviders = ProviderProfile::with('user')
            ->whereHas('user') // Ensure the user actually exists/is active based on existing logic
            ->where('rating_average', '>=', 4.0)
            ->orderBy('completed_jobs_count', 'desc')
            ->take(4)
            ->get();

        // 3. Fetch Top Customer Reviews (Checking status if applicable in existing schema)
        $reviews = Review::with(['reviewer', 'reviewee'])
            ->where('rating', '>=', 4)
            ->latest()
            ->take(3)
            ->get();

        return view('welcome', compact('categories', 'topProviders', 'reviews'));
    }
}