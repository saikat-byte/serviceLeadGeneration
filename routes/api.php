<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ServiceRequestController;
use App\Http\Controllers\Api\V1\InterestController;
use App\Http\Controllers\Api\V1\SettlementController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\ComplaintController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProviderLeadController;
use App\Http\Controllers\Api\V1\ServiceJobController;
use App\Http\Controllers\Api\V1\BookingController;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    
    // Customer Routes
    Route::prefix('customer')->group(function () {
        Route::post('/service-requests', [ServiceRequestController::class, 'store']);
        Route::post('/leads/{lead}/select-provider', [InterestController::class, 'selectProvider']);
        
        // Create Booking from Connection
        Route::post('/connections/{connection}/book', [BookingController::class, 'store']);
        
        // Payment Workflow (Moved to Customer group)
        Route::post('/bookings/{booking}/pay', [PaymentController::class, 'initiate']);
        Route::post('/payments/{payment}/confirm', [PaymentController::class, 'confirm']);

        // Review & Rating Workflow
        Route::post('/bookings/{booking}/reviews', [ReviewController::class, 'store']);
    });

    // Provider Routes
    Route::prefix('provider')->group(function () {
        Route::get('/leads', [ProviderLeadController::class, 'index']);
        Route::post('/leads/{lead}/accept', [InterestController::class, 'acceptLead']);
        
        // Job Execution Routes
        Route::post('/jobs/{job}/start', [ServiceJobController::class, 'start']);
        Route::post('/jobs/{job}/complete', [ServiceJobController::class, 'complete']);
    });

    // Admin / Settlement Routes
    Route::post('/settlements/{settlement}/payout', [SettlementController::class, 'payout']);

    // 🟢 Complaint & Dispute Workflow Routes
    Route::get('/complaints', [ComplaintController::class, 'index']);
    Route::post('/complaints', [ComplaintController::class, 'store']);
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show']);

});