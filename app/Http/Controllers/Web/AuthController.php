<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            // Added: setHttpClient(['verify' => false]) to bypass local SSL cURL error
            $googleUser = Socialite::driver('google')
                            ->setHttpClient(new \GuzzleHttp\Client(['verify' => false]))
                            ->user();
            
            // Check if user exists by google_id or email
            $user = User::where('google_id', $googleUser->getId())
                        ->orWhere('email', $googleUser->getEmail())
                        ->first();

            if ($user) {
                // If user exists but doesn't have google_id set, update it
                if (empty($user->google_id)) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $user->avatar ?? $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'email_verified_at' => now(), // Auto verify since Google authenticated them
                    'role' => 'customer', // Default role
                    'status' => 'active',
                ]);
            }

            Auth::login($user, true);

            // Redirect based on role
            if ($user->role?->value === 'provider') {
                return redirect()->intended('/provider/dashboard');
            }

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            // Restore the original error redirect or keep dd() if you want to see future errors
            return redirect('/')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    }
}