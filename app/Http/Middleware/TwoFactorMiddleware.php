<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is authenticated and has 2FA enabled
        if ($user && $user->hasTwoFactorEnabled()) {
            // Ensure they have passed 2FA verification in their current session
            if (!$request->session()->get('2fa.verified')) {
                // Allow logout or 2FA challenge routes if reached
                if ($request->routeIs('admin.two-factor') || $request->routeIs('logout')) {
                    return $next($request);
                }

                // Preserve login.id for the challenge, log out to ensure session security
                $request->session()->put('login.id', $user->id);
                auth()->logout();

                return redirect()->route('admin.two-factor')
                    ->with('warning', 'Please complete Two-Factor Authentication to access this area.');
            }
        }

        return $next($request);
    }
}
