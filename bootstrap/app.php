<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Enforce cookie encryption on all cookies
        $middleware->encryptCookies(except: []);

        // Redirect unauthenticated guests to admin login
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        // Register Route Middleware Aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Custom 403 Forbidden handler for Spatie UnauthorizedException
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'User does not have the necessary permissions.',
                    'required_permissions' => $e->getRequiredPermissions(),
                ], 403);
            }

            $required = $e->getRequiredPermissions() ?: $e->getRequiredRoles();
            $permissionList = !empty($required) ? implode(', ', $required) : 'restricted action';

            return response()->view('errors.403', [
                'exception' => $e,
                'message' => "Access Denied: Your account does not possess the required permission [{$permissionList}] to access this resource.",
            ], 403);
        });
    })->create();
