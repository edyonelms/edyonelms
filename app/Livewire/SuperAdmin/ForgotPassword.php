<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use App\Services\OtpMailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $step = 'email'; // email | otp | password
    public $email = '';
    public $otp = '';
    public $password = '';
    public $password_confirmation = '';
    public $showPassword = false;
    public $showConfirmPassword = false;
    public $resendCooldown = 0;
    public $canResend = true;

    protected function rules()
    {
        if ($this->step === 'email') {
            return ['email' => 'required|email'];
        }

        if ($this->step === 'otp') {
            return ['otp' => 'required|digits:6'];
        }

        if ($this->step === 'password') {
            return [
                'password'              => ['required', 'min:8', 'max:16', 'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'],
                'password_confirmation' => 'required|same:password',
            ];
        }

        return [];
    }

    protected function messages()
    {
        return [
            'email.required'                 => 'The email field is required.',
            'email.email'                    => 'Please enter a valid email address.',
            'otp.required'                   => 'Please enter the OTP.',
            'otp.digits'                     => 'OTP must be exactly 6 digits.',
            'password.required'              => 'The password field is required.',
            'password.min'                   => 'Password must be at least 8 characters.',
            'password.max'                   => 'Password must not exceed 16 characters.',
            'password.regex'                 => 'Password must contain at least 1 number and 1 special character.',
            'password_confirmation.required' => 'Please confirm your password.',
            'password_confirmation.same'     => 'Passwords do not match.',
        ];
    }

    // ─── Step 1: Email ────────────────────────────────────────────────────────

    public function submitEmail()
    {
        $this->validate(['email' => 'required|email'], $this->messages());

        $user = User::where('email', $this->email)->where('role', 'super-admin')->first();

        if (!$user) {
            $this->addError('email', 'No super admin account found with this email address.');
            return;
        }

        OtpMailService::sendOtp($user, 'Super Admin Panel');

        $this->step = 'otp';
        $this->resendCooldown = 120;
        $this->canResend = false;
    }

    // ─── Step 2: OTP Verify ───────────────────────────────────────────────────

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|digits:6'], $this->messages());

        $user = User::where('email', $this->email)->where('role', 'super-admin')->first();

        if (!$user) {
            $this->addError('otp', 'Unable to verify. Please start over.');
            $this->step = 'email';
            return;
        }

        try {
            OtpMailService::verifyOtp($user, $this->otp);
        } catch (\Exception $e) {
            $this->addError('otp', $e->getMessage());
            return;
        }

        // Store verified state in cache so password reset step is protected
        Cache::put('super_admin_otp_verified_' . $this->email, true, 600);

        $this->step = 'password';
    }

    // ─── Resend OTP ───────────────────────────────────────────────────────────

    public function resendOtp()
    {
        $user = User::where('email', $this->email)->where('role', 'super-admin')->first();

        if (!$user) {
            $this->addError('otp', 'Unable to resend OTP. Please go back and try again.');
            return;
        }

        if (!OtpMailService::canResend($user)) {
            $otpCreatedAt = Carbon::parse($user->otp_expires_at)->subMinutes(2);
            $elapsed      = now()->diffInSeconds($otpCreatedAt);
            $remaining    = max(0, 120 - (int) $elapsed);
            $this->addError('otp', "Please wait {$remaining} seconds before resending.");
            return;
        }

        $this->otp = '';
        $this->resetErrorBag();

        OtpMailService::sendOtp($user, 'Super Admin Panel');

        $this->resendCooldown = 120;
        $this->canResend = false;
    }

    // ─── Cooldown Sync (called by Alpine countdown) ───────────────────────────

    public function checkCooldown()
    {
        $user = User::where('email', $this->email)->where('role', 'super-admin')->first();

        if (!$user || empty($user->otp_expires_at)) {
            $this->canResend      = true;
            $this->resendCooldown = 0;
            return;
        }

        $otpCreatedAt = Carbon::parse($user->otp_expires_at)->subMinutes(2);
        $elapsed      = now()->diffInSeconds($otpCreatedAt);
        $remaining    = 120 - (int) $elapsed;

        if ($remaining <= 0) {
            $this->canResend      = true;
            $this->resendCooldown = 0;
        } else {
            $this->canResend      = false;
            $this->resendCooldown = $remaining;
        }
    }

    // ─── Step 3: Reset Password ───────────────────────────────────────────────

    public function resetPassword()
    {
        $this->validate([
            'password'              => ['required', 'min:8', 'max:16', 'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*(),.?":{}|<>]).+$/'],
            'password_confirmation' => 'required|same:password',
        ], $this->messages());

        $verified = Cache::get('super_admin_otp_verified_' . $this->email);

        if (!$verified) {
            $this->addError('password', 'Session expired. Please start the process again.');
            $this->step = 'email';
            return;
        }

        $user = User::where('email', $this->email)->where('role', 'super-admin')->first();

        if (!$user) {
            $this->addError('password', 'User not found. Please start the process again.');
            $this->step = 'email';
            return;
        }

        $user->update(['password' => Hash::make($this->password)]);

        Cache::forget('super_admin_otp_verified_' . $this->email);

        return redirect()->route('super-admin.login')
            ->with('success', 'Password reset successfully. Please login with your new password.');
    }

    // ─── Toggles ──────────────────────────────────────────────────────────────

    public function togglePassword()
    {
        $this->showPassword = !$this->showPassword;
    }

    public function toggleConfirmPassword()
    {
        $this->showConfirmPassword = !$this->showConfirmPassword;
    }

    public function render()
    {
        return view('livewire.super-admin.forgot-password')
            ->layout('components.layouts.fullscreen');
    }
}
