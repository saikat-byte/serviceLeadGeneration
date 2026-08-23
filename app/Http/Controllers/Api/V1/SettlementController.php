<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Settlement;
use App\Services\SettlementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function __construct(protected SettlementService $settlementService) {}

    /**
     * Mark a settlement as paid out to the provider.
     */
    public function payout(Request $request, Settlement $settlement): JsonResponse
    {
        $request->validate([
            'payout_reference' => 'required|string',
        ]);

        $this->settlementService->processPayout($settlement, $request->payout_reference);

        return response()->json([
            'success' => true,
            'message' => 'Provider settlement processed and marked as settled successfully.',
        ]);
    }
}