<?php

namespace App\Livewire\Admin\Profile;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Title;
use Livewire\Component;
use PragmaRX\Google2FA\Google2FA;

#[Layout('admin.layouts.app')]
#[Title('Two-Factor Security - TISHA Real Estate')]
class TwoFactorSettings extends Component
{
    public bool $showingQrCode = false;
    public bool $showingRecoveryCodes = false;

    public string $secretKey = '';
    public string $qrCodeSvg = '';
    public array $recoveryCodes = [];

    #[Rule('nullable|string|size:6')]
    public string $confirmationCode = '';

    public function mount(): void
    {
        $user = Auth::user();
        if ($user && $user->hasTwoFactorEnabled()) {
            $this->recoveryCodes = json_decode($user->two_factor_recovery_codes ?? '[]', true) ?: [];
        }
    }

    /**
     * Start the 2FA enablement flow: generate secret & QR code.
     */
    public function enableTwoFactor(): void
    {
        $google2fa = new Google2FA();
        $this->secretKey = $google2fa->generateSecretKey();

        // Build OTPAuth URL
        $appName = config('app.name', 'TISHA Real Estate');
        $userEmail = Auth::user()->email;
        $qrCodeUrl = $google2fa->getQRCodeUrl($appName, $userEmail, $this->secretKey);

        // Render QR Code as SVG using BaconQrCode
        $renderer = new ImageRenderer(
            new RendererStyle(200, 2),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $this->qrCodeSvg = $writer->writeString($qrCodeUrl);

        $this->showingQrCode = true;
        $this->confirmationCode = '';
        $this->resetErrorBag();
    }

    /**
     * Confirm the 6-digit code and permanently activate 2FA for the user.
     */
    public function confirmTwoFactor(): void
    {
        $this->validate([
            'confirmationCode' => 'required|string|size:6',
        ], [
            'confirmationCode.required' => 'Please enter the 6-digit code from your authenticator app.',
            'confirmationCode.size' => 'The confirmation code must be 6 digits.',
        ]);

        $google2fa = new Google2FA();
        $valid = $google2fa->verifyKey($this->secretKey, $this->confirmationCode);

        if (!$valid) {
            $this->addError('confirmationCode', 'The confirmation code was invalid. Please try again.');
            return;
        }

        // Generate 8 emergency recovery codes
        $codes = collect(range(1, 8))->map(function () {
            return Str::random(10) . '-' . Str::random(5);
        })->all();

        $user = Auth::user();
        $user->two_factor_secret = $this->secretKey;
        $user->two_factor_recovery_codes = json_encode($codes);
        $user->save();

        $this->recoveryCodes = $codes;
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = true;
        $this->secretKey = '';
        $this->confirmationCode = '';

        session()->put('2fa.verified', true);
        session()->flash('status', 'Two-Factor Authentication has been successfully enabled!');
    }

    /**
     * Regenerate new recovery codes.
     */
    public function regenerateRecoveryCodes(): void
    {
        $user = Auth::user();
        if (!$user->hasTwoFactorEnabled()) {
            return;
        }

        $codes = collect(range(1, 8))->map(function () {
            return Str::random(10) . '-' . Str::random(5);
        })->all();

        $user->two_factor_recovery_codes = json_encode($codes);
        $user->save();

        $this->recoveryCodes = $codes;
        $this->showingRecoveryCodes = true;

        session()->flash('status', 'New recovery codes have been generated.');
    }

    /**
     * Disable Two-Factor Authentication.
     */
    public function disableTwoFactor(): void
    {
        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->save();

        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->secretKey = '';
        $this->recoveryCodes = [];

        session()->flash('status', 'Two-Factor Authentication has been disabled.');
    }

    public function render()
    {
        return view('livewire.admin.profile.two-factor-settings', [
            'user' => Auth::user(),
        ]);
    }
}
