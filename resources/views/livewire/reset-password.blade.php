<div class="min-h-screen flex items-center justify-center bg-gradient-to-r from-purple-50 to-pink-50">
    <div class="flex flex-col md:flex-row w-full max-w-5xl h-[600px] rounded-2xl overflow-hidden shadow-lg bg-white">
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

        <div class="w-full md:w-1/2 flex flex-col items-center justify-center p-8 md:p-12">
            <div class="mb-8 w-20 h-20">
                <img src="{{ asset('website-image/Group 11525.png') }}" alt="Logo"
                    class="w-full h-full object-contain">
            </div>

            {{-- Step Indicators --}}
            <div class="flex items-center gap-2 mb-6">
                @for ($s = 1; $s <= 3; $s++)
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $step >= $s ? 'bg-purple-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            {{ $s }}
                        </div>
                        @if ($s < 3)
                            <div class="w-8 h-0.5 {{ $step > $s ? 'bg-purple-600' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endfor
            </div>

            @if ($step === 1)
                <div class="text-center mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Reset Password</h1>
                    <p class="text-gray-500 mt-2">Enter your registered email to receive OTP</p>
                </div>

                <div class="w-full max-w-sm">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Email Address</label>
                        <input type="email" wire:model.live="email" placeholder="Enter your registered email"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('email')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <button wire:click="sendOtp" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:opacity-90 transition duration-300 shadow-md hover:shadow-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="sendOtp">Send OTP</span>
                        <span wire:loading wire:target="sendOtp">Sending...</span>
                    </button>
                </div>
            @elseif($step === 2)
                <div class="text-center mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Verify OTP</h1>
                    <p class="text-gray-500 mt-2">Enter the OTP sent to
                        {{ substr($email, 0, 3) }}***{{ substr($email, strpos($email, '@')) }}</p>
                </div>

                <div class="w-full max-w-sm" x-data="{
                    otp: @js($otp),
                    countdown: @js($countdown),
                    interval: null,
                    init() {
                        this.startTimer();
                        this.$watch('otp', (newOtp) => {
                            @this.set('otp', newOtp);
                        });
                    },
                    startTimer() {
                        if (this.interval) clearInterval(this.interval);
                        this.countdown = 120;
                        this.interval = setInterval(() => {
                            if (this.countdown > 0) {
                                this.countdown--;
                            } else {
                                clearInterval(this.interval);
                                @this.call('timerFinished');
                            }
                        }, 1000);
                    },
                    handleInput(index) {
                        if (this.otp[index].length === 1 && index < 5) {
                            this.$refs[`otp${index + 1}`].focus();
                        }
                    },
                    handleBackspace(index) {
                        if (this.otp[index].length === 0 && index > 0) {
                            this.$refs[`otp${index - 1}`].focus();
                        }
                    },
                    formatCountdown(seconds) {
                        if (seconds <= 0) return 'Resend available';
                        const minutes = Math.floor(seconds / 60);
                        const secs = seconds % 60;
                        return `${minutes}:${secs.toString().padStart(2, '0')}`;
                    }
                }" @start-countdown.window="startTimer()">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">OTP Code</label>
                        <div class="flex justify-between space-x-2">
                            @for ($i = 0; $i < 6; $i++)
                                <input type="text" x-model="otp[{{ $i }}]" x-ref="otp{{ $i }}"
                                    maxlength="1"
                                    @input="handleInput({{ $i }})"
                                    @keydown.backspace="handleBackspace({{ $i }})"
                                    class="w-12 h-12 text-center text-xl border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                    @if ($i === 0) autofocus @endif inputmode="numeric"
                                    pattern="[0-9]*"
                                    x-on:keypress="return ($event.charCode >= 48 && $event.charCode <= 57)">
                            @endfor
                        </div>
                        @error('otp')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <button wire:click="resendOtp" x-bind:disabled="countdown > 0"
                            class="text-sm"
                            x-bind:class="countdown > 0 ? 'text-gray-400 cursor-not-allowed' : 'text-purple-600 hover:text-purple-800 hover:underline cursor-pointer'">
                            Resend OTP
                        </button>
                        <span class="text-sm text-gray-500" x-text="formatCountdown(countdown)"></span>
                    </div>

                    <button wire:click="verifyOtp" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:opacity-90 transition duration-300 shadow-md hover:shadow-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="verifyOtp">Verify OTP</span>
                        <span wire:loading wire:target="verifyOtp">Verifying...</span>
                    </button>
                </div>
            @elseif($step === 3)
                <div class="text-center mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Set New Password</h1>
                    <p class="text-gray-500 mt-2">Create a strong new password</p>
                </div>

                <div class="w-full max-w-sm">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-medium mb-1">New Password</label>
                        <div class="relative">
                            <input type="{{ $showPassword ? 'text' : 'password' }}" wire:model.live="password"
                                placeholder="Enter new password"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                            <button type="button" wire:click="$toggle('showPassword')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                @if ($showPassword)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                @endif
                            </button>
                        </div>
                        @error('password')
                            <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Password Requirements --}}
                    <div class="mb-4 text-xs text-gray-500 space-y-1">
                        <p class="{{ strlen($password) >= 8 && strlen($password) <= 16 ? 'text-green-600' : '' }}">
                            {{ strlen($password) >= 8 && strlen($password) <= 16 ? '✓' : '○' }} 8-16 characters
                        </p>
                        <p class="{{ preg_match('/[0-9]/', $password) ? 'text-green-600' : '' }}">
                            {{ preg_match('/[0-9]/', $password) ? '✓' : '○' }} At least one number
                        </p>
                        <p class="{{ preg_match('/[^a-zA-Z0-9]/', $password) ? 'text-green-600' : '' }}">
                            {{ preg_match('/[^a-zA-Z0-9]/', $password) ? '✓' : '○' }} At least one special character
                        </p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-medium mb-1">Confirm Password</label>
                        <div class="relative">
                            <input type="{{ $showConfirmPassword ? 'text' : 'password' }}"
                                wire:model.live="password_confirmation" placeholder="Confirm new password"
                                class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                            <button type="button" wire:click="$toggle('showConfirmPassword')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                @if ($showConfirmPassword)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                @endif
                            </button>
                        </div>
                    </div>

                    <button wire:click="resetPassword" wire:loading.attr="disabled"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-500 text-white rounded-lg hover:opacity-90 transition duration-300 shadow-md hover:shadow-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="resetPassword">Reset Password</span>
                        <span wire:loading wire:target="resetPassword">Resetting...</span>
                    </button>
                </div>
            @endif

            <div class="mt-6 text-center">
                <a href="{{ route('admin.login') }}"
                    class="text-sm text-purple-600 hover:text-purple-800 hover:underline">Back to Login</a>
            </div>
        </div>
    </div>
</div>
