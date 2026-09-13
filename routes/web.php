<?php

use App\Livewire\Admin\Profile\TwoFactorSettings;
use App\Livewire\Auth\AdminLogin;
use App\Livewire\Auth\AdminTwoFactor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Authentication Routes (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', AdminLogin::class)->name('admin.login');
    Route::get('/admin/two-factor-challenge', AdminTwoFactor::class)->name('admin.two-factor');
});

// Authenticated Admin Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/admin/profile/2fa', TwoFactorSettings::class)->name('admin.profile.2fa');

    Route::post('/admin/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');
});
