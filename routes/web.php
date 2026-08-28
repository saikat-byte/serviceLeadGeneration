<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ComplaintController;
use App\Http\Controllers\Web\CustomerBookingController;
use App\Http\Controllers\Web\CustomerDashboardController;
use App\Http\Controllers\Web\CustomerServiceRequestController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LocationController;
use App\Http\Controllers\Web\ProfileController;
use App\Http\Controllers\Web\ProviderController;
use App\Http\Controllers\Web\ProviderDashboardController;
use App\Http\Controllers\Web\ProviderJobController;
use App\Http\Controllers\Web\ProviderOnboardingController;
use App\Http\Controllers\Web\ServiceCategoryController;
use App\Http\Controllers\Web\ServiceController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/services/category/{slug}', [ServiceCategoryController::class, 'show'])->name('categories.show');
Route::get('/services/{slug}', [ServiceController::class, 'show'])->name('services.show');

// Batch 19: Public Provider Directory & Profiles
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
Route::get('/providers/{user}', [ProviderController::class, 'show'])->name('providers.show');
Route::view('/how-it-works', 'pages.how-it-works')->name('how-it-works');

Route::get('/login', function () {
    return redirect('/')->with('error', 'Please log in to access this page.');
})->name('login');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->group(function () {
    
    // ==========================================
    // CUSTOMER ONLY ROUTES
    // ==========================================
    Route::middleware([RoleMiddleware::class.':customer'])->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        

        
        // Customer Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('customer.profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('customer.profile.update');
        
        // Customer Complaints
        Route::get('/complaints', [ComplaintController::class, 'index'])->name('customer.complaints.index');
        Route::get('/bookings/{booking}/complaints/create', [ComplaintController::class, 'create'])->name('customer.complaints.create');
        Route::post('/bookings/{booking}/complaints', [ComplaintController::class, 'store'])->name('customer.complaints.store');

        // Customer Other Routes
        Route::get('/locations/create', [LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
        
        Route::post('/service-requests', [CustomerServiceRequestController::class, 'store'])->name('service-requests.store');
        Route::get('/service-requests/{serviceRequest}', [CustomerServiceRequestController::class, 'show'])->name('service-requests.show');
        Route::post('/service-requests/{serviceRequest}/book/{provider}', [CustomerServiceRequestController::class, 'book'])->name('service-requests.book');

        Route::get('/bookings/{booking}', [CustomerBookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/pay', [CustomerBookingController::class, 'pay'])->name('bookings.pay');
        Route::post('/bookings/{booking}/review', [CustomerBookingController::class, 'review'])->name('bookings.review');
    });

    // ==========================================
    // PROVIDER ONLY ROUTES (Prefix: /provider)
    // ==========================================
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class.':provider'])->prefix('provider')->name('provider.')->group(function () {
        
        Route::get('/dashboard', [ProviderDashboardController::class, 'index'])->name('dashboard');
        
        // ---- BATCH 20.1: PROVIDER ONBOARDING ROUTES ----
        Route::prefix('onboarding')->name('onboarding.')->group(function () {
            Route::get('/', [ProviderOnboardingController::class, 'index'])->name('index');
            
            Route::get('/profile', [ProviderOnboardingController::class, 'stepProfile'])->name('profile');
            Route::post('/profile', [ProviderOnboardingController::class, 'saveProfile'])->name('profile.save');
            
            Route::get('/services', [ProviderOnboardingController::class, 'stepServices'])->name('services');
            Route::post('/services', [ProviderOnboardingController::class, 'saveServices'])->name('services.save');
            
            Route::get('/skills', [ProviderOnboardingController::class, 'stepSkills'])->name('skills');
            Route::post('/skills', [ProviderOnboardingController::class, 'saveSkills'])->name('skills.save');
            
            Route::get('/areas', [ProviderOnboardingController::class, 'stepAreas'])->name('areas');
            Route::post('/areas', [ProviderOnboardingController::class, 'saveAreas'])->name('areas.save');
            
            Route::get('/availability', [ProviderOnboardingController::class, 'stepAvailability'])->name('availability');
            Route::post('/availability', [ProviderOnboardingController::class, 'saveAvailability'])->name('availability.save');
            
            Route::get('/review', [ProviderOnboardingController::class, 'stepReview'])->name('review');
        });
        // ------------------------------------------------

        // Provider Profile (/provider/profile)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
        
        // Provider Complaints (/provider/complaints)
        Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
        Route::get('/bookings/{booking}/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
        Route::post('/bookings/{booking}/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
        
        // Provider Other Routes
        Route::post('/leads/{lead}/interest', [ProviderDashboardController::class, 'expressInterest'])->name('leads.interest');
        Route::post('/jobs/{job}/start', [ProviderJobController::class, 'start'])->name('jobs.start');
        Route::post('/jobs/{job}/complete', [ProviderJobController::class, 'complete'])->name('jobs.complete');
    });

});

// ==========================================
// DEVELOPER BYPASS LOGIN (For Local Testing Only)
// ==========================================
if (app()->environment('local')) {
    Route::get('/dev/login/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        auth()->login($user);
        
        $userRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;
        
        if ($userRole === 'provider') {
            return redirect()->route('provider.dashboard')->with('success', 'Logged in as Provider: ' . $user->name);
        }
        
        return redirect()->route('dashboard')->with('success', 'Logged in as Customer: ' . $user->name);
    });
}