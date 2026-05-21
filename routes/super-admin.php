<?php

use App\Livewire\SuperAdmin\AboutApp;
use App\Livewire\SuperAdmin\Analytics;
use App\Livewire\SuperAdmin\Credit;
use App\Livewire\SuperAdmin\Dashboard;
use App\Livewire\SuperAdmin\Enquiry;
use App\Livewire\SuperAdmin\Fees;
use App\Livewire\SuperAdmin\ForgotPassword;
use App\Livewire\SuperAdmin\Login;
use App\Livewire\SuperAdmin\Payroll;
use App\Livewire\SuperAdmin\PortalWebsite;
use App\Livewire\SuperAdmin\PrivacyPolicy;
use App\Livewire\SuperAdmin\Rating;
use App\Livewire\SuperAdmin\Schools;
use App\Livewire\SuperAdmin\Session;
use App\Livewire\SuperAdmin\Student;
use App\Livewire\SuperAdmin\Support;
use App\Livewire\SuperAdmin\Teacher;
use App\Livewire\SuperAdmin\Users;
use App\Livewire\SuperAdmin\Profile as SuperAdminProfile;
use App\Livewire\SuperAdmin\TermOfUse;
use App\Livewire\SuperAdmin\TermsCondition;
use App\Livewire\SuperAdmin\WebsiteData;
use App\Livewire\Components\Profile;
use Illuminate\Support\Facades\Route;

// Super Admin Routes
Route::middleware(['guest:web'])->group(function () {
    Route::get('admin', Login::class)->name('super-admin.login');
    Route::get('forgot-password', ForgotPassword::class)->name('super-admin.forgot-password');
});

Route::middleware(['auth:web', 'super-admin'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('super-admin.dashboard');
    Route::get('schools', Schools::class)->name('super-admin.schools');
    Route::get('students', Student::class)->name('super-admin.students');
    Route::get('teachers', Teacher::class)->name('super-admin.teachers');
    Route::get('fees', Fees::class)->name('super-admin.fees');
    Route::get('users', Users::class)->name('super-admin.users');
    Route::get('payroll', Payroll::class)->name('super-admin.payroll');
    Route::get('enquiries', Enquiry::class)->name('super-admin.enquiries');
    Route::get('support', Support::class)->name('super-admin.support');
    Route::get('website-data', WebsiteData::class)->name('super-admin.website-data');
    Route::get('analytics', Analytics::class)->name('super-admin.analytics');
    Route::get('portal-website', PortalWebsite::class)->name('super-admin.portal-website');
    Route::get('terms-condition', TermsCondition::class)->name('super-admin.terms-condition');
    Route::get('privacy-policy', PrivacyPolicy::class)->name('super-admin.privacy-policy');
    Route::get('rating', Rating::class)->name('super-admin.rating');
    Route::get('about-app', AboutApp::class)->name('super-admin.about-app');
    Route::get('term-of-use', TermOfUse::class)->name('super-admin.term-of-use');
    Route::get('sessions', Session::class)->name('super-admin.sessions');
    Route::get('credit', Credit::class)->name('super-admin.credit');
    Route::get('profile', SuperAdminProfile::class)->name('super-admin.profile');
});
