<?php

use App\Livewire\Admin\Profile\TwoFactorSettings;
use App\Livewire\Auth\AdminLogin;
use App\Livewire\Auth\AdminTwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin Guest / Authentication Routes (Outside auth middleware)
Route::get('/login', AdminLogin::class)->middleware('guest')->name('login');

Route::prefix('admin')->name('admin.')->middleware('guest')->group(function () {
    Route::get('/login', AdminLogin::class)->name('login');
    Route::get('/two-factor-challenge', AdminTwoFactor::class)->name('two-factor');
});

// Protected Admin Portal Routes
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth', 'admin', '2fa'])
    ->group(function () {

        // Admin Dashboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Admin Profile 2FA Management
        Route::get('/profile/2fa', TwoFactorSettings::class)->name('profile.2fa');

        // Role & Permission Management (Permission: manage_roles)
        Route::get('/roles', \App\Livewire\Admin\ManageRoles::class)->middleware('can:manage_roles')->name('roles');

        // User Management (Permission: manage_roles)
        Route::get('/users', \App\Livewire\Admin\ManageUsers::class)->middleware('can:manage_roles')->name('users');

        // Admin Logout
        Route::post('/logout', function () {
            Auth::logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('admin.login');
        })->name('logout');
    });

// Fallback logout route alias
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect()->route('admin.login');
})->middleware('auth')->name('logout');
