<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-10">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-8 sm:p-10">

        {{-- Logo --}}
        <div class="w-12 h-12 mx-auto mb-6">
            <img src="{{ asset('website-image/Group 11525.png') }}" alt="Logo" class="w-full h-full object-contain">
        </div>

        {{-- ========== STEP 1: EMAIL ========== --}}
        @if ($step === 'email')
            {{-- Illustration: recover access key --}}
            <div class="w-20 h-20 mx-auto mb-5">
                <svg viewBox="0 0 64 64" fill="none" class="w-full h-full">
                    <circle cx="24" cy="40" r="13" fill="#EFF4FF" stroke="#3B82F6" stroke-width="2.5"/>
                    <circle cx="24" cy="40" r="4.5" fill="#fff" stroke="#3B82F6" stroke-width="2.5"/>
                    <path d="M31 33l20-20M44 7l9 9M39 12l7 7" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-2xl font-semibold text-gray-900">Forgot Password?</h1>
                <p class="text-gray-500 mt-1.5 text-sm">Enter your email to receive an OTP</p>
            </div>

            <div x-data="{ email: @entangle('email') }">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1.5">Email Address</label>
                    <input type="email" x-model="email"
                        placeholder="Enter your email address"
                        wire:keydown.enter="submitEmail"
                        class="w-full px-4 py-3 text-gray-700 bg-white border border-gray-200 rounded-xl placeholder-gray-400 transition focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button wire:click="submitEmail" wire:loading.attr="disabled"
                    :disabled="!email.trim()"
                    class="w-full py-3 rounded-xl text-white font-medium bg-gradient-3 shadow-sm transition hover:opacity-95 disabled:opacity-40 disabled:cursor-not-allowed mb-4">
                    <span wire:loading.remove wire:target="submitEmail">Send OTP</span>
                    <span wire:loading wire:target="submitEmail">Sending…</span>
                </button>

                <div class="text-center">
                    <a href="{{ route('super-admin.login') }}" class="text-sm text-gray-400 hover:text-gray-600 hover:underline">
                        ← Back to Login
                    </a>
                </div>
            </div>
        @endif

        {{-- ========== STEP 2: OTP ========== --}}
        @if ($step === 'otp')
            {{-- Illustration: email with verified badge --}}
            <div class="w-20 h-20 mx-auto mb-5">
                <svg viewBox="0 0 64 64" fill="none" class="w-full h-full">
                    <rect x="8" y="14" width="48" height="34" rx="5" fill="#EFF4FF" stroke="#3B82F6" stroke-width="2.5"/>
                    <path d="M10 18l22 16 22-16" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="48" cy="46" r="11" fill="#3B82F6" stroke="#fff" stroke-width="2.5"/>
                    <path d="M43.5 46l3 3 6-6.5" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-2xl font-semibold text-gray-900">Verify OTP</h1>
                <p class="text-gray-500 mt-1.5 text-sm">Enter the 6-digit code sent to your email</p>
                <p class="text-blue-600 text-sm font-medium mt-1">{{ $email }}</p>
            </div>

            {{-- 6-box OTP input --}}
            <div x-data="{
                    otp: @entangle('otp'),
                    focusNext(index) {
                        if (this.otp[index] && index < 5) {
                            this.$refs['otp' + (index + 1)].focus();
                        }
                    },
                    focusPrev(index, event) {
                        if (event.key === 'Backspace' && !this.otp[index] && index > 0) {
                            this.$refs['otp' + (index - 1)].focus();
                        }
                    }
                }">
                <div class="flex justify-center gap-2 sm:gap-3 mb-4">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" maxlength="1"
                            x-ref="otp{{ $i }}"
                            x-model="otp[{{ $i }}]"
                            x-on:input="focusNext({{ $i }})"
                            x-on:keydown="focusPrev({{ $i }}, $event)"
                            inputmode="numeric"
                            class="w-10 h-12 sm:w-11 sm:h-14 text-center text-lg sm:text-xl font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl transition focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10"
                            @if ($i === 0) autofocus @endif>
                    @endfor
                </div>

                @error('otp')
                    <p class="text-red-500 text-xs text-center mb-3">{{ $message }}</p>
                @enderror

                <button wire:click="verifyOtp"
                    :disabled="otp.join('').length !== 6"
                    class="w-full py-3 rounded-xl text-white font-medium bg-gradient-3 shadow-sm transition hover:opacity-95 disabled:opacity-40 disabled:cursor-not-allowed mb-4">
                    <span wire:loading.remove wire:target="verifyOtp">Verify OTP</span>
                    <span wire:loading wire:target="verifyOtp">Verifying…</span>
                </button>
            </div>

            {{-- Countdown + Resend --}}
            <div class="text-center" x-data="{
                    countdown: @entangle('countdown'),
                    canResend: @entangle('canResend'),
                    timer: null,
                    startTimer() {
                        if (this.timer) clearInterval(this.timer);
                        this.timer = setInterval(() => {
                            if (this.countdown > 0) {
                                this.countdown--;
                            } else {
                                this.canResend = true;
                                clearInterval(this.timer);
                                this.timer = null;
                                $wire.timerFinished();
                            }
                        }, 1000);
                    }
                }" x-init="
                    startTimer();
                    $watch('canResend', value => {
                        if (value === false && countdown > 0) startTimer();
                    });
                ">
                <template x-if="!canResend">
                    <p class="text-sm text-gray-500">
                        Resend OTP in
                        <span class="font-semibold text-blue-600"
                            x-text="Math.floor(countdown / 60) + ':' + String(countdown % 60).padStart(2, '0')"></span>
                    </p>
                </template>
                <template x-if="canResend">
                    <button wire:click="resendOtp"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">
                        Resend OTP
                    </button>
                </template>
            </div>

            <div class="text-center mt-3">
                <button wire:click="$set('step', 'email')"
                    class="text-sm text-gray-400 hover:text-gray-600 hover:underline">
                    ← Change Email
                </button>
            </div>
        @endif

        {{-- ========== STEP 3: NEW PASSWORD ========== --}}
        @if ($step === 'password')
            {{-- Illustration: secured lock with check --}}
            <div class="w-20 h-20 mx-auto mb-5">
                <svg viewBox="0 0 64 64" fill="none" class="w-full h-full">
                    <rect x="14" y="28" width="36" height="28" rx="6" fill="#EFF4FF" stroke="#3B82F6" stroke-width="2.5"/>
                    <path d="M22 28v-6a10 10 0 0120 0v6" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round"/>
                    <path d="M26 42l4 4 8-8.5" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-2xl font-semibold text-gray-900">Set New Password</h1>
                <p class="text-gray-500 mt-1.5 text-sm">Create a strong password for your account</p>
            </div>

            <div x-data="{
                    password: @entangle('password'),
                    confirmation: @entangle('password_confirmation'),
                    showPass: false,
                    showConfirm: false,
                    get hasLength() { return this.password.length >= 8 && this.password.length <= 16; },
                    get hasNumber() { return /[0-9]/.test(this.password); },
                    get hasSpecial() { return /[!@#$%^&*(),.?\&quot;:{}|<>]/.test(this.password); },
                    get filled() { return this.password.trim().length > 0 && this.confirmation.trim().length > 0; }
                }">
                {{-- New Password --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1.5">New Password</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" x-model="password"
                            placeholder="Enter new password"
                            class="w-full px-4 py-3 pr-11 text-gray-700 bg-white border border-gray-200 rounded-xl placeholder-gray-400 transition focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                        <button type="button" @click="showPass = !showPass"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showPass" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1.5">Confirm Password</label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'" x-model="confirmation"
                            placeholder="Confirm new password"
                            class="w-full px-4 py-3 pr-11 text-gray-700 bg-white border border-gray-200 rounded-xl placeholder-gray-400 transition focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                        <button type="button" @click="showConfirm = !showConfirm"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg x-show="!showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="showConfirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Password Requirements (live) --}}
                <div class="mb-6 p-3.5 bg-gray-50 rounded-xl ring-1 ring-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-2">Password must contain:</p>
                    <ul class="space-y-1.5">
                        <li class="flex items-center gap-2 text-xs transition" :class="hasLength ? 'text-green-600' : 'text-gray-400'">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="hasLength ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'"/>
                            </svg>
                            8–16 characters
                        </li>
                        <li class="flex items-center gap-2 text-xs transition" :class="hasNumber ? 'text-green-600' : 'text-gray-400'">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="hasNumber ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'"/>
                            </svg>
                            At least 1 number
                        </li>
                        <li class="flex items-center gap-2 text-xs transition" :class="hasSpecial ? 'text-green-600' : 'text-gray-400'">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="hasSpecial ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12'"/>
                            </svg>
                            At least 1 special character (!@#$%^&amp;*...)
                        </li>
                    </ul>
                </div>

                <button wire:click="resetPassword" wire:loading.attr="disabled"
                    :disabled="!filled"
                    class="w-full py-3 rounded-xl text-white font-medium bg-gradient-3 shadow-sm transition hover:opacity-95 disabled:opacity-40 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                    <span wire:loading wire:target="resetPassword">Resetting…</span>
                </button>

                <div class="text-center mt-4">
                    <a href="{{ route('super-admin.login') }}" class="text-sm text-gray-400 hover:text-gray-600 hover:underline">
                        ← Back to Login
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
