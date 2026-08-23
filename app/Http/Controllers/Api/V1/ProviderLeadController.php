<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderLeadController extends Controller
{
    /**
     * Get list of leads offered to the authenticated provider.
     */
    public function index(Request $request): JsonResponse
    {
        $provider = Auth::user();

        // Fetch leads assigned to this specific provider
        $leads = Lead::whereHas('matches', function ($query) use ($provider) {
            $query->where('provider_id', $provider->id);
        })
        ->with([
            'serviceRequest.service', 
            'serviceRequest.location', 
            'matches' => function ($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            }
        ])
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data'    => $leads,
        ]);
    }
}