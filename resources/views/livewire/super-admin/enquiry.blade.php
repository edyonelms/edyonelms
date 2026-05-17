<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════════════════════════════════════════════
         STICKY HEADER
    ══════════════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-50">

        {{-- Title row + Analytics --}}
        <div class="px-4 sm:px-6 pt-4 sm:pt-5 pb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Enquiries</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage demo &amp; contact enquiries from website</p>
            </div>
            {{-- Desktop stats --}}
            <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 divide-x divide-gray-200">
                <span class="pr-4">Demo: <strong class="text-blue-700">{{ $analytics['demo'] }}</strong></span>
                <span class="px-4">Contact: <strong class="text-violet-700">{{ $analytics['contact'] }}</strong></span>
                <span class="px-4">Pending: <strong class="text-amber-500">{{ $analytics['demo_pending'] + $analytics['contact_pending'] }}</strong></span>
                <span class="px-4">Remarked: <strong class="text-emerald-600">{{ $analytics['demo_remarked'] + $analytics['contact_remarked'] }}</strong></span>
                <span class="pl-4">This Month: <strong class="text-blue-600">{{ $analytics['demo_this_month'] + $analytics['contact_this_month'] }}</strong></span>
            </div>
        </div>

        {{-- Mobile stats --}}
        <div class="flex lg:hidden flex-wrap items-center gap-3 text-xs text-gray-500 px-4 sm:px-6 pb-3">
            <span>Demo: <strong class="text-blue-700">{{ $analytics['demo'] }}</strong></span>
            <span>Contact: <strong class="text-violet-700">{{ $analytics['contact'] }}</strong></span>
            <span>Pending: <strong class="text-amber-500">{{ $analytics['demo_pending'] + $analytics['contact_pending'] }}</strong></span>
            <span>Remarked: <strong class="text-emerald-600">{{ $analytics['demo_remarked'] + $analytics['contact_remarked'] }}</strong></span>
            <span>This Month: <strong class="text-blue-600">{{ $analytics['demo_this_month'] + $analytics['contact_this_month'] }}</strong></span>
        </div>

        {{-- ── TABS ── --}}
        <div class="px-4 sm:px-6 flex gap-0 border-t border-gray-100">

            {{-- Demo Tab --}}
            <button wire:click="switchTab('demo')"
                class="group flex items-center gap-2 px-5 py-3 text-sm font-semibold transition-all duration-150
                       border-b-2 {{ $activeTab === 'demo' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                🎬 Demo
                <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                    {{ $activeTab === 'demo' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $analytics['demo'] }}
                </span>
            </button>

            {{-- Contact Tab --}}
            <button wire:click="switchTab('contact')"
                class="group flex items-center gap-2 px-5 py-3 text-sm font-semibold transition-all duration-150
                       border-b-2 {{ $activeTab === 'contact' ? 'border-violet-600 text-violet-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                ✉️ Contact
                <span class="text-xs px-1.5 py-0.5 rounded-full font-medium
                    {{ $activeTab === 'contact' ? 'bg-violet-100 text-violet-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $analytics['contact'] }}
                </span>
            </button>

        </div>

        {{-- ── FILTERS ── --}}
        <div class="px-4 sm:px-6 py-3 border-t border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                {{-- Search --}}
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Search name, email, school..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                </div>

                {{-- Status --}}
                <select wire:model.live="statusFilter"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="remarked">Remarked</option>
                </select>

                {{-- Date + Clear --}}
                <div class="flex gap-2">
                    <select wire:model.live="filterDays"
                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">All Time</option>
                        <option value="7">Last 7 days</option>
                        <option value="15">Last 15 days</option>
                        <option value="30">Last 30 days</option>
                    </select>
                    @if ($search || $statusFilter || $filterDays)
                        <button wire:click="clearFilters" title="Clear filters"
                            class="px-3 py-2 text-sm text-red-600 border border-red-200 bg-red-50
                                   hover:bg-red-100 rounded-lg transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TABLE
    ══════════════════════════════════════════════════════════ --}}
    <div class="p-4 sm:p-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                #</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Contact</th>
                            @if ($activeTab === 'demo')
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    School / Role</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Students</th>
                            @else
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    School</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Subject</th>
                            @endif
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
                        @forelse ($enquiries as $i => $enquiry)
                            <tr class="hover:bg-gray-50/70 transition-colors">

                                {{-- # --}}
                                <td class="px-4 py-3 text-xs text-gray-400">
                                    {{ $enquiries->firstItem() + $i }}
                                </td>

                                {{-- Name --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                    {{ $activeTab === 'demo' ? 'bg-blue-100' : 'bg-violet-100' }}">
                                            <span
                                                class="text-xs font-bold {{ $activeTab === 'demo' ? 'text-blue-600' : 'text-violet-600' }}">
                                                {{ strtoupper(substr($enquiry->full_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate max-w-[130px]">
                                                {{ $enquiry->full_name }}
                                            </p>
                                            @if ($activeTab === 'demo' && $enquiry->city)
                                                <p class="text-xs text-gray-400 truncate max-w-[130px]">
                                                    {{ $enquiry->city }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Contact --}}
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-700 truncate max-w-[160px]">{{ $enquiry->email }}</p>
                                    @if ($enquiry->phone_number ?? $enquiry->phone)
                                        <p class="text-xs text-gray-400">
                                            {{ $enquiry->phone_number ?? $enquiry->phone }}</p>
                                    @endif
                                </td>

                                {{-- School / Role --}}
                                <td class="px-4 py-3">
                                    @if ($enquiry->school_name)
                                        <p class="text-sm font-medium text-gray-800 truncate max-w-[140px]">
                                            {{ $enquiry->school_name }}</p>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                    @if ($activeTab === 'demo' && $enquiry->role)
                                        <p class="text-xs text-gray-400">{{ $enquiry->role }}</p>
                                    @endif
                                </td>

                                {{-- Students / Subject --}}
                                <td class="px-4 py-3">
                                    @if ($activeTab === 'demo')
                                        @if ($enquiry->no_of_students)
                                            <span
                                                class="inline-flex items-center gap-1 text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium">
                                                👥 {{ $enquiry->no_of_students }}
                                            </span>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    @else
                                        @if ($enquiry->subject)
                                            <p class="text-sm text-gray-700 truncate max-w-[140px]">
                                                {{ $enquiry->subject }}</p>
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($enquiry->remark)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full
                                                     font-medium bg-green-50 text-green-700 border border-green-100">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            Remarked
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full
                                                     font-medium bg-amber-50 text-amber-700 border border-amber-100">
                                            <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                {{-- Date --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <p class="text-xs font-medium text-gray-700">
                                        {{ $enquiry->created_at->format('d M Y') }}</p>
                                    <p class="text-xs text-gray-400">{{ $enquiry->created_at->diffForHumans() }}</p>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="viewEnquiry({{ $enquiry->id }})"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button wire:click="openRemarkModal({{ $enquiry->id }})"
                                            class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                            title="Add / Edit Remark">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteEnquiry({{ $enquiry->id }})"
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-14 text-center">
                                    <div
                                        class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">No enquiries found</p>
                                    @if ($search || $statusFilter || $filterDays)
                                        <button wire:click="clearFilters"
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
            @if ($enquiries->hasPages())
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing <strong class="text-gray-700">{{ $enquiries->firstItem() }}</strong>
                        to <strong class="text-gray-700">{{ $enquiries->lastItem() }}</strong>
                        of <strong class="text-gray-700">{{ $enquiries->total() }}</strong> enquiries
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($enquiries->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&laquo;
                                Prev</span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                &laquo; Prev
                            </button>
                        @endif
                        @foreach ($enquiries->getUrlRange(max(1, $enquiries->currentPage() - 2), min($enquiries->lastPage(), $enquiries->currentPage() + 2)) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors
                                    {{ $page == $enquiries->currentPage()
                                        ? 'bg-blue-600 text-white border border-blue-600'
                                        : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        @if ($enquiries->hasMorePages())
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


    {{-- ══════════════════════════════════════════════════════════
         VIEW SLIDE-IN PANEL
    ══════════════════════════════════════════════════════════ --}}
    @if ($showDetailModal && $selectedEnquiry)
        <div class="fixed inset-0 z-[9999] flex items-start justify-end bg-black/30 backdrop-blur-sm">
            <div class="relative w-full max-w-lg h-screen bg-white shadow-2xl flex flex-col">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center
                            {{ $activeTab === 'demo' ? 'bg-blue-600' : 'bg-violet-600' }}">
                            @if ($activeTab === 'demo')
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 10l4.553-2.069A1 1 0 0121 8.876V15.12a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Enquiry Details</h2>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-[10px] px-1.5 py-0.5 rounded-full font-semibold
                                    {{ $activeTab === 'demo' ? 'bg-blue-100 text-blue-700' : 'bg-violet-100 text-violet-700' }}">
                                    {{ $activeTab === 'demo' ? 'Demo' : 'Contact' }}
                                </span>
                                @if ($selectedEnquiry->remark)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold">✓ Remarked</span>
                                @else
                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">⏳ Pending</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button wire:click="closeDetailModal" type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Body (scrollable) --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5">

                    {{-- Person Info --}}
                    <div class="bg-gray-50 rounded-xl p-4 flex items-center gap-4">
                        <div class="w-14 h-14 rounded-full flex items-center justify-center flex-shrink-0 shadow
                            {{ $activeTab === 'demo' ? 'bg-blue-100' : 'bg-violet-100' }}">
                            <span class="text-xl font-bold {{ $activeTab === 'demo' ? 'text-blue-600' : 'text-violet-600' }}">
                                {{ strtoupper(substr($selectedEnquiry->full_name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-base font-bold text-gray-900 truncate">{{ $selectedEnquiry->full_name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $selectedEnquiry->email }}</p>
                            @if ($selectedEnquiry->phone_number ?? $selectedEnquiry->phone)
                                <p class="text-sm text-gray-500">{{ $selectedEnquiry->phone_number ?? $selectedEnquiry->phone }}</p>
                            @endif
                            <p class="text-xs text-blue-500 mt-0.5 font-medium">{{ $selectedEnquiry->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Contact Information --}}
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Contact Information</p>
                        <div class="grid grid-cols-2 gap-3">
                            @if ($selectedEnquiry->school_name)
                                <div class="col-span-2 bg-gray-50 rounded-xl border border-gray-200 p-3">
                                    <p class="text-xs text-gray-400 mb-0.5">School / Organisation</p>
                                    <p class="text-sm font-semibold text-gray-800">{{ $selectedEnquiry->school_name }}</p>
                                </div>
                            @endif
                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-3">
                                <p class="text-xs text-gray-400 mb-0.5">Date</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $selectedEnquiry->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-3">
                                <p class="text-xs text-gray-400 mb-0.5">Time</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $selectedEnquiry->created_at->format('h:i A') }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Demo Details --}}
                    @if ($activeTab === 'demo' && ($selectedEnquiry->city || $selectedEnquiry->no_of_students || $selectedEnquiry->role))
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Demo Details</p>
                            <div class="grid grid-cols-3 gap-3">
                                @if ($selectedEnquiry->city)
                                    <div class="bg-blue-50 rounded-xl border border-blue-100 p-3">
                                        <p class="text-xs text-gray-400 mb-0.5">City</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $selectedEnquiry->city }}</p>
                                    </div>
                                @endif
                                @if ($selectedEnquiry->no_of_students)
                                    <div class="bg-blue-50 rounded-xl border border-blue-100 p-3">
                                        <p class="text-xs text-gray-400 mb-0.5">Students</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $selectedEnquiry->no_of_students }}</p>
                                    </div>
                                @endif
                                @if ($selectedEnquiry->role)
                                    <div class="bg-blue-50 rounded-xl border border-blue-100 p-3">
                                        <p class="text-xs text-gray-400 mb-0.5">Role</p>
                                        <p class="text-sm font-semibold text-gray-800">{{ $selectedEnquiry->role }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @elseif ($activeTab === 'contact' && $selectedEnquiry->subject)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Contact Details</p>
                            <div class="bg-violet-50 rounded-xl border border-violet-100 p-4">
                                <p class="text-xs text-gray-400 mb-0.5">Subject</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $selectedEnquiry->subject }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Message --}}
                    @php $msg = $selectedEnquiry->description ?? $selectedEnquiry->message ?? null; @endphp
                    @if ($msg)
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Message</p>
                            <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $msg }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Remark --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Remark</p>
                            <button wire:click="openRemarkModal({{ $selectedEnquiry->id }})"
                                class="flex items-center gap-1.5 text-xs text-emerald-700 border border-emerald-200
                                       px-2.5 py-1 rounded-lg hover:bg-emerald-50 transition-colors font-semibold">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                {{ $selectedEnquiry->remark ? 'Edit Remark' : 'Add Remark' }}
                            </button>
                        </div>
                        @if ($selectedEnquiry->remark)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <p class="text-sm text-emerald-800 leading-relaxed whitespace-pre-line">{{ $selectedEnquiry->remark }}</p>
                            </div>
                        @else
                            <div class="bg-gray-50 border border-dashed border-gray-300 rounded-xl p-4 text-center">
                                <p class="text-xs text-gray-400">No remark added yet.</p>
                            </div>
                        @endif
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-white flex-shrink-0 flex items-center gap-3">
                    <button wire:click="closeDetailModal" type="button"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-colors">
                        Close
                    </button>
                    <button wire:click="deleteEnquiry({{ $selectedEnquiry->id }})"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-red-600 border border-red-200
                               rounded-xl hover:bg-red-50 transition-colors font-medium flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                    </button>
                </div>

            </div>
        </div>
    @endif


    {{-- ══════════════════════════════════════════════════════════
         REMARK MODAL
    ══════════════════════════════════════════════════════════ --}}
    @if ($showRemarkModal)
        <div class="fixed inset-0 z-[1000] flex items-center justify-center px-4 py-6"
            style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);" wire:click="closeRemarkModal">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg flex flex-col max-h-[90vh]" wire:click.stop>

                <div
                    class="flex-shrink-0 flex items-center justify-between px-5 py-4 border-b border-gray-100
                            bg-gradient-to-r from-emerald-50 to-teal-50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Add / Edit Remark</h3>
                            <p class="text-xs text-gray-400">Internal note for this enquiry</p>
                        </div>
                    </div>
                    <button wire:click="closeRemarkModal"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-5">
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Your Remark <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="remarkText" rows="6" placeholder="Write your remark here..."
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm
                               focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400
                               resize-none leading-relaxed
                               @error('remarkText') border-red-400 @enderror">
                    </textarea>
                    @error('remarkText')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-400 text-right mt-1">{{ strlen($remarkText) }} / 1000</p>
                </div>

                <div class="flex-shrink-0 px-5 pb-5 pt-3 border-t border-gray-100 flex items-center gap-2">
                    <button wire:click="saveRemark"
                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm
                               font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Save Remark
                    </button>
                    <button wire:click="closeRemarkModal"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm
                               font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
