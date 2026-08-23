<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\User;
use App\Services\InterestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InterestController extends Controller
{
    public function __construct(protected InterestService $interestService) {}

    /**
     * Provider accepts the lead opportunity.
     */
    public function acceptLead(Lead $lead): JsonResponse
    {
        $provider = Auth::user();

        if ($provider->role->value !== 'provider') {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $this->interestService->recordProviderInterest($lead, $provider);

        return response()->json([
            'success' => true,
            'message' => 'Lead accepted successfully. Interest recorded.',
        ]);
    }

    /**
     * Customer selects a provider from the interested list.
     */
    public function selectProvider(Request $request, Lead $lead): JsonResponse
    {
        $request->validate([
            'provider_id' => 'required|exists:users,id',
        ]);

        $customer = Auth::user();
        $provider = User::findOrFail($request->provider_id);

        $this->interestService->customerSelectsProvider($lead, $customer, $provider);

        return response()->json([
            'success' => true,
            'message' => 'Provider selected successfully. Connection established.',
        ]);
    }
}