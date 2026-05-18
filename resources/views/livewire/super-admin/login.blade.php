<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-purple-50 to-pink-50">
    <div class="flex flex-col md:flex-row w-full max-w-5xl h-[600px] rounded-2xl overflow-hidden shadow-lg bg-white">

        {{-- Left decorative panel --}}
        <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-purple-100 to-pink-100 flex-col items-center justify-center p-8 relative">
            <div class="absolute inset-0 opacity-10 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiIHBhdHRlcm5UcmFuc2Zvcm09InJvdGF0ZSg0NSkiPjxyZWN0IHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCIgZmlsbD0icmdiYSgyMTYsIDE4MCwgMjU1LCAwLjEpIj48L3JlY3Q+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIj48L3JlY3Q+PC9zdmc+')]"></div>
            <div class="relative z-10 w-3/4 max-w-xs">
                <img src="{{ asset('admin-image/Frame 1171279095.png') }}" alt="Illustration" class="w-full h-auto object-contain">
            </div>
        </div>

        {{-- Right form panel --}}
        <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-8 md:p-12">
            <div class="mb-8 w-20 h-20">
                <img src="{{ asset('website-image/Group 11525.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>

            {{-- ── STEP 1: Credentials ── --}}
            @if ($step === 'credentials')
                <div class="text-center mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Admin Login</h1>
                    <p class="text-gray-500 mt-2">Login to continue to your dashboard</p>
                </div>

                <div class="w-full max-w-sm">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
                        <input type="email" wire:model="email" placeholder="Enter your email"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="mb-4" x-data="{ showPass: false }">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Password</label>
                        <div class="relative">
                            <input :type="showPass ? 'text' : 'password'" wire:model="password"
                                placeholder="Enter your password"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
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

                    <div class="flex items-center justify-end mb-6">
                        <a href="{{ route('super-admin.forgot-password') }}"
                            class="text-sm text-purple-600 hover:text-purple-800 hover:underline">Forgot password?</a>
                    </div>

                    <button wire:click="login" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-3 text-white rounded-lg hover:opacity-90 transition duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="login">Login</span>
                        <span wire:loading wire:target="login">Sending OTP…</span>
                    </button>
                </div>
            @endif

            {{-- ── STEP 2: OTP Verification ── --}}
            @if ($step === 'otp')
                <div class="text-center mb-8">
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">Verify OTP</h1>
                    <p class="text-gray-500 mt-2 text-sm">
                        A 6-digit code was sent to<br>
                        <span class="font-medium text-gray-700">{{ $otpSentTo }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Valid for 2 minutes</p>
                </div>

                <div class="w-full max-w-sm">
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Enter OTP</label>
                        <input type="text" wire:model="otpCode" inputmode="numeric" maxlength="6"
                            placeholder="_ _ _ _ _ _"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-center text-2xl tracking-[0.5em] font-mono"
                            wire:keydown.enter="verifyOtp">
                        @error('otpCode') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="verifyOtp" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-3 text-white rounded-lg hover:opacity-90 transition duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="verifyOtp">Verify & Login</span>
                        <span wire:loading wire:target="verifyOtp">Verifying…</span>
                    </button>

                    <div class="flex items-center justify-between mt-4">
                        <button wire:click="backToLogin"
                            class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                            ← Back
                        </button>
                        <button wire:click="resendOtp"
                            class="text-sm text-purple-600 hover:text-purple-800 hover:underline">
                            Resend OTP
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
