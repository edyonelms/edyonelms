<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $email, $password;
    public $showPassword = false; 
    
    protected $rules = [
        'email' => 'required|email',
        'password' => 'required',
    ];

    protected $messages = [
        'email.required' => 'The email field is required.',
        'email.email' => 'The email must be a valid email address.',
        'password.required' => 'The password field is required.',
    ];

    // Add this method to toggle password visibility
    public function toggleShowPassword()
    {
        $this->showPassword = !$this->showPassword;
    }

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

        if (!in_array($user->role, ['admin', 'sub-admin'], true)) {
            Auth::logout();
            $this->addError('email', 'You do not have school admin access.');
            return redirect()->route('admin.login');
        }

        if (!$user->organization_id) {
            Auth::logout();
            $this->addError('email', 'No organization assigned to this account.');
            return redirect()->route('admin.login');
        }

        // Scoped sub-admins must be active to sign in.
        if ($user->role === 'sub-admin' && !$user->is_active) {
            Auth::logout();
            $this->addError('email', 'Your account is inactive. Please contact the administrator.');
            return redirect()->route('admin.login');
        }

        // Sub-admins land on their first granted screen; full admins on Quick Links.
        $landingRoute = 'admin.quick-links';
        if ($user->role === 'sub-admin') {
            $permissions  = (array) $user->permissions;
            $landingRoute = $permissions[0] ?? 'admin.profile';
        }

        return redirect()->route($landingRoute, ['organization' => $user->organization_id])
            ->with('success', 'Login successful.');
    }

    public function render()
    {
        return view('livewire.admin.login')->layout('components.layouts.fullscreen');
    }
}
