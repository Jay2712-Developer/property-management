<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach hardened security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Prevent Clickjacking - permit only same-origin framing (modals/previews)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Prevent MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Referrer Privacy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 4. Cross-Site Scripting Filter Protection
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 5. Restrict device capabilities (Camera/Mic restricted, Geolocation restricted to self)
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(self)');

        return $response;
    }
}
