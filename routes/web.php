<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\CustomerDashboardController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\CustomerServiceRequestController;
use App\Http\Controllers\Web\ProviderDashboardController;
use App\Http\Controllers\Web\ProviderJobController;
use App\Http\Controllers\Web\CustomerBookingController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// --- ADD THIS LOGIN ROUTE CATCHER ---
Route::get('/login', function () {
    return redirect('/')->with('error', 'Please log in to access this page.');
})->name('login');
// ------------------------------------

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    
    // Customer Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
    Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
    
    // Service Requests & Connections
    Route::post('/service-requests', [CustomerServiceRequestController::class, 'store'])->name('service-requests.store');
    Route::get('/service-requests/{serviceRequest}', [CustomerServiceRequestController::class, 'show'])->name('service-requests.show');
    Route::post('/service-requests/{serviceRequest}/book/{provider}', [CustomerServiceRequestController::class, 'book'])->name('service-requests.book');

    // Customer Bookings (Payment & Review)
    Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/pay', [CustomerBookingController::class, 'pay'])->name('bookings.pay');
    Route::post('/bookings/{booking}/review', [CustomerBookingController::class, 'review'])->name('bookings.review');

    // Provider Dashboard & Jobs
    Route::get('/provider/dashboard', [ProviderDashboardController::class, 'index'])->name('provider.dashboard');
    Route::post('/provider/leads/{lead}/interest', [ProviderDashboardController::class, 'expressInterest'])->name('provider.leads.interest');
    Route::post('/provider/jobs/{job}/start', [ProviderJobController::class, 'start'])->name('provider.jobs.start');
    Route::post('/provider/jobs/{job}/complete', [ProviderJobController::class, 'complete'])->name('provider.jobs.complete');
});

// ==========================================
// DEVELOPER BYPASS LOGIN (For Local Testing Only)
// ==========================================
if (app()->environment('local')) {
    Route::get('/dev/login/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        auth()->login($user);
        
        if ($user->role?->value === 'provider') {
            return redirect()->route('provider.dashboard')->with('success', 'Logged in as Provider: ' . $user->name);
        }
        
        return redirect()->route('dashboard')->with('success', 'Logged in as Customer: ' . $user->name);
    });
}