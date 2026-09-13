<?php

namespace Tests\Feature;

use App\Livewire\Admin\Profile\TwoFactorSettings;
use App\Livewire\Auth\AdminLogin;
use App\Livewire\Auth\AdminTwoFactor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class AdminAuthAndTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
    }

    public function test_user_without_2fa_can_authenticate_and_redirect_to_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@tisha.com',
            'password' => Hash::make('password'),
            'two_factor_secret' => null,
        ]);

        Livewire::test(AdminLogin::class)
            ->set('email', 'admin@tisha.com')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_with_2fa_is_redirected_to_two_factor_challenge(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'email' => 'admin-2fa@tisha.com',
            'password' => Hash::make('password'),
            'two_factor_secret' => $secret,
        ]);

        Livewire::test(AdminLogin::class)
            ->set('email', 'admin-2fa@tisha.com')
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.two-factor'));

        $this->assertGuest();
        $this->assertEquals($user->id, session('login.id'));
    }

    public function test_two_factor_challenge_authenticates_with_valid_totp(): void
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();

        $user = User::factory()->create([
            'two_factor_secret' => $secret,
        ]);

        // Put login.id in session as if coming from AdminLogin
        session()->put('login.id', $user->id);

        $validCode = $google2fa->getCurrentOtp($secret);

        Livewire::test(AdminTwoFactor::class)
            ->set('code', $validCode)
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNull(session('login.id'));
    }

    public function test_two_factor_challenge_authenticates_with_recovery_code(): void
    {
        $recoveryCodes = ['recovery-code-12345', 'recovery-code-67890'];

        $user = User::factory()->create([
            'two_factor_secret' => 'SECRETKEY123456',
            'two_factor_recovery_codes' => json_encode($recoveryCodes),
        ]);

        session()->put('login.id', $user->id);

        Livewire::test(AdminTwoFactor::class)
            ->call('toggleRecoveryMode')
            ->set('recovery_code', 'recovery-code-12345')
            ->call('verify')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);

        // Verify recovery code was consumed
        $user->refresh();
        $remaining = json_decode($user->two_factor_recovery_codes, true);
        $this->assertNotContains('recovery-code-12345', $remaining);
        $this->assertContains('recovery-code-67890', $remaining);
    }

    public function test_admin_can_enable_confirm_and_disable_2fa_in_profile(): void
    {
        $user = User::factory()->create([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ]);

        $this->actingAs($user);

        $google2fa = new Google2FA();

        // 1. Enable 2FA & inspect generated QR code & secret
        $component = Livewire::test(TwoFactorSettings::class)
            ->call('enableTwoFactor')
            ->assertSet('showingQrCode', true);

        $secretKey = $component->get('secretKey');
        $this->assertNotEmpty($secretKey);
        $this->assertNotEmpty($component->get('qrCodeSvg'));

        // 2. Confirm 2FA with current valid OTP
        $validCode = $google2fa->getCurrentOtp($secretKey);

        $component->set('confirmationCode', $validCode)
            ->call('confirmTwoFactor')
            ->assertHasNoErrors()
            ->assertSet('showingRecoveryCodes', true);

        $user->refresh();
        $this->assertTrue($user->hasTwoFactorEnabled());
        $this->assertEquals($secretKey, $user->two_factor_secret);
        $this->assertNotEmpty($user->two_factor_recovery_codes);

        // 3. Disable 2FA
        $component->call('disableTwoFactor');
        $user->refresh();
        $this->assertFalse($user->hasTwoFactorEnabled());
        $this->assertNull($user->two_factor_secret);
    }
}
