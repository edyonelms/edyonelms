<div class="p-4 md:p-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 px-2">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rate Our LMS</h1>
            <p class="text-sm text-gray-500 mt-0.5">Share your experience and help us improve</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 bg-white border border-gray-200 rounded-lg px-4 py-2 shadow-sm w-fit">
            <svg class="w-4 h-4 text-yellow-400 fill-yellow-400" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
            </svg>
            <span>Your feedback matters</span>
        </div>
    </div>

    <div class="max-w-2xl mx-auto">

        @if (!$rated)
            {{-- Rating Form --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Card Header --}}
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-800">How was your experience?</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Rate {{ $organization->name ?? 'our LMS' }} and leave your thoughts</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-6">

                    {{-- Star Rating --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Select your rating</label>
                        <div class="flex items-center gap-2">
                            @for ($i = 1; $i <= 5; $i++)
                                <button type="button" wire:click="setRating({{ $i }})"
                                    class="focus:outline-none transition-all duration-150 hover:scale-110 active:scale-95">
                                    <svg class="w-10 h-10 transition-colors duration-150 {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-200 fill-gray-200 hover:text-yellow-300 hover:fill-yellow-300' }}"
                                        stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                </button>
                            @endfor
                            @if ($rating > 0)
                                <span class="ml-2 text-sm font-medium text-gray-600">
                                    @if ($rating == 1) Poor
                                    @elseif ($rating == 2) Fair
                                    @elseif ($rating == 3) Good
                                    @elseif ($rating == 4) Very Good
                                    @else Excellent!
                                    @endif
                                </span>
                            @endif
                        </div>
                        @error('rating')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Feedback --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Your Comments <span class="text-red-400">*</span>
                        </label>
                        <textarea wire:model="feedback" rows="4"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-400 text-gray-700 text-sm resize-none transition-colors"
                            placeholder="Tell us what you liked or what we can improve..."></textarea>
                        @error('feedback')
                            <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="pt-1">
                        <button type="button" wire:click="submit" wire:loading.attr="disabled"
                            class="w-full sm:w-auto px-8 py-2.5 bg-gradient-3 hover:bg-gradient-3-hover text-white text-sm font-medium rounded-xl transition-all duration-200 disabled:opacity-60 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="submit">Submit Feedback</span>
                            <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>

        @else
            {{-- Thank You Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Success Banner --}}
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-800">Thank you for your feedback!</h2>
                            <p class="text-xs text-gray-500 mt-0.5">Submitted on {{ $submittedAt }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-5">

                    {{-- Rating Display --}}
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Your Rating</p>
                        <div class="flex items-center gap-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-7 h-7 {{ $i <= $rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-200 fill-gray-200' }}"
                                    stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                            @endfor
                            <span class="ml-2 text-sm font-semibold text-gray-700">{{ $rating }}/5 —
                                @if ($rating == 1) Poor
                                @elseif ($rating == 2) Fair
                                @elseif ($rating == 3) Good
                                @elseif ($rating == 4) Very Good
                                @else Excellent
                                @endif
                            </span>
                        </div>
                    </div>

                    {{-- Feedback Display --}}
                    @if ($feedback)
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Your Comments</p>
                            <p class="text-sm text-gray-700 italic leading-relaxed">"{{ $feedback }}"</p>
                        </div>
                    @endif

                    <p class="text-xs text-gray-400 text-center">Your feedback has been recorded. We appreciate your time!</p>
                </div>
            </div>
        @endif

    </div>
</div>
