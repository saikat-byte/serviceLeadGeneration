<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        
        // Enum theke value ber kora jate string er sathe match korano jay
        $userRole = $user->role instanceof \BackedEnum ? $user->role->value : $user->role;

        // Admin der sob jaygay access ache
        if ($userRole === 'admin' || $user->hasRole('super_admin')) {
            return $next($request);
        }

        // Jodi user er role pathano role er sathe na mele
        if ($userRole !== $role) {
            // Sothik dashboard e thele pathiye dao
            if ($userRole === 'provider') {
                return redirect()->route('provider.dashboard')->with('error', 'Access denied. You have been redirected to your Provider dashboard.');
            }
            return redirect()->route('dashboard')->with('error', 'Access denied. You have been redirected to your Customer dashboard.');
        }

        return $next($request);
    }
}