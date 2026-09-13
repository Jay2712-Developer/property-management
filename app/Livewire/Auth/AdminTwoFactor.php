<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('components.layouts.app')]
#[Title('Two-Factor Authentication - TISHA Real Estate')]
class AdminTwoFactor extends Component
{
    #[Rule('nullable|string')]
    public string $code = '';

    #[Rule('nullable|string')]
    public string $recovery_code = '';

    public bool $usingRecoveryCode = false;

    public ?User $user = null;

    public function mount(): void
    {
        if (!session()->has('login.id')) {
            $this->redirect(route('admin.login'), navigate: true);
            return;
        }

        $this->user = User::find(session()->get('login.id'));

        if (!$this->user || !$this->user->hasTwoFactorEnabled()) {
            $this->redirect(route('admin.login'), navigate: true);
        }
    }

    public function toggleRecoveryMode(): void
    {
        $this->usingRecoveryCode = !$this->usingRecoveryCode;
        $this->resetErrorBag();
        $this->code = '';
        $this->recovery_code = '';
    }

    public function verify()
    {
        if ($this->usingRecoveryCode) {
            $this->validate([
                'recovery_code' => 'required|string',
            ], [
                'recovery_code.required' => 'Please enter an emergency recovery code.',
            ]);

            $recoveryCodes = json_decode($this->user->two_factor_recovery_codes ?? '[]', true) ?: [];

            if (!in_array($this->recovery_code, $recoveryCodes, true)) {
                $this->addError('recovery_code', 'The provided recovery code is invalid or has already been used.');
                return;
            }

            // Remove used recovery code
            $updatedCodes = array_values(array_diff($recoveryCodes, [$this->recovery_code]));
            $this->user->two_factor_recovery_codes = json_encode($updatedCodes);
            $this->user->save();
        } else {
            $this->validate([
                'code' => 'required|string|size:6',
            ], [
                'code.required' => 'Please enter the 6-digit authentication code.',
                'code.size' => 'The authentication code must be exactly 6 digits.',
            ]);

            $google2fa = new Google2FA();
            $valid = $google2fa->verifyKey($this->user->two_factor_secret, $this->code);

            if (!$valid) {
                $this->addError('code', 'The two-factor authentication code is invalid or expired.');
                return;
            }
        }

        // Login User
        Auth::login($this->user, session()->get('login.remember', false));
        session()->forget(['login.id', 'login.remember']);
        session()->regenerate();

        return $this->redirect(route('admin.dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.admin-two-factor');
    }
}
