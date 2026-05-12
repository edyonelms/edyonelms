<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email, $password;

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [
        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'password.required' => 'The password field is required.',
    ];

    public function login()
    {
        $this->validate();

        $user = User::where('email', $this->email)->first();

        if (!$user) {
            $this->addError('email', 'Email does not exist.');
            return;
        }

        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password])) {
            $this->addError('password', 'Incorrect password.');
            return;
        }

        if ($user->role !== 'super-admin') {
            Auth::logout();
            $this->addError('email', 'You do not have super-admin access.');
            return;
        }

        return redirect()->intended('/dashboard');
    }

    public function render()
    {
        return view('livewire.super-admin.login')->layout('components.layouts.fullscreen');
    }
}
