<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-purple-50 to-pink-50 p-4 sm:p-6">
    <!-- Split Screen Container -->
    <div class="flex flex-col md:flex-row w-full max-w-5xl md:h-[600px] rounded-2xl overflow-hidden shadow-lg bg-white">

        <!-- Left Side (Decorative) -->
        <div
            class="hidden md:flex md:w-1/2 bg-gradient-to-br from-purple-100 to-pink-100 flex-col items-center justify-center p-8 relative">
            <div
                class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyMTYsIDE4MCwgMjU1LCAwLjEpIj48L3JlY3Q+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIj48L3JlY3Q+PC9zdmc+')]">
            </div>
            <div class="relative z-10 w-3/4 max-w-xs">
                <img src="{{ asset('admin-image/Frame 1171279095.png') }}" alt="Illustration"
                    class="w-full h-auto object-contain">
            </div>
        </div>

        <!-- Right Side (Form) -->
        <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-6 sm:p-8 md:p-12">

            <!-- Logo -->
            <div class="mb-4 sm:mb-6 w-16 h-16 sm:w-20 sm:h-20">
                <img src="{{ asset('website-image/Group 11525.png') }}" alt="Logo"
                    class="w-full h-full object-contain">
            </div>

            {{-- ── STEP 1: Credentials ── --}}
            @if ($step === 'credentials')
                <div class="text-center mb-6 sm:mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Welcome Back!</h1>
                    <p class="text-gray-500 mt-2 text-sm">Login to continue to your dashboard</p>
                </div>

                <div class="w-full max-w-sm">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                        <input type="email" wire:model="email" placeholder="Enter your Email"
                            wire:keydown.enter="login"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                        <div class="relative">
                            <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model="password"
                                placeholder="Enter your password"
                                wire:keydown.enter="login"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                            <button type="button" wire:click="toggleShowPassword"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                @if ($showPassword)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                @endif
                            </button>
                        </div>
                        @error('password')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mb-5">
                        <a href="{{ route('reset.password') }}"
                            class="text-sm text-purple-600 hover:text-purple-800 hover:underline">Forgot password?</a>
                    </div>

                    <button wire:click="login" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-3 text-white rounded-lg hover:bg-gradient-3-hover transition duration-300 shadow-md hover:shadow-lg disabled:opacity-60">
                        <span wire:loading.remove wire:target="login">Login</span>
                        <span wire:loading wire:target="login">Sending OTP…</span>
                    </button>
                </div>
            @endif

            {{-- ── STEP 2: OTP Verification ── --}}
            @if ($step === 'otp')
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800">Verify OTP</h1>
                    <p class="text-gray-500 mt-1 text-sm">Enter the 6-digit code sent to your email</p>
                    <p class="text-purple-600 text-sm font-medium mt-1">{{ $otpSentTo }}</p>
                </div>

                <div class="w-full max-w-sm">
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
                        }" class="flex justify-center gap-2 sm:gap-3 mb-4">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" maxlength="1"
                                x-ref="otp{{ $i }}"
                                x-model="otp[{{ $i }}]"
                                x-on:input="focusNext({{ $i }})"
                                x-on:keydown="focusPrev({{ $i }}, $event)"
                                inputmode="numeric"
                                class="w-10 h-12 sm:w-11 sm:h-14 text-center text-lg sm:text-xl font-bold border-2 border-gray-300 rounded-xl
                                       focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all"
                                @if ($i === 0) autofocus @endif>
                        @endfor
                    </div>

                    @error('otp')
                        <p class="text-red-500 text-xs text-center mb-3">{{ $message }}</p>
                    @enderror

                    <button wire:click="verifyOtp" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-3 text-white rounded-lg hover:bg-gradient-3-hover transition duration-300 shadow-md disabled:opacity-60 mb-4">
                        <span wire:loading.remove wire:target="verifyOtp">Verify &amp; Login</span>
                        <span wire:loading wire:target="verifyOtp">Verifying…</span>
                    </button>

                    {{-- Countdown + Resend — restarts when canResend flips back to false --}}
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
                                <span class="font-semibold text-purple-600"
                                    x-text="Math.floor(countdown / 60) + ':' + String(countdown % 60).padStart(2, '0')"></span>
                            </p>
                        </template>
                        <template x-if="canResend">
                            <button wire:click="resendOtp"
                                class="text-sm text-purple-600 hover:text-purple-800 font-medium hover:underline">
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
                </div>
            @endif

        </div>
    </div>
</div>
