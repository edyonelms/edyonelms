<div class="bg-gray-50 p-4 md:p-8">
    <div class="mx-auto">
        @if (!$rated)
            <!-- Rating Form -->
            <div class="bg-white rounded-xl shadow-md p-6 md:p-8">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-semibold text-gray-800 mb-2">Rate Our Service</h1>
                    <p class="text-gray-600">Your feedback helps us improve</p>
                </div>

                <!-- Star Rating -->
                <div class="mb-8">
                    <p class="text-center text-gray-700 mb-4">How would you rate your experience?</p>
                    <div class="flex justify-center space-x-2">
                        @for ($i = 1; $i <= 5; $i++)
                            <button type="button" wire:click="setRating({{ $i }})"
                                class="focus:outline-none transition-transform duration-200 hover:scale-110">
                                <svg class="w-12 h-12 {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 fill-transparent' }}"
                                    stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <p class="text-center text-sm text-gray-500 mt-2">
                        {{ $rating > 0 ? "Selected: {$rating} star" . ($rating > 1 ? 's' : '') : 'Select a rating' }}
                    </p>
                </div>

                <!-- Review Textarea -->
                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        Additional Comments (Optional)
                    </label>
                    <textarea wire:model="feedback" rows="4"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-700 resize-none"
                        placeholder="Share your thoughts about our service..."></textarea>
                    @error('feedback')
                        <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center">
                    <button type="submit" wire:click="submit"
                        class="px-8 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Submit Feedback
                    </button>
                </div>
            </div>
        @else
            <!-- Thank You Screen -->
            <div class="bg-white rounded-xl shadow-md md:p-8">
                <!-- Success Header -->
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-semibold text-gray-800 mb-2">Thank You!</h1>
                    <p class="text-gray-600">Your feedback has been submitted successfully.</p>
                </div>

                <!-- Divider -->
                <div class="border-t border-gray-200 my-6"></div>

                <!-- Review Summary -->
                <div class="space-y-6 ">
                    <!-- Rating Display -->
                    <h3 class="text-lg font-medium text-gray-800 mb-3 flex justify-center">Your Rating</h3>
                    <div class="flex justify-center">
                        <div class="flex items-center space-x-2 mb-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-6 h-6 {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300 fill-transparent' }}"
                                    stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            @endfor
                            <span class="text-gray-700 font-medium ml-2">{{ $rating }}/5</span>
                        </div>
                    </div>

                    <!-- Feedback Content -->
                    @if ($feedback)
                        <div>
                            <h3 class="text-lg font-medium text-gray-800 mb-3">Your Comments</h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-gray-700 italic">"{{ $feedback }}"</p>

                            </div>
                            <span class="text-gray-700">Submitted on: {{ $submittedAt }}</span>

                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
