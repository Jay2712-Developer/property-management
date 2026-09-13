<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Allowed roles for accessing the TISHA Real Estate admin portal.
     *
     * @var list<string>
     */
    protected array $allowedRoles = [
        'Super Admin',
        'Admin',
        'Manager',
        'Agent',
        'Viewer',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->hasAnyRole($this->allowedRoles)) {
            abort(403, 'Unauthorized access to the admin portal.');
        }

        return $next($request);
    }
}
