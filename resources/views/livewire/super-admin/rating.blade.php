<div class="min-h-screen bg-gray-50">

    {{-- ══════════ HEADER ══════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-4">
                {{-- LMS Logo --}}
                <div class="flex-shrink-0">
                    <img src="{{ asset('website-image/Group 11525.png') }}" alt="LMS Logo"
                        class="w-12 h-12 rounded-xl object-contain border border-gray-200 bg-white p-1 shadow-sm"
                        onerror="this.style.display='none'">
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">School Reviews & Ratings</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Manage and view feedback from all schools</p>
                </div>
            </div>

            {{-- Header Stats --}}
            <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 divide-x divide-gray-200">
                <span class="pr-4">Schools: <strong
                        class="text-gray-800">{{ $stats['active_schools_with_reviews'] }}</strong></span>
                <span class="px-4">Total: <strong class="text-gray-800">{{ $stats['total_reviews'] }}</strong></span>
                <span class="px-4">Active: <strong
                        class="text-emerald-600">{{ $stats['active_reviews'] }}</strong></span>
                <span class="px-4">Pending: <strong
                        class="text-amber-500">{{ $stats['pending_reviews'] }}</strong></span>
                <span class="pl-4">
                    Avg:
                    <strong class="text-yellow-500">
                        {{ number_format($stats['average_rating'], 1) }} ★
                    </strong>
                </span>
            </div>
        </div>

        {{-- Mobile Stats --}}
        <div class="flex lg:hidden flex-wrap items-center gap-3 text-xs text-gray-500 mt-3">
            <span>Schools: <strong class="text-gray-800">{{ $stats['active_schools_with_reviews'] }}</strong></span>
            <span>Total: <strong class="text-gray-800">{{ $stats['total_reviews'] }}</strong></span>
            <span>Active: <strong class="text-emerald-600">{{ $stats['active_reviews'] }}</strong></span>
            <span>Pending: <strong class="text-amber-500">{{ $stats['pending_reviews'] }}</strong></span>
            <span>Avg: <strong class="text-yellow-500">{{ number_format($stats['average_rating'], 1) }}
                    ★</strong></span>
        </div>

        {{-- ── FILTERS (attached to header) ── --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                {{-- Search --}}
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search school name..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                </div>

                {{-- Status --}}
                <select wire:model.live="statusFilter"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="2">Pending</option>
                    <option value="3">Archived</option>
                </select>

                {{-- Rating --}}
                <select wire:model.live="ratingFilter"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Ratings</option>
                    <option value="5">★★★★★ 5 Stars</option>
                    <option value="4">★★★★☆ 4 Stars</option>
                    <option value="3">★★★☆☆ 3 Stars</option>
                    <option value="2">★★☆☆☆ 2 Stars</option>
                    <option value="1">★☆☆☆☆ 1 Star</option>
                </select>

                {{-- Clear --}}
                @if ($search || $statusFilter || $ratingFilter)
                    <button wire:click="resetFilters"
                        class="inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm
                               text-red-600 border border-red-200 bg-red-50 hover:bg-red-100
                               rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </button>
                @else
                    <div></div>
                @endif
            </div>
        </div>
    </div>

    <div class="p-4 sm:p-6">

        {{-- ══════════ TABLE ══════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                School</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Rating</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Feedback</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($reviews as $review)
                            <tr class="hover:bg-gray-50/70 transition-colors">

                                {{-- School --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        @if ($review->organization?->logo)
                                            <img src="{{ $review->organization->logo }}"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-bold text-indigo-600">
                                                    {{ strtoupper(substr($review->organization?->name ?? 'S', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate max-w-[140px]">
                                                {{ $review->organization?->name ?? '—' }}
                                            </p>
                                            <p class="text-xs text-gray-400 truncate max-w-[140px]">
                                                {{ $review->organization?->email ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Rating --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center gap-0.5">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                viewBox="0 0 20 20" fill="currentColor">
                                                <path
                                                    d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                        <span
                                            class="ml-1 text-xs text-gray-500 font-medium">{{ $review->rating }}/5</span>
                                    </div>
                                </td>

                                {{-- Feedback --}}
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-600 truncate max-w-[200px]"
                                        title="{{ $review->feedback }}">
                                        {{ $review->feedback }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $statusColor = $this->getStatusColor($review->status);
                                        $statusLabel = $this->getStatusLabel($review->status);
                                    @endphp
                                    <span
                                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium
                                        {{ $review->status == 1 ? 'bg-green-50 text-green-700 border border-green-100' : '' }}
                                        {{ $review->status == 2 ? 'bg-amber-50 text-amber-700 border border-amber-100' : '' }}
                                        {{ $review->status == 3 ? 'bg-gray-100 text-gray-600 border border-gray-200' : '' }}">
                                        <span
                                            class="w-1.5 h-1.5 rounded-full
                                            {{ $review->status == 1 ? 'bg-green-500' : '' }}
                                            {{ $review->status == 2 ? 'bg-amber-400' : '' }}
                                            {{ $review->status == 3 ? 'bg-gray-400' : '' }}">
                                        </span>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-xs font-medium text-gray-700">
                                        {{ \Carbon\Carbon::parse($review->created_at)->timezone('Asia/Kolkata')->format('d M Y') }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($review->created_at)->timezone('Asia/Kolkata')->format('h:i A') }}
                                        IST
                                    </p>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <button wire:click="viewReview({{ $review->id }})"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <select wire:change="updateStatus({{ $review->id }}, $event.target.value)"
                                            class="text-xs border border-gray-300 rounded-lg px-2 py-1.5
                                                   focus:ring-2 focus:ring-blue-500 bg-white text-gray-700">
                                            <option value="1" {{ $review->status == 1 ? 'selected' : '' }}>Active
                                            </option>
                                            <option value="2" {{ $review->status == 2 ? 'selected' : '' }}>
                                                Pending</option>
                                            <option value="3" {{ $review->status == 3 ? 'selected' : '' }}>
                                                Archived</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-14 text-center">
                                    <div
                                        class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">No reviews found</p>
                                    @if ($search || $statusFilter || $ratingFilter)
                                        <button wire:click="resetFilters"
                                            class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                            Clear filters
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($reviews->hasPages())
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing <strong class="text-gray-700">{{ $reviews->firstItem() }}</strong>
                        to <strong class="text-gray-700">{{ $reviews->lastItem() }}</strong>
                        of <strong class="text-gray-700">{{ $reviews->total() }}</strong> reviews
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($reviews->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&laquo;
                                Prev</span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                &laquo; Prev
                            </button>
                        @endif
                        @foreach ($reviews->getUrlRange(max(1, $reviews->currentPage() - 2), min($reviews->lastPage(), $reviews->currentPage() + 2)) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors
                                    {{ $page == $reviews->currentPage()
                                        ? 'bg-blue-600 text-white border border-blue-600'
                                        : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        @if ($reviews->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Next &raquo;
                            </button>
                        @else
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">Next
                                &raquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════ VIEW RATING SLIDE-IN PANEL ══════════ --}}
    @if ($selectedReview)
        <div class="fixed inset-0 z-[9999] flex items-start justify-end bg-black/30 backdrop-blur-sm">
            <div class="relative w-full max-w-md h-screen bg-white shadow-2xl flex flex-col">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-yellow-400 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Review Details</h2>
                            <p class="text-xs text-gray-500">School feedback & rating</p>
                        </div>
                    </div>
                    <button wire:click="closeReview" type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body (scrollable) --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5">

                    {{-- School Info --}}
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                        @if ($selectedReview->organization?->logo)
                            <img src="{{ $selectedReview->organization->logo }}"
                                class="w-14 h-14 rounded-full object-cover border-2 border-white shadow flex-shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0 shadow">
                                <span class="text-xl font-bold text-indigo-600">
                                    {{ strtoupper(substr($selectedReview->organization?->name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $selectedReview->organization?->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $selectedReview->organization?->email ?? '' }}</p>
                            <p class="text-xs text-blue-500 mt-0.5 font-medium">{{ $selectedReview->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="bg-yellow-50 border border-yellow-100 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Rating</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 {{ $i <= $selectedReview->rating ? 'text-yellow-400' : 'text-gray-200' }}"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                            <span class="text-2xl font-bold text-gray-800">
                                {{ $selectedReview->rating }}<span class="text-sm font-normal text-gray-400">/5</span>
                            </span>
                        </div>
                    </div>

                    {{-- Feedback --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Feedback</p>
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $selectedReview->feedback }}</p>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Status</p>
                        @php $sc = $this->getStatusColor($selectedReview->status); @endphp
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <span class="inline-flex items-center gap-2 text-sm font-semibold
                                {{ $selectedReview->status == 1 ? 'text-green-700' : '' }}
                                {{ $selectedReview->status == 2 ? 'text-amber-600' : '' }}
                                {{ $selectedReview->status == 3 ? 'text-gray-500' : '' }}">
                                <span class="w-2 h-2 rounded-full
                                    {{ $selectedReview->status == 1 ? 'bg-green-500' : '' }}
                                    {{ $selectedReview->status == 2 ? 'bg-amber-400' : '' }}
                                    {{ $selectedReview->status == 3 ? 'bg-gray-400' : '' }}">
                                </span>
                                {{ $this->getStatusLabel($selectedReview->status) }}
                            </span>
                        </div>
                    </div>

                    {{-- Date --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Date</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($selectedReview->created_at)->timezone('Asia/Kolkata')->format('d M Y') }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Time</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ \Carbon\Carbon::parse($selectedReview->created_at)->timezone('Asia/Kolkata')->format('h:i A') }}
                            </p>
                            <p class="text-xs text-gray-400">IST</p>
                        </div>
                    </div>

                    {{-- Change Status --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Change Status</p>
                        <select wire:change="updateStatus({{ $selectedReview->id }}, $event.target.value)"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="1" {{ $selectedReview->status == 1 ? 'selected' : '' }}>Active</option>
                            <option value="2" {{ $selectedReview->status == 2 ? 'selected' : '' }}>Pending</option>
                            <option value="3" {{ $selectedReview->status == 3 ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-white flex-shrink-0">
                    <button wire:click="closeReview" type="button"
                        class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
