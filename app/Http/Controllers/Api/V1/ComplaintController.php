<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreComplaintRequest;
use App\Models\Booking;
use App\Models\Complaint;
use App\Services\ComplaintService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function __construct(protected ComplaintService $complaintService) {}

    /**
     * List user's complaints.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $complaints = Complaint::where('complainant_id', $user->id)
            ->orWhere('against_user_id', $user->id)
            ->with(['booking', 'againstUser'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => $complaints,
        ]);
    }

    /**
     * Store a new complaint.
     */
    public function store(StoreComplaintRequest $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $booking = Booking::findOrFail($request->booking_id);

            // তোমার ComplaintService-এর সিগনেচার অনুযায়ী মেথড কল করা হচ্ছে
            $complaint = $this->complaintService->createComplaint(
                $booking, 
                $user->id, 
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Complaint submitted successfully and is under review.',
                'data'    => $complaint,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * View single complaint details.
     */
    public function show(Complaint $complaint): JsonResponse
    {
        $user = Auth::user();

        if ($complaint->complainant_id !== $user->id && $complaint->against_user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        $complaint->load(['booking', 'complainant', 'againstUser']);

        return response()->json([
            'success' => true,
            'data'    => $complaint,
        ]);
    }
}