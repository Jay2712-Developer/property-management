<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Admin Login - TISHA Real Estate')]
class AdminLogin extends Component
{
    #[Rule('required|email', message: 'Please provide a valid email address.')]
    public string $email = '';

    #[Rule('required|string', message: 'Password is required.')]
    public string $password = '';

    public bool $remember = false;

    public function mount(): void
    {
        if (Auth::check()) {
            $this->redirect(route('admin.dashboard'), navigate: true);
        }
    }

    public function login()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (!$user || !Hash::check($this->password, $user->password)) {
            $this->addError('email', 'These credentials do not match our records.');
            return;
        }

        // Check if user has Two-Factor Authentication enabled
        if ($user->hasTwoFactorEnabled()) {
            session()->put([
                'login.id' => $user->id,
                'login.remember' => $this->remember,
            ]);

            return $this->redirect(route('admin.two-factor'), navigate: true);
        }

        // Standard Login
        Auth::login($user, $this->remember);
        session()->regenerate();

        return $this->redirect(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.admin-login');
    }
}
