<div class="min-h-screen bg-gray-50">

    {{-- ══════════ HEADER ══════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Support</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage queries from all school admins</p>
            </div>
            <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 divide-x divide-gray-200">
                <span class="pr-4">Total: <strong class="text-gray-800">{{ $totalQueries }}</strong></span>
                <span class="px-4">Replied: <strong class="text-emerald-600">{{ $repliedQueries }}</strong></span>
                <span class="px-4">Pending: <strong class="text-amber-500">{{ $pendingQueries }}</strong></span>
                <span class="pl-4">This Month: <strong class="text-blue-600">{{ $thisMonthQueries }}</strong></span>
            </div>
        </div>

        {{-- Mobile Stats --}}
        <div class="flex lg:hidden flex-wrap gap-3 text-xs text-gray-500 mt-3">
            <span>Total: <strong class="text-gray-800">{{ $totalQueries }}</strong></span>
            <span>Replied: <strong class="text-emerald-600">{{ $repliedQueries }}</strong></span>
            <span>Pending: <strong class="text-amber-500">{{ $pendingQueries }}</strong></span>
            <span>This Month: <strong class="text-blue-600">{{ $thisMonthQueries }}</strong></span>
        </div>

        {{-- ── FILTERS ── --}}
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">

                {{-- Search --}}
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Search topic, query, school..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                </div>

                {{-- Organization --}}
                <select wire:model.live="organizationFilter"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Schools</option>
                    @foreach ($organizations as $org)
                        <option value="{{ $org->id }}">{{ $org->name }}</option>
                    @endforeach
                </select>

                {{-- Status --}}
                <select wire:model.live="statusFilter"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="replied">Replied</option>
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
                    @if ($search || $organizationFilter || $statusFilter || $filterDays)
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

    <div class="p-4 sm:p-6 space-y-4">

        {{-- ══════════ TABLE ══════════ --}}
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
                                School</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Topic</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Query</th>
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
                        @forelse ($supports as $i => $support)
                            <tr class="hover:bg-gray-50/70 transition-colors">

                                {{-- # --}}
                                <td class="px-4 py-3 text-xs text-gray-400">
                                    {{ $supports->firstItem() + $i }}
                                </td>

                                {{-- School --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2.5">
                                        @if ($support->organization?->logo)
                                            <img src="{{ $support->organization->logo }}"
                                                class="w-8 h-8 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                        @else
                                            <div
                                                class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                                <span class="text-xs font-bold text-indigo-600">
                                                    {{ strtoupper(substr($support->organization?->name ?? 'S', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            {{-- School name from ticket's organization_id (always correct) --}}
                                            <p class="text-sm font-semibold text-gray-800 truncate max-w-[130px]">
                                                {{ $support->organization?->name ?? '—' }}
                                            </p>
                                            {{-- Admin who submitted --}}
                                            <p class="text-xs text-gray-400 truncate max-w-[130px]">
                                                {{ $support->user?->name ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Topic --}}
                                <td class="px-4 py-3">
                                    <p class="text-sm font-medium text-gray-800 truncate max-w-[140px]">
                                        {{ $support->topic }}
                                    </p>
                                    @if ($support->image)
                                        <span class="inline-flex items-center gap-1 text-xs text-purple-600 mt-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Attachment
                                        </span>
                                    @endif
                                </td>

                                {{-- Query --}}
                                <td class="px-4 py-3">
                                    <p class="text-sm text-gray-600 truncate max-w-[200px]"
                                        title="{{ $support->admin_query }}">
                                        {{ $support->admin_query }}
                                    </p>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($support->super_admin_reply)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full
                                                     font-medium bg-green-50 text-green-700 border border-green-100">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            Replied
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
                                        {{ $support->created_at->format('d M Y') }}
                                    </p>
                                    <p class="text-xs text-gray-400">{{ $support->created_at->diffForHumans() }}</p>
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="viewSupport({{ $support->id }})"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button wire:click="openReplyModal({{ $support->id }})"
                                            class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors"
                                            title="Reply">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                            </svg>
                                        </button>
                                        {{-- FIX 1: confirmDelete instead of deleteSupport directly --}}
                                        <button wire:click="confirmDelete({{ $support->id }})"
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
                                <td colspan="7" class="px-6 py-14 text-center">
                                    <div
                                        class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">No support queries found</p>
                                    @if ($search || $organizationFilter || $statusFilter || $filterDays)
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
            @if ($supports->hasPages())
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing <strong class="text-gray-700">{{ $supports->firstItem() }}</strong>
                        to <strong class="text-gray-700">{{ $supports->lastItem() }}</strong>
                        of <strong class="text-gray-700">{{ $supports->total() }}</strong> queries
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($supports->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&laquo;
                                Prev</span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                &laquo; Prev
                            </button>
                        @endif
                        @foreach ($supports->getUrlRange(max(1, $supports->currentPage() - 2), min($supports->lastPage(), $supports->currentPage() + 2)) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors
                                    {{ $page == $supports->currentPage()
                                        ? 'bg-blue-600 text-white border border-blue-600'
                                        : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                                {{ $page }}
                            </button>
                        @endforeach
                        @if ($supports->hasMorePages())
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

    {{-- ══════════ VIEW QUERY PANEL ══════════ --}}
    @if ($showDetailModal && $selectedSupport)
        <div class="fixed inset-0 z-[9999] flex items-start justify-end bg-black/30 backdrop-blur-sm"
            wire:click.self="closeDetailModal">
            <div class="relative w-full max-w-lg h-screen bg-white shadow-2xl flex flex-col"
                x-data x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100">

                {{-- Sticky Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">Support Query</h2>
                            <p class="text-xs text-gray-400">{{ $selectedSupport->organization?->name ?? '—' }}</p>
                        </div>
                        @if ($selectedSupport->super_admin_reply)
                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-semibold bg-green-100 text-green-700 border border-green-200">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>Replied
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                                <span class="w-1.5 h-1.5 bg-amber-400 rounded-full"></span>Pending
                            </span>
                        @endif
                    </div>
                    <button wire:click="closeDetailModal"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5">

                    {{-- School Card --}}
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        @if ($selectedSupport->organization?->logo)
                            <img src="{{ $selectedSupport->organization->logo }}"
                                class="w-14 h-14 rounded-2xl object-cover border border-gray-200 flex-shrink-0" alt="">
                        @else
                            <div class="w-14 h-14 rounded-2xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-xl font-bold text-indigo-600">
                                    {{ strtoupper(substr($selectedSupport->organization?->name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-base font-bold text-gray-900 truncate">{{ $selectedSupport->organization?->name ?? '—' }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $selectedSupport->user?->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $selectedSupport->user?->email ?? '—' }}</p>
                        </div>
                    </div>

                    {{-- Date Grid --}}
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-gray-400 font-medium mb-1">Submitted</p>
                            <p class="text-gray-800 font-semibold">{{ $selectedSupport->created_at->format('d M Y') }}</p>
                            <p class="text-gray-500">{{ $selectedSupport->created_at->format('h:i A') }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3 border border-gray-100">
                            <p class="text-gray-400 font-medium mb-1">Time Ago</p>
                            <p class="text-gray-800 font-semibold">{{ $selectedSupport->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    {{-- Topic --}}
                    <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                        <p class="text-xs text-blue-400 font-semibold uppercase tracking-wide mb-1">Topic</p>
                        <p class="text-sm font-bold text-blue-900">{{ $selectedSupport->topic }}</p>
                    </div>

                    {{-- Message --}}
                    <div>
                        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Message</p>
                        <div class="text-sm text-gray-700 whitespace-pre-line leading-relaxed bg-gray-50 rounded-xl p-4 border border-gray-100">
                            {{ $selectedSupport->admin_query }}
                        </div>
                    </div>

                    {{-- Attachment --}}
                    @if ($selectedSupport->image)
                        @php
                            $attUrl = $selectedSupport->image;
                            $attExt = strtolower(pathinfo(parse_url($attUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                        @endphp
                        <div>
                            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Attachment</p>
                            @if (in_array($attExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $attUrl }}" class="w-full rounded-xl border border-gray-200" alt="">
                                <a href="{{ $attUrl }}" target="_blank"
                                    class="text-xs text-blue-600 mt-1.5 inline-flex items-center gap-1 hover:underline">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                    </svg>
                                    Open full size
                                </a>
                            @elseif ($attExt === 'pdf')
                                <a href="{{ $attUrl }}" target="_blank"
                                    class="flex items-center gap-2 text-sm text-blue-600 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 hover:bg-blue-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    Open / Download PDF
                                </a>
                            @else
                                <a href="{{ $attUrl }}" target="_blank"
                                    class="flex items-center gap-2 text-sm text-purple-600 bg-purple-50 border border-purple-200 rounded-xl px-4 py-3 hover:bg-purple-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    View Attachment
                                </a>
                            @endif
                        </div>
                    @endif

                    {{-- Reply Section --}}
                    @if ($selectedSupport->super_admin_reply)
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs font-bold text-emerald-800">Super Admin Reply</p>
                                </div>
                                <p class="text-xs text-emerald-500">{{ $selectedSupport->updated_at->format('d M Y') }}</p>
                            </div>
                            <p class="text-sm text-emerald-900 whitespace-pre-line leading-relaxed">
                                {{ $selectedSupport->super_admin_text }}
                            </p>
                        </div>
                    @else
                        <div class="flex items-center gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-amber-700">Awaiting reply from Super Admin.</p>
                        </div>
                    @endif

                </div>

                {{-- Sticky Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-white flex gap-3 flex-shrink-0">
                    <button wire:click="openReplyModal({{ $selectedSupport->id }})"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        {{ $selectedSupport->super_admin_reply ? 'Update Reply' : 'Send Reply' }}
                    </button>
                    <button wire:click="closeDetailModal"
                        class="px-5 py-2.5 text-sm text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors flex-shrink-0">
                        Close
                    </button>
                </div>

            </div>
        </div>
    @endif


    {{-- ══════════ SEND REPLY PANEL ══════════ --}}
    @if ($showReplyModal && $selectedSupport)
        <div class="fixed inset-0 z-[9999] flex items-start justify-end bg-black/30 backdrop-blur-sm"
            wire:click.self="closeReplyModal">
            <div class="relative w-full max-w-lg h-screen bg-white shadow-2xl flex flex-col"
                x-data x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100">

                {{-- Sticky Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-gray-900">
                                {{ $selectedSupport->super_admin_reply ? 'Update Reply' : 'Send Reply' }}
                            </h2>
                            <p class="text-xs text-gray-400">{{ $selectedSupport->organization?->name ?? '—' }}</p>
                        </div>
                    </div>
                    <button wire:click="closeReplyModal"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5">

                    {{-- Original Query Summary --}}
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Original Query</p>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 mb-1">{{ $selectedSupport->topic }}</p>
                        <p class="text-xs text-gray-500 line-clamp-4 leading-relaxed">{{ $selectedSupport->admin_query }}</p>
                        @if ($selectedSupport->image)
                            <a href="{{ $selectedSupport->image }}" target="_blank"
                                class="flex items-center gap-1 text-xs text-purple-600 hover:underline mt-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                View Attachment
                            </a>
                        @endif
                    </div>

                    {{-- Previous Reply (if updating) --}}
                    @if ($selectedSupport->super_admin_reply)
                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <p class="text-xs font-semibold text-blue-500 uppercase tracking-wide mb-2">Previous Reply</p>
                            <p class="text-sm text-blue-800 leading-relaxed line-clamp-4">{{ $selectedSupport->super_admin_text }}</p>
                        </div>
                    @endif

                    {{-- Reply Textarea --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-2">
                            Your Reply <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model.defer="superAdminReply" rows="8"
                            placeholder="Type your reply to the school admin here..."
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm
                                   focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400
                                   resize-none leading-relaxed transition-colors"></textarea>
                        @error('superAdminReply')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Sticky Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-white flex gap-3 flex-shrink-0">
                    <button wire:click="sendReply"
                        class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        {{ $selectedSupport->super_admin_reply ? 'Update Reply' : 'Send Reply' }}
                    </button>
                    <button wire:click="closeReplyModal"
                        class="px-5 py-2.5 text-sm text-gray-600 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors flex-shrink-0">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ══════════ FIX 1: DELETE CONFIRMATION POPUP ══════════ --}}
    @if ($confirmDeleteId)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[99999] px-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden" x-data
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">

                {{-- Icon + Content --}}
                <div class="p-6 text-center">
                    {{-- Red warning icon --}}
                    <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">Delete Support Ticket?</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        This ticket will be permanently deleted and cannot be recovered.
                        Are you sure you want to proceed?
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex border-t border-gray-100">
                    <button wire:click="cancelDelete"
                        class="flex-1 py-3.5 text-sm font-medium text-gray-600 hover:bg-gray-50
                               transition-colors border-r border-gray-100">
                        Cancel
                    </button>
                    <button wire:click="deleteSupport({{ $confirmDeleteId }})"
                        class="flex-1 py-3.5 text-sm font-semibold text-red-600 hover:bg-red-50
                               transition-colors">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
