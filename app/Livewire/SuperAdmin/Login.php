<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use App\Services\OtpMailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Login extends Component
{
    public string $step = 'credentials'; // 'credentials' | 'otp'

    public $email = '';
    public $password = '';
    public $otpCode = '';
    public $otpSentTo = '';

    public function login()
    {
        $this->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ], [
            'email.required'    => 'The email field is required.',
            'email.email'       => 'Must be a valid email address.',
            'password.required' => 'The password field is required.',
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Email does not exist.');
            return;
        }

        if (!Hash::check($this->password, $user->password)) {
            $this->addError('password', 'Incorrect password.');
            return;
        }

        if ($user->role !== 'super-admin') {
            $this->addError('email', 'You do not have super-admin access.');
            return;
        }

        OtpMailService::sendOtp($user, 'Super Admin');

        $this->otpSentTo = $user->email;
        $this->step = 'otp';
    }

    public function verifyOtp()
    {
        $this->validate([
            'otpCode' => 'required|digits:6',
        ], [
            'otpCode.required' => 'Please enter the OTP.',
            'otpCode.digits'   => 'OTP must be 6 digits.',
        ]);

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('otpCode', 'Session expired. Please login again.');
            $this->step = 'credentials';
            return;
        }

        try {
            OtpMailService::verifyOtp($user, $this->otpCode);
            Auth::login($user);
            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            $this->addError('otpCode', $e->getMessage());
        }
    }

    public function resendOtp()
    {
        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->step = 'credentials';
            return;
        }

        if (!OtpMailService::canResend($user)) {
            $this->addError('otpCode', 'Please wait before requesting a new OTP.');
            return;
        }

        OtpMailService::sendOtp($user, 'Super Admin');
        $this->otpCode = '';
        $this->resetValidation('otpCode');
    }

    public function backToLogin()
    {
        $this->step = 'credentials';
        $this->otpCode = '';
        $this->otpSentTo = '';
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.super-admin.login')->layout('components.layouts.fullscreen');
    }
}
