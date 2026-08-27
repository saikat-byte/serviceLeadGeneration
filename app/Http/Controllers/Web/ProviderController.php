<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    public function index(Request $request)
    {
        // 1. Base Query: Only users with 'provider' role and an existing provider profile
        $query = User::where('role', 'provider')
            ->whereHas('providerProfile')
            ->with(['providerProfile', 'providerServices.service']);

        // 2. Fetch Providers safely ordered by completed jobs and rating
        // Note: Using ProviderProfile safely to order
        $providers = $query->join('provider_profiles', 'users.id', '=', 'provider_profiles.user_id')
            ->orderBy('provider_profiles.completed_jobs_count', 'desc')
            ->orderBy('provider_profiles.rating_average', 'desc')
            ->select('users.*') // Prevent ID clash
            ->paginate(12);

        return view('providers.index', compact('providers'));
    }

    public function show(User $user)
    {
        // 1. Security Check: Ensure the requested user is actually a provider
        $roleCheck = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;
        if ($roleCheck !== 'provider' || !$user->providerProfile) {
            abort(404, 'Provider not found.');
        }

        // 2. Load necessary public-safe relationships
        $provider = $user->load([
            'providerProfile',
            'providerServices' => function($q) {
                // Only show approved/active services
                $q->where('status', 'active')->with('service.category');
            },
            'providerSkills.skill',
            'providerServiceAreas',
            // Load verified verification if exists
            'verifications' => function($q) {
                $q->where('status', 'verified')->where('expires_at', '>', now());
            }
        ]);

        // 3. Load public-safe reviews where this provider is the reviewee (via booking)
        $reviews = \App\Models\Review::with(['reviewer'])
            ->where('reviewee_id', $provider->id)
            ->whereIn('status', ['published', 'submitted']) // Do not show removed/flagged
            ->latest()
            ->take(10)
            ->get();

        return view('providers.show', compact('provider', 'reviews'));
    }
}