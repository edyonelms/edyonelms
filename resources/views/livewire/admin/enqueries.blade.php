<div class="p-4">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6 p-4">
        <h2 class="text-2xl font-bold text-gray-800">Enquiries Management</h2>

        <!-- Search Box -->
        <div class="w-64 relative">
            <input wire:model.live="search" placeholder="Search enquiries..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="flex justify-center items-center mb-6 p-4">
        <div class="flex gap-2 bg-white p-1 rounded-lg shadow-md border border-gray-200">
            @foreach (['teacher', 'student', 'website'] as $tab)
                <button
                    class="px-6 py-2 rounded-md transition-all {{ $activeTab === $tab ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-gray-100' }} font-medium"
                    wire:click="showTab('{{ $tab }}')">
                    {{ $this->getTabTitle($tab) }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-700">Filter by:</span>

            <!-- Days Filter -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Last:</span>
                <div class="flex gap-1">
                    @foreach ([7, 15, 30] as $days)
                        <button wire:click="applyFilter('days', {{ $days }})"
                            class="px-3 py-1 text-sm rounded-md transition-colors {{ $filterDays == $days ? 'bg-blue-100 text-blue-700 border border-blue-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $days }} days
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Months Filter -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Last:</span>
                <div class="flex gap-1">
                    @foreach ([3, 6] as $months)
                        <button wire:click="applyFilter('months', {{ $months }})"
                            class="px-3 py-1 text-sm rounded-md transition-colors {{ $filterMonths == $months ? 'bg-blue-100 text-blue-700 border border-blue-300' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            {{ $months }} months
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Status Filter (Only for Teacher and Student) -->
            @if ($this->hasReplyFunctionality($activeTab))
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-600">Status:</span>
                    <select wire:model.live="statusFilter"
                        class="text-sm border border-gray-300 rounded-md px-3 py-1 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="pending">Pending Reply</option>
                        <option value="replied">Replied</option>
                    </select>
                </div>
            @endif

            <!-- Clear Filters -->
            @if ($filterDays || $filterMonths || $search || $statusFilter)
                <button wire:click="clearFilters"
                    class="px-3 py-1 text-sm text-red-600 bg-red-50 rounded-md hover:bg-red-100 transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                    Clear Filters
                </button>
            @endif
        </div>
    </div>

    <!-- Enquiries List -->
    <div class="space-y-4">
        @forelse ($enquiries as $enquiry)
            <div
                class="p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200 border border-gray-100">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-3">
                            <!-- Enquiry Icon -->
                            <div class="flex-shrink-0">
                                <div
                                    class="p-2 {{ $enquiry->admin_reply ?? false ? 'bg-green-50' : 'bg-orange-50' }} rounded-lg">
                                    <svg class="h-5 w-5 {{ $enquiry->admin_reply ?? false ? 'text-green-600' : 'text-orange-600' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            </div>

                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="font-bold text-lg text-gray-900">
                                        @if ($activeTab === 'teacher' || $activeTab === 'student')
                                            {{ $enquiry->topic }}
                                        @else
                                            {{ $enquiry->full_name }} - {{ $enquiry->type }}
                                        @endif
                                    </h3>

                                    @if ($this->hasReplyFunctionality($activeTab))
                                        <span
                                            class="px-2 py-1 text-xs font-medium rounded-full {{ $enquiry->admin_reply ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $enquiry->admin_reply ? 'Replied' : 'Pending' }}
                                        </span>
                                    @endif

                                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                        {{ $enquiry->organization->name }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-2">
                                    @if ($activeTab === 'teacher')
                                        <div class="flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            <span>{{ $enquiry->user->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span>{{ $enquiry->user->email }}</span>
                                        </div>
                                        @if ($enquiry->teacherDetail)
                                            <div class="flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                <span>Teacher: {{ $enquiry->teacherDetail->name }}</span>
                                            </div>
                                        @endif
                                    @elseif($activeTab === 'student')
                                        <div class="flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            <span>{{ $enquiry->user->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span>{{ $enquiry->user->email }}</span>
                                        </div>
                                        @if ($enquiry->studentDetail)
                                            <div class="flex items-center gap-1">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                    </path>
                                                </svg>
                                                <span>Student: {{ $enquiry->studentDetail->name }}</span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                                </path>
                                            </svg>
                                            <span>{{ $enquiry->full_name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                            <span>{{ $enquiry->email }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                            <span>{{ $enquiry->mobile_number }}</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Query/Description Preview -->
                                <p class="text-gray-600 text-sm leading-relaxed line-clamp-2 mb-3">
                                    @if ($activeTab === 'teacher')
                                        {{ Str::limit($enquiry->teacher_query, 120) }}
                                    @elseif($activeTab === 'student')
                                        {{ Str::limit($enquiry->student_query, 120) }}
                                    @else
                                        {{ Str::limit($enquiry->description, 120) }}
                                    @endif
                                </p>

                                <!-- Meta Information -->
                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                    <div class="flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ $enquiry->created_at->format('M j, Y g:i A') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $enquiry->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                        <button wire:click="viewEnquiry({{ $enquiry->id }})"
                            class="px-3 py-2 text-blue-600 border border-blue-300 rounded-md hover:bg-blue-50 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            View
                        </button>

                        @if ($this->hasReplyFunctionality($activeTab) && !($enquiry->admin_reply ?? false))
                            <button wire:click="openReplyModal({{ $enquiry->id }})"
                                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                </svg>
                                Reply
                            </button>
                        @endif

                        <button wire:click="deleteEnquiry({{ $enquiry->id }})"
                            class="px-3 py-2 text-red-600 border border-red-300 rounded-md hover:bg-red-50 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-20 h-20 mx-auto mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No {{ $this->getTabTitle($activeTab) }} found
                </h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    {{ $filterDays || $filterMonths || $search || $statusFilter ? 'Try adjusting your filters or search terms.' : 'All ' . strtolower($this->getTabTitle($activeTab)) . ' will appear here.' }}
                </p>
                @if ($filterDays || $filterMonths || $search || $statusFilter)
                    <button wire:click="clearFilters"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-md hover:shadow-lg transition-all duration-200 inline-flex items-center gap-2">
                        Clear Filters
                    </button>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $enquiries->links() }}
    </div>

    {{-- ══════════ VIEW ENQUIRY MODAL ══════════ --}}
    <x-view-modal :show="$showDetailModal && $selectedEnquiry !== null"
        title="{{ $this->getTabTitle($activeTab) }} Details"
        closeAction="closeDetailModal">

        @if ($selectedEnquiry)
            {{-- Status badge --}}
            <div class="flex items-center justify-between mb-4">
                @if ($this->hasReplyFunctionality($activeTab))
                    <span class="text-xs px-3 py-1 rounded-full font-medium border
                        {{ ($selectedEnquiry->admin_reply ?? false)
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-100'
                            : 'bg-amber-50 text-amber-700 border-amber-100' }}">
                        {{ ($selectedEnquiry->admin_reply ?? false) ? 'Replied' : 'Pending' }}
                    </span>
                @else
                    <span class="text-xs px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-100 font-medium">
                        Website Enquiry
                    </span>
                @endif
                <span class="text-xs text-gray-400">
                    {{ $selectedEnquiry->created_at->format('d M Y, g:i A') }} · {{ $selectedEnquiry->created_at->diffForHumans() }}
                </span>
            </div>

            {{-- Sender info --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-4 text-left">
                @if ($activeTab === 'teacher' || $activeTab === 'student')
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Name</p>
                            <p class="font-semibold text-gray-800">{{ $selectedEnquiry->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Email</p>
                            <p class="font-medium text-gray-700">{{ $selectedEnquiry->user->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">School</p>
                            <p class="font-medium text-gray-700">{{ $selectedEnquiry->organization->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Topic</p>
                            <p class="font-semibold text-gray-800">{{ $selectedEnquiry->topic }}</p>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Name</p>
                            <p class="font-semibold text-gray-800">{{ $selectedEnquiry->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Type</p>
                            <p class="font-medium text-gray-700">{{ $selectedEnquiry->type }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Email</p>
                            <p class="font-medium text-gray-700">{{ $selectedEnquiry->email }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-0.5">Mobile</p>
                            <p class="font-medium font-mono text-gray-700">{{ $selectedEnquiry->mobile_number ?? '—' }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Message --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-4 text-left">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    @if ($activeTab === 'teacher') Teacher Query
                    @elseif ($activeTab === 'student') Student Query
                    @else Description
                    @endif
                </p>
                <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                    @if ($activeTab === 'teacher') {{ $selectedEnquiry->teacher_query }}
                    @elseif ($activeTab === 'student') {{ $selectedEnquiry->student_query }}
                    @else {{ $selectedEnquiry->description }}
                    @endif
                </p>
            </div>

            {{-- Admin Reply (if exists) --}}
            @if (($selectedEnquiry->admin_reply ?? false) && $this->hasReplyFunctionality($activeTab))
                <div class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 text-left">
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider mb-2">Admin Reply</p>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $selectedEnquiry->admin_reply }}</p>
                </div>
            @endif
        @endif

        <x-slot:footer>
            <div class="flex items-center gap-2">
                @if ($selectedEnquiry && $this->hasReplyFunctionality($activeTab) && !($selectedEnquiry->admin_reply ?? false))
                    <button wire:click="openReplyModal({{ $selectedEnquiry->id }})"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                               text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Reply
                    </button>
                @endif
                <button wire:click="closeDetailModal"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Close
                </button>
            </div>
        </x-slot:footer>
    </x-view-modal>

    {{-- ══════════ REPLY MODAL ══════════ --}}
    @if ($showReplyModal && $selectedEnquiry && $this->hasReplyFunctionality($activeTab))
        <div class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh] pointer-events-auto">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-base font-semibold text-gray-900">Reply to Enquiry</h3>
                    <button wire:click="closeReplyModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">
                    {{-- Original enquiry --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Topic</p>
                        <p class="text-sm font-semibold text-gray-800 mb-2">{{ $selectedEnquiry->topic }}</p>
                        <p class="text-xs text-gray-500 mb-2">
                            From: <strong>{{ $selectedEnquiry->user->name }}</strong>
                            ({{ $selectedEnquiry->organization->name }})
                        </p>
                        <div class="bg-white border border-gray-200 rounded-lg p-3">
                            <p class="text-sm text-gray-700 whitespace-pre-line">
                                @if ($activeTab === 'teacher') {{ $selectedEnquiry->teacher_query }}
                                @else {{ $selectedEnquiry->student_query }}
                                @endif
                            </p>
                        </div>
                    </div>
                    {{-- Reply textarea --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">
                            Your Reply <span class="text-red-400">*</span>
                        </label>
                        <textarea wire:model="adminReply" rows="6"
                            placeholder="Type your reply here..."
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg resize-none
                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"></textarea>
                        @error('adminReply') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 flex-shrink-0">
                    <button wire:click="closeReplyModal"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="sendReply"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold
                               bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        Send Reply
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
