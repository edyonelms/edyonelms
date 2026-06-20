<div class="min-h-screen flex items-center justify-center bg-gray-50 px-4 py-10">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-8 sm:p-10">

        {{-- Logo --}}
        <div class="w-12 h-12 mx-auto mb-6">
            <img src="{{ asset('website-image/Group 11525.png') }}" alt="Logo" class="w-full h-full object-contain">
        </div>

        @if (session('success'))
            <div class="mb-6 flex items-start gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm text-green-700 ring-1 ring-green-100">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- ── STEP 1: Credentials ── --}}
        @if ($step === 'credentials')
            {{-- Illustration: secure login shield --}}
            <div class="w-20 h-20 mx-auto mb-5">
                <svg viewBox="0 0 64 64" fill="none" class="w-full h-full">
                    <path d="M32 6l20 8v14c0 13-8.6 22-20 26-11.4-4-20-13-20-26V14l20-8z" fill="#EFF4FF" stroke="#3B82F6" stroke-width="2.5" stroke-linejoin="round"/>
                    <rect x="24" y="29" width="16" height="13" rx="2.5" fill="#fff" stroke="#3B82F6" stroke-width="2.5"/>
                    <path d="M27 29v-3a5 5 0 0110 0v3" stroke="#3B82F6" stroke-width="2.5" stroke-linecap="round"/>
                    <circle cx="32" cy="34" r="2" fill="#3B82F6"/>
                </svg>
            </div>

            <div class="text-center mb-8">
                <h1 class="text-2xl font-semibold text-gray-900">Admin Login</h1>
                <p class="text-gray-500 mt-1.5 text-sm">Login to continue to your dashboard</p>
            </div>

            <div x-data="{ email: @entangle('email'), password: @entangle('password'), showPass: false }">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1.5">Email</label>
                    <input type="email" x-model="email" placeholder="Enter your email"
                        class="w-full px-4 py-3 text-gray-700 bg-white border border-gray-200 rounded-xl placeholder-gray-400 transition focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10">
                    @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500 mb-1.5">Password</label>
                    <div class="relative">
                        <input :type="showPass ? 'text' : 'password'" x-model="password"
                            placeholder="Enter your password"
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

                <div class="flex justify-end mb-6">
                    <a href="{{ route('super-admin.forgot-password') }}"
                        class="text-sm text-blue-600 hover:text-blue-700 hover:underline">Forgot password?</a>
                </div>

                <button wire:click="login" wire:loading.attr="disabled"
                    :disabled="!email.trim() || !password.trim()"
                    class="w-full py-3 rounded-xl text-white font-medium bg-gradient-3 shadow-sm transition hover:opacity-95 disabled:opacity-40 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="login">Login</span>
                    <span wire:loading wire:target="login">Sending OTP…</span>
                </button>
            </div>
        @endif

        {{-- ── STEP 2: OTP Verification ── --}}
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
                <p class="text-blue-600 text-sm font-medium mt-1">{{ $otpSentTo }}</p>
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
                    <span wire:loading.remove wire:target="verifyOtp">Verify &amp; Login</span>
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
                <button wire:click="backToLogin"
                    class="text-sm text-gray-400 hover:text-gray-600 hover:underline">
                    ← Back to login
                </button>
            </div>
        @endif
    </div>
</div>
