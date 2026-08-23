<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreReviewRequest;
use App\Models\Booking;
use App\Services\ReviewService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(protected ReviewService $reviewService) {}

    public function store(StoreReviewRequest $request, Booking $booking): JsonResponse
    {
        try {
            $customer = Auth::user();

            $review = $this->reviewService->storeReview($booking, $customer->id, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Review submitted and published successfully.',
                'data'    => $review,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}