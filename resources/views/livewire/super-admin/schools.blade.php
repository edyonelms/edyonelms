<div class="min-h-screen bg-gray-50">

    {{-- ══════════ LIST VIEW ══════════ --}}
    @if ($activeView === 'list')

        {{-- ── HEADER ── --}}
        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Schools</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Manage all registered schools</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                        <span class="pr-4">Total: <strong class="text-gray-800">{{ $totalSchools }}</strong></span>
                        <span class="px-4">Active: <strong
                                class="text-emerald-600">{{ $activeSchools }}</strong></span>
                        <span class="px-4">Pending: <strong
                                class="text-amber-500">{{ $pendingSchools }}</strong></span>
                        <span class="px-4">Students: <strong
                                class="text-blue-600">{{ $totalStudents }}</strong></span>
                        <span class="px-4">Teachers: <strong
                                class="text-purple-600">{{ $totalTeachers }}</strong></span>
                        <span class="pl-4">Avg Students: <strong
                                class="text-gray-800">{{ $avgStudents }}</strong></span>
                    </div>
                    <button wire:click="openModal"
                        class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700
                               text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Add School</span>
                    </button>
                </div>
            </div>
            {{-- Mobile stats --}}
            <div class="flex lg:hidden flex-wrap items-center gap-3 text-xs text-gray-500 mt-3">
                <span>Total: <strong class="text-gray-800">{{ $totalSchools }}</strong></span>
                <span>Active: <strong class="text-emerald-600">{{ $activeSchools }}</strong></span>
                <span>Pending: <strong class="text-amber-500">{{ $pendingSchools }}</strong></span>
                <span>Students: <strong class="text-blue-600">{{ $totalStudents }}</strong></span>
                <span>Teachers: <strong class="text-purple-600">{{ $totalTeachers }}</strong></span>
                <span>Avg: <strong class="text-gray-800">{{ $avgStudents }}</strong></span>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-5">

            {{-- ── SEARCH ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
                <div class="relative max-w-md">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Search by name, email, code, affiliation..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                </div>
            </div>

            {{-- ── SCHOOL CARDS ── --}}
            @if ($schools->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($schools as $school)
                        <div
                            class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md
                            transition-all duration-200 overflow-hidden flex flex-col">

                            <div
                                class="pt-5 pb-3 px-4 flex flex-col items-center text-center border-b border-gray-100 relative">
                                <span
                                    class="absolute top-3 right-3 w-2 h-2 rounded-full
                                    {{ $school->status ? 'bg-emerald-500' : 'bg-amber-400' }}"></span>

                                @if ($school->logo)
                                    <img src="{{ $school->logo }}"
                                        class="w-16 h-16 rounded-full object-cover border-2 border-gray-200 shadow-sm mb-2">
                                @else
                                    <div
                                        class="w-16 h-16 rounded-full bg-indigo-100 border-2 border-indigo-200
                                        flex items-center justify-center mb-2 shadow-sm">
                                        <span class="text-xl font-bold text-indigo-600">
                                            {{ strtoupper(substr($school->name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif

                                <h3 class="text-sm font-bold text-gray-900 leading-tight">{{ $school->name }}</h3>
                                <p class="text-xs text-gray-400 truncate w-full mt-0.5">{{ $school->email }}</p>
                            </div>

                            <div class="p-4 space-y-2 flex-1">
                                @if ($school->affiliation_no)
                                    <div class="flex items-center gap-2 text-xs text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <span class="truncate">Affiliation: <strong
                                                class="text-gray-700">{{ $school->affiliation_no }}</strong></span>
                                    </div>
                                @endif
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $school->mobile_number ?? '—' }}</span>
                                </div>
                                @if ($school->address)
                                    <div class="flex items-start gap-2 text-xs text-gray-500">
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0 mt-0.5" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="line-clamp-2">{{ $school->address }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center border-t border-gray-100 divide-x divide-gray-100">
                                <button wire:click="viewSchoolDetail({{ $school->id }})"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium
                                           text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    View
                                </button>
                                <button wire:click="onEdit({{ $school->id }})"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium
                                           text-amber-600 hover:bg-amber-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                                <button wire:click="onDelete({{ $school->id }})"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium
                                           text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ── PAGINATION ── --}}
                @if ($schools->hasPages())
                    <div
                        class="bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3
                        flex flex-col sm:flex-row items-center justify-between gap-3">
                        <p class="text-sm text-gray-500">
                            Showing <strong class="text-gray-700">{{ $schools->firstItem() }}</strong>
                            to <strong class="text-gray-700">{{ $schools->lastItem() }}</strong>
                            of <strong class="text-gray-700">{{ $schools->total() }}</strong> schools
                        </p>
                        <div class="flex items-center gap-1">
                            @if ($schools->onFirstPage())
                                <span
                                    class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">
                                    &laquo; Prev
                                </span>
                            @else
                                <button wire:click="previousPage"
                                    class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    &laquo; Prev
                                </button>
                            @endif

                            @foreach ($schools->getUrlRange(max(1, $schools->currentPage() - 2), min($schools->lastPage(), $schools->currentPage() + 2)) as $page => $url)
                                <button wire:click="gotoPage({{ $page }})"
                                    class="px-3 py-1.5 text-sm rounded-lg transition-colors
                                        {{ $page == $schools->currentPage()
                                            ? 'bg-blue-600 text-white border border-blue-600'
                                            : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                                    {{ $page }}
                                </button>
                            @endforeach

                            @if ($schools->hasMorePages())
                                <button wire:click="nextPage"
                                    class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Next &raquo;
                                </button>
                            @else
                                <span
                                    class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">
                                    Next &raquo;
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">No schools found</p>
                    @if ($search)
                        <button wire:click="$set('search', '')"
                            class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Clear search
                        </button>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════ DETAIL VIEW ══════════ --}}
    @if ($activeView === 'detail' && $detailSchool)

        {{-- ── DETAIL HEADER ── --}}
        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button wire:click="backToList"
                        class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-gray-200
                               text-gray-500 hover:text-gray-800 hover:bg-gray-50 text-sm transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="hidden sm:inline">Back</span>
                    </button>
                    <div class="flex items-center gap-3">
                        @if ($detailSchool->logo)
                            <img src="{{ $detailSchool->logo }}"
                                class="w-10 h-10 rounded-xl object-cover border border-gray-200">
                        @else
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                                <span class="text-sm font-bold text-indigo-600">
                                    {{ strtoupper(substr($detailSchool->name, 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <h1 class="text-lg font-bold text-gray-900 leading-none">{{ $detailSchool->name }}</h1>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $detailSchool->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-full font-medium
                        {{ $detailSchool->status
                            ? 'bg-green-50 text-green-700 border border-green-100'
                            : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                        <span
                            class="w-1.5 h-1.5 rounded-full {{ $detailSchool->status ? 'bg-green-500' : 'bg-amber-400' }}"></span>
                        {{ $detailSchool->status ? 'Active' : 'Pending' }}
                    </span>
                    <button wire:click="onEdit({{ $detailSchool->id }})"
                        class="px-3 py-1.5 text-sm font-medium text-amber-600 border border-amber-200
                               rounded-lg hover:bg-amber-50 transition-colors">
                        Edit
                    </button>
                    <button wire:click="onDelete({{ $detailSchool->id }})"
                        class="px-3 py-1.5 text-sm font-medium text-red-600 border border-red-200
                               rounded-lg hover:bg-red-50 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="p-4 sm:p-6 space-y-5">

            {{-- ── TABS ── --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                {{-- Tab Bar --}}
                <div class="flex border-b border-gray-200 overflow-x-auto">
                    @foreach ([
        'overview' => 'Overview',
        'bank' => 'Bank Details',
        'fees' => 'Fees Analytics',
        'students' => 'Students',
        'teachers' => 'Teachers',
    ] as $tab => $label)
                        <button wire:click="setDetailTab('{{ $tab }}')"
                            class="px-5 py-3 text-sm font-medium whitespace-nowrap transition-colors border-b-2
                                {{ $detailTab === $tab
                                    ? 'border-blue-600 text-blue-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- ════════ OVERVIEW TAB ════════ --}}
                @if ($detailTab === 'overview')
                    <div class="p-5">
                        {{-- Two-section field list --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-2">

                            {{-- School Information --}}
                            <div>
                                <p
                                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 pb-2 border-b border-gray-100">
                                    School Information
                                </p>
                                <div class="divide-y divide-gray-100">
                                    @foreach ([
        'name' => 'School Name',
        'email' => 'Email',
        'mobile_number' => 'Mobile',
        'state' => 'State',
        'address' => 'Address',
    ] as $field => $label)
                                        <div class="flex items-start gap-4 py-2.5">
                                            <span
                                                class="text-xs text-gray-400 w-28 flex-shrink-0 pt-0.5 leading-relaxed">
                                                {{ $label }}
                                            </span>
                                            <span class="text-sm text-gray-800 font-medium leading-relaxed">
                                                {{ $detailSchool->$field ?? '—' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Registration Details --}}
                            <div>
                                <p
                                    class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2 pb-2 border-b border-gray-100">
                                    Registration Details
                                </p>
                                <div class="divide-y divide-gray-100">
                                    @foreach ([
        'education_board' => 'Education Board',
        'school_code' => 'School Code',
        'affiliation_no' => 'Affiliation No',
        'udise_number' => 'UDISE Number',
        'serial_number' => 'Serial Number',
    ] as $field => $label)
                                        <div class="flex items-start gap-4 py-2.5">
                                            <span
                                                class="text-xs text-gray-400 w-28 flex-shrink-0 pt-0.5 leading-relaxed">
                                                {{ $label }}
                                            </span>
                                            <span class="text-sm text-gray-800 font-medium font-mono leading-relaxed">
                                                {{ $detailSchool->$field ?? '—' }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div>
                    </div>
                @endif

                {{-- ════════ BANK DETAILS TAB ════════ --}}
                @if ($detailTab === 'bank')
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Bank Account Details
                            </p>
                            <button wire:click="openBankModal"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-lg transition-colors
                                    {{ $detailSchool->bank_name
                                        ? 'text-amber-600 border border-amber-200 hover:bg-amber-50'
                                        : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $detailSchool->bank_name
                                            ? 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'
                                            : 'M12 4v16m8-8H4' }}" />
                                </svg>
                                {{ $detailSchool->bank_name ? 'Edit' : 'Add Bank Details' }}
                            </button>
                        </div>

                        @if ($detailSchool->bank_name)
                            <div class="divide-y divide-gray-100">
                                @foreach ([
        'Bank Name' => $detailSchool->bank_name,
        'Account Holder' => $detailSchool->bank_holder_name,
        'Account Number' => $detailSchool->bank_account_no,
        'IFSC Code' => $detailSchool->bank_ifsc,
        'Branch' => $detailSchool->bank_branch,
    ] as $label => $value)
                                    <div class="flex items-center gap-6 py-3">
                                        <span
                                            class="text-xs text-gray-400 w-32 flex-shrink-0">{{ $label }}</span>
                                        <span class="text-sm font-semibold text-gray-800 font-mono">
                                            {{ $value ?? '—' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="py-12 text-center text-sm text-gray-400">
                                No bank details added yet.
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ════════ FEES ANALYTICS TAB ════════ --}}
                @if ($detailTab === 'fees')
                    <div>
                        {{-- ── Overall Fee Analytics Cards ── --}}
                        <div class="p-5 border-b border-gray-100 bg-gray-50">
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                                {{-- Total Collected --}}
                                <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-1">
                                    <p class="text-xs text-gray-400 font-medium">Total Collected</p>
                                    <p class="text-xl font-bold text-emerald-600">
                                        ₹{{ number_format($feeStats['total_collected'] ?? 0, 0) }}
                                    </p>
                                </div>
                                {{-- This Month --}}
                                <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-1">
                                    <p class="text-xs text-gray-400 font-medium">This Month</p>
                                    <p class="text-xl font-bold text-blue-600">
                                        ₹{{ number_format($feeStats['this_month'] ?? 0, 0) }}
                                    </p>
                                </div>
                                {{-- Last Month --}}
                                <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-1">
                                    <p class="text-xs text-gray-400 font-medium">Last Month</p>
                                    <p class="text-xl font-bold text-purple-600">
                                        ₹{{ number_format($feeStats['last_month'] ?? 0, 0) }}
                                    </p>
                                </div>
                                {{-- This Year --}}
                                <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-1">
                                    <p class="text-xs text-gray-400 font-medium">This Year</p>
                                    <p class="text-xl font-bold text-indigo-600">
                                        ₹{{ number_format($feeStats['this_year'] ?? 0, 0) }}
                                    </p>
                                </div>
                                {{-- Total Transactions --}}
                                <div class="bg-white rounded-xl border border-gray-200 p-4 flex flex-col gap-1">
                                    <p class="text-xs text-gray-400 font-medium">Transactions</p>
                                    <p class="text-xl font-bold text-gray-800">
                                        {{ $feeStats['total_transactions'] ?? 0 }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Stats Strip (header-style, no chips) --}}
                        <div class="px-5 py-3 border-b border-gray-100 bg-white">
                            <div
                                class="flex flex-wrap items-center gap-1 text-sm text-gray-500 divide-x divide-gray-300">
                                <span class="pr-4">
                                    To Collect:
                                    <strong
                                        class="text-gray-800">₹{{ number_format($feeStats['total_to_collect'] ?? 0, 0) }}</strong>
                                </span>
                                <span class="px-4">
                                    Collected:
                                    <strong
                                        class="text-emerald-600">₹{{ number_format($feeStats['total_collected'] ?? 0, 0) }}</strong>
                                </span>
                                <span class="px-4">
                                    This Month:
                                    <strong
                                        class="text-blue-600">₹{{ number_format($feeStats['this_month'] ?? 0, 0) }}</strong>
                                </span>
                                <span class="px-4">
                                    Last Month:
                                    <strong
                                        class="text-purple-600">₹{{ number_format($feeStats['last_month'] ?? 0, 0) }}</strong>
                                </span>
                                <span class="pl-4">
                                    Transactions:
                                    <strong class="text-gray-800">{{ $feeStats['total_transactions'] ?? 0 }}</strong>
                                </span>
                            </div>
                        </div>

                        <div class="p-5 space-y-5">

                            {{-- Fee Type + Monthly Chart --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                {{-- Fee Type Breakdown --}}
                                <div class="bg-white border border-gray-200 rounded-xl p-4">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">
                                        By Fee Type
                                    </p>
                                    @php
                                        $academic = $feeStats['academic_total'] ?? 0;
                                        $transport = $feeStats['transport_total'] ?? 0;
                                        $total = $academic + $transport;
                                        $acPct = $total > 0 ? round(($academic / $total) * 100) : 0;
                                        $trPct = $total > 0 ? round(($transport / $total) * 100) : 0;
                                    @endphp
                                    <div class="space-y-3">
                                        <div>
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-gray-600">Academic</span>
                                                <span class="font-semibold text-gray-800">
                                                    ₹{{ number_format($academic, 0) }} ({{ $acPct }}%)
                                                </span>
                                            </div>
                                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-blue-500 rounded-full"
                                                    style="width: {{ $acPct }}%"></div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="text-gray-600">Transport</span>
                                                <span class="font-semibold text-gray-800">
                                                    ₹{{ number_format($transport, 0) }} ({{ $trPct }}%)
                                                </span>
                                            </div>
                                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-amber-500 rounded-full"
                                                    style="width: {{ $trPct }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-3 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                                        <span class="text-gray-400">This Month Transactions</span>
                                        <span
                                            class="font-semibold text-gray-700">{{ $feeStats['this_month_count'] ?? 0 }}</span>
                                    </div>
                                </div>

                                {{-- Financial Year Monthly Chart (Apr 2026 → Mar 2027) --}}
                                <div class="bg-white border border-gray-200 rounded-xl p-4">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                                        Monthly Collections
                                    </p>
                                    <p class="text-[10px] text-gray-400 mb-3">FY Apr 2026 – Mar 2027</p>

                                    @php
                                        $fyMonths = [
                                            ['label' => 'Apr', 'month' => 4, 'year' => 2026],
                                            ['label' => 'May', 'month' => 5, 'year' => 2026],
                                            ['label' => 'Jun', 'month' => 6, 'year' => 2026],
                                            ['label' => 'Jul', 'month' => 7, 'year' => 2026],
                                            ['label' => 'Aug', 'month' => 8, 'year' => 2026],
                                            ['label' => 'Sep', 'month' => 9, 'year' => 2026],
                                            ['label' => 'Oct', 'month' => 10, 'year' => 2026],
                                            ['label' => 'Nov', 'month' => 11, 'year' => 2026],
                                            ['label' => 'Dec', 'month' => 12, 'year' => 2026],
                                            ['label' => 'Jan', 'month' => 1, 'year' => 2027],
                                            ['label' => 'Feb', 'month' => 2, 'year' => 2027],
                                            ['label' => 'Mar', 'month' => 3, 'year' => 2027],
                                        ];
                                        $fyChart = $feeStats['fy_monthly_chart'] ?? [];
                                        $maxVal = !empty($fyChart) ? max(array_values($fyChart)) : 1;
                                        $maxVal = max($maxVal, 1);
                                        $nowMonth = now()->month;
                                        $nowYear = now()->year;
                                    @endphp

                                    <div class="flex items-end gap-0.5 h-24">
                                        @foreach ($fyMonths as $m)
                                            @php
                                                $key = "{$m['year']}-{$m['month']}";
                                                $val = $fyChart[$key] ?? 0;
                                                $pct = ($val / $maxVal) * 100;
                                                $barH = max(3, round($pct * 0.82));
                                                $isCurrent = $m['month'] == $nowMonth && $m['year'] == $nowYear;
                                                $color = $isCurrent ? '#3b82f6' : ($val > 0 ? '#bfdbfe' : '#f3f4f6');
                                            @endphp
                                            <div class="flex-1 flex flex-col items-center gap-0.5">
                                                {{-- Amount label above bar --}}
                                                @if ($val > 0)
                                                    <span
                                                        class="text-[7px] text-gray-500 w-full text-center truncate leading-none mb-0.5">
                                                        ₹{{ number_format($val / 1000, 0) }}k
                                                    </span>
                                                @else
                                                    <span
                                                        class="text-[7px] text-transparent leading-none mb-0.5">—</span>
                                                @endif
                                                <div class="w-full rounded-sm transition-all"
                                                    style="height: {{ $barH }}px; background-color: {{ $color }};">
                                                </div>
                                                <span
                                                    class="text-[8px] text-gray-400 mt-0.5">{{ $m['label'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Recent Payments Table --}}
                            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <h4 class="text-sm font-semibold text-gray-700">Recent Payments</h4>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead class="bg-gray-50 border-b border-gray-200">
                                            <tr>
                                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">
                                                    Receipt</th>
                                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">
                                                    Student</th>
                                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">
                                                    Type</th>
                                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">
                                                    Amount</th>
                                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">
                                                    Mode</th>
                                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">
                                                    Date</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @forelse ($feeStats['recent_payments'] ?? [] as $payment)
                                                <tr class="hover:bg-gray-50/50">
                                                    <td class="px-3 py-2.5 text-xs font-mono text-gray-600">
                                                        {{ $payment->receipt_number }}
                                                    </td>
                                                    <td class="px-3 py-2.5">
                                                        <p class="text-xs font-medium text-gray-800">
                                                            {{ $payment->studentDetail?->full_name ?? '—' }}
                                                        </p>
                                                        <p class="text-xs text-gray-400">
                                                            {{ $payment->standard?->name }}
                                                            {{ $payment->section ? ' / ' . $payment->section->name : '' }}
                                                        </p>
                                                    </td>
                                                    <td class="px-3 py-2.5">
                                                        <span
                                                            class="text-xs px-2 py-0.5 rounded-full font-medium
                                                            {{ $payment->fee_type === 'academic'
                                                                ? 'bg-blue-50 text-blue-700 border border-blue-100'
                                                                : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                                            {{ ucfirst($payment->fee_type) }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-2.5 text-sm font-semibold text-emerald-700">
                                                        ₹{{ number_format($payment->amount, 0) }}
                                                    </td>
                                                    <td class="px-3 py-2.5 text-xs text-gray-600 capitalize">
                                                        {{ $payment->payment_mode ?? '—' }}
                                                    </td>
                                                    <td class="px-3 py-2.5 text-xs text-gray-500">
                                                        {{ $payment->payment_date?->format('d M Y') }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6"
                                                        class="px-3 py-8 text-center text-sm text-gray-400">
                                                        No fee payments recorded yet
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                @endif

                {{-- ════════ STUDENTS TAB ════════ --}}
                @if ($detailTab === 'students')
                    {{-- Analytics Strip --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 min-h-[44px]">
                        @if ($selectedStudentId && isset($studentStats['selected']) && $studentStats['selected'])
                            @php $sel = $studentStats['selected']; @endphp
                            <div class="flex flex-wrap items-center gap-1 text-sm text-gray-600">
                                <button wire:click="selectStudent({{ $selectedStudentId }})"
                                    class="flex items-center gap-1 text-xs text-blue-500 hover:text-blue-700 pr-3 mr-2 border-r border-gray-300 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    All Students
                                </button>
                                <div
                                    class="flex flex-wrap items-center gap-1 text-sm text-gray-500 divide-x divide-gray-300">
                                    <span class="pr-3">
                                        <strong class="text-gray-800">{{ $sel->full_name ?? '—' }}</strong>
                                    </span>
                                    <span class="px-3">
                                        Class: <strong class="text-blue-600">
                                            {{ $sel->standard?->name ?? '—' }}{{ $sel->section ? ' / ' . $sel->section->name : '' }}
                                        </strong>
                                    </span>
                                    <span class="px-3">
                                        Mobile: <strong
                                            class="text-gray-800 font-mono">{{ $sel->phone ?? '—' }}</strong>
                                    </span>
                                    <span class="px-3">
                                        Adm No: <strong
                                            class="text-gray-800 font-mono text-xs">{{ $sel->admission_no ?? '—' }}</strong>
                                    </span>
                                    <span class="px-3">
                                        Gender: <strong
                                            class="text-gray-800">{{ ucfirst($sel->gender ?? '—') }}</strong>
                                    </span>
                                    <span class="pl-3">
                                        Status:
                                        @if ($sel->user?->is_active)
                                            <strong class="text-emerald-600">Active</strong>
                                        @else
                                            <strong class="text-red-500">Inactive</strong>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @else
                            <div
                                class="flex flex-wrap items-center gap-1 text-sm text-gray-500 divide-x divide-gray-300">
                                <span class="pr-4">
                                    Total: <strong class="text-gray-800">{{ $studentStats['total'] ?? 0 }}</strong>
                                </span>
                                <span class="px-4">
                                    Active: <strong
                                        class="text-emerald-600">{{ $studentStats['active'] ?? 0 }}</strong>
                                </span>
                                <span class="px-4">
                                    Inactive: <strong
                                        class="text-red-500">{{ $studentStats['inactive'] ?? 0 }}</strong>
                                </span>
                                <span class="px-4">
                                    Male: <strong class="text-blue-600">{{ $studentStats['male'] ?? 0 }}</strong>
                                </span>
                                <span class="pl-4">
                                    Female: <strong class="text-pink-600">{{ $studentStats['female'] ?? 0 }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Student Table --}}
                    @php
                        $schoolStudents = \App\Models\Student\StudentDetail::with(['user', 'standard', 'section'])
                            ->where('organization_id', $detailSchool->id)
                            ->latest()
                            ->take(20)
                            ->get();
                    @endphp
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-gray-400">
                                Showing latest <strong class="text-gray-600">{{ $schoolStudents->count() }}</strong>
                                of <strong class="text-gray-600">{{ $detailSchool->total_students }}</strong> students
                                @if ($selectedStudentId)
                                    — <span class="text-blue-500">1 selected</span>
                                @endif
                            </p>
                            <button wire:click="openAddStudentPanel"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white
                                       bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Add Student
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-y border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">#</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Name</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Mobile
                                        </th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Class
                                        </th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Admission
                                            No</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($schoolStudents as $i => $s)
                                        <tr wire:click="selectStudent({{ $s->id }})"
                                            class="cursor-pointer transition-colors
                                                {{ $selectedStudentId == $s->id ? 'bg-blue-50 hover:bg-blue-50' : 'hover:bg-gray-50/70' }}">
                                            <td class="px-3 py-2.5 text-xs text-gray-400">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2.5">
                                                <div class="flex items-center gap-2">
                                                    @if ($selectedStudentId == $s->id)
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                                                    @endif
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">
                                                            {{ $s->full_name ?? '—' }}</p>
                                                        <p class="text-xs text-gray-400">{{ $s->user?->email ?? '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2.5 text-xs font-mono text-gray-600">
                                                {{ $s->phone ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5">
                                                <span
                                                    class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                                                    {{ $s->standard?->name ?? '—' }}
                                                    @if ($s->section)
                                                        / {{ $s->section->name }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="px-3 py-2.5 text-xs font-mono text-gray-700">
                                                {{ $s->admission_no ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5">
                                                @if ($s->user?->is_active)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                                        bg-green-50 text-green-700 rounded-full border border-green-100">
                                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                        Active
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                                        bg-red-50 text-red-600 rounded-full border border-red-100">
                                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-400">
                                                No students found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- ════════ TEACHERS TAB ════════ --}}
                @if ($detailTab === 'teachers')
                    {{-- Analytics Strip --}}
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 min-h-[44px]">
                        @if ($selectedTeacherId && isset($teacherStats['selected']) && $teacherStats['selected'])
                            @php $tsel = $teacherStats['selected']; @endphp
                            <div class="flex flex-wrap items-center gap-1 text-sm text-gray-600">
                                <button wire:click="selectTeacher({{ $selectedTeacherId }})"
                                    class="flex items-center gap-1 text-xs text-blue-500 hover:text-blue-700 pr-3 mr-2 border-r border-gray-300 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7" />
                                    </svg>
                                    All Teachers
                                </button>
                                <div
                                    class="flex flex-wrap items-center gap-1 text-sm text-gray-500 divide-x divide-gray-300">
                                    <span class="pr-3">
                                        <strong class="text-gray-800">{{ $tsel->user?->name ?? '—' }}</strong>
                                    </span>
                                    <span class="px-3">
                                        Emp ID: <strong
                                            class="text-gray-800 font-mono text-xs">{{ $tsel->employee_id ?? '—' }}</strong>
                                    </span>
                                    <span class="px-3">
                                        Mobile: <strong
                                            class="text-gray-800 font-mono">{{ $tsel->phone ?? '—' }}</strong>
                                    </span>
                                    <span class="px-3">
                                        Qualification: <strong
                                            class="text-gray-800">{{ $tsel->qualification ?? '—' }}</strong>
                                    </span>
                                    <span class="pl-3">
                                        Status:
                                        @if ($tsel->user?->is_active)
                                            <strong class="text-emerald-600">Active</strong>
                                        @else
                                            <strong class="text-red-500">Inactive</strong>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        @else
                            <div
                                class="flex flex-wrap items-center gap-1 text-sm text-gray-500 divide-x divide-gray-300">
                                <span class="pr-4">
                                    Total: <strong class="text-gray-800">{{ $teacherStats['total'] ?? 0 }}</strong>
                                </span>
                                <span class="px-4">
                                    Active: <strong
                                        class="text-emerald-600">{{ $teacherStats['active'] ?? 0 }}</strong>
                                </span>
                                <span class="pl-4">
                                    Inactive: <strong
                                        class="text-red-500">{{ $teacherStats['inactive'] ?? 0 }}</strong>
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Teachers Table --}}
                    @php
                        $schoolTeachers = \App\Models\Teacher\TeacherDetail::with(['user'])
                            ->where('organization_id', $detailSchool->id)
                            ->latest()
                            ->take(20)
                            ->get();
                    @endphp
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs text-gray-400">
                                Showing latest <strong class="text-gray-600">{{ $schoolTeachers->count() }}</strong>
                                of <strong class="text-gray-600">{{ $detailSchool->total_teachers }}</strong>
                                teachers
                                @if ($selectedTeacherId)
                                    — <span class="text-blue-500">1 selected</span>
                                @endif
                            </p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-y border-gray-200">
                                    <tr>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">#</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Name</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Employee ID</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Mobile</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Qualification</th>
                                        <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-500">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($schoolTeachers as $i => $t)
                                        <tr wire:click="selectTeacher({{ $t->id }})"
                                            class="cursor-pointer transition-colors
                                                {{ $selectedTeacherId == $t->id ? 'bg-blue-50 hover:bg-blue-50' : 'hover:bg-gray-50/70' }}">
                                            <td class="px-3 py-2.5 text-xs text-gray-400">{{ $i + 1 }}</td>
                                            <td class="px-3 py-2.5">
                                                <div class="flex items-center gap-2">
                                                    @if ($selectedTeacherId == $t->id)
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full bg-blue-500 flex-shrink-0"></span>
                                                    @endif
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-800">
                                                            {{ $t->user?->name ?? '—' }}</p>
                                                        <p class="text-xs text-gray-400">{{ $t->user?->email ?? '' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-2.5 text-xs font-mono text-gray-700">
                                                {{ $t->employee_id ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5 text-xs font-mono text-gray-600">
                                                {{ $t->phone ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5 text-xs text-gray-700">
                                                {{ $t->qualification ?? '—' }}
                                            </td>
                                            <td class="px-3 py-2.5">
                                                @if ($t->user?->is_active)
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                                        bg-green-50 text-green-700 rounded-full border border-green-100">
                                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                                        Active
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                                        bg-red-50 text-red-600 rounded-full border border-red-100">
                                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-3 py-8 text-center text-sm text-gray-400">
                                                No teachers found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

    {{-- ══════════ ADD / EDIT SCHOOL MODAL ══════════ --}}
    <x-modal-form :show="$showModal" :title="$editId ? 'Edit School' : 'Add School'" submitAction="saveSchool" :submitButton="$editId ? 'Update' : 'Create'"
        closeAction="closeModal">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">School Logo</label>
                @if ($editId && $existingLogo && !$logo)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ $existingLogo }}"
                            class="w-16 h-16 rounded-xl object-cover border border-gray-200">
                        <span class="text-xs text-gray-400">Current logo</span>
                    </div>
                @endif
                <input type="file" wire:model="logo" accept="image/*"
                    class="block w-full text-sm text-gray-500
                           file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                           file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100 transition-colors">
                @error('logo')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            <x-input wire:model.defer="schoolName" label="School Name *" placeholder="Enter school name" />
            <x-input wire:model.defer="email" label="Email *" placeholder="Enter email" />
            <x-input wire:model.defer="mobileNumber" label="Mobile Number *" placeholder="Enter mobile number" />
            <x-input wire:model.defer="state" label="State *" placeholder="Enter state" />
            <x-input wire:model.defer="educationBoard" label="Education Board *"
                placeholder="Enter education board" />
            <x-input wire:model.defer="schoolCode" label="Code *" placeholder="Enter school code" />
            <x-input wire:model.defer="affiliationNo" label="Affiliation No"
                placeholder="Enter affiliation number" />
            <x-input wire:model.defer="udiseNumber" label="UDISE Number" placeholder="Enter UDISE number" />
            <x-input wire:model.defer="serialNumber" label="Serial Number *" placeholder="Enter serial number" />
            <div class="sm:col-span-2">
                <x-textarea wire:model.defer="address" label="Address" placeholder="Enter address" rows="2" />
            </div>
        </div>
    </x-modal-form>

    {{-- ══════════ BANK DETAILS MODAL ══════════ --}}
    <x-modal-form :show="$showBankModal" :title="$editBankMode ? 'Edit Bank Details' : 'Add Bank Details'" submitAction="saveBankDetails" :submitButton="$editBankMode ? 'Update' : 'Save'"
        closeAction="closeBankModal">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <x-input wire:model.defer="bankHolderName" label="Account Holder Name *"
                placeholder="Enter holder name" />
            <x-input wire:model.defer="bankName" label="Bank Name *" placeholder="Enter bank name" />
            <x-input wire:model.defer="bankAccountNo" label="Account Number *" placeholder="Enter account number" />
            <x-input wire:model.defer="bankIfsc" label="IFSC Code *" placeholder="Enter IFSC code" />
            <div class="sm:col-span-2">
                <x-input wire:model.defer="bankBranch" label="Branch *" placeholder="Enter branch name" />
            </div>
        </div>
    </x-modal-form>

    {{-- ══════════ ADD STUDENT SLIDE-IN PANEL ══════════ --}}
    @if ($showAddStudentPanel)
        <div class="fixed inset-0 z-[9999]">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]"
                 wire:click="closeAddStudentPanel"></div>

            {{-- Panel --}}
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-lg bg-white shadow-2xl flex flex-col z-10">

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">Add Student</p>
                            <p class="text-xs text-gray-400">{{ $detailSchool->name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeAddStudentPanel"
                        class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Scrollable Body --}}
                <div class="flex-1 overflow-y-auto p-5 space-y-4">

                    {{-- Basic Info --}}
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Basic Information</p>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input wire:model.defer="stName" type="text" placeholder="Student's full name"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stName') border-red-400 @enderror" />
                            @error('stName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Email <span class="text-red-500">*</span></label>
                                <input wire:model.defer="stEmail" type="email" placeholder="student@email.com"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stEmail') border-red-400 @enderror" />
                                @error('stEmail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Mobile <span class="text-red-500">*</span></label>
                                <input wire:model.defer="stMobile" type="text" placeholder="10-digit number"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stMobile') border-red-400 @enderror" />
                                @error('stMobile') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Gender <span class="text-red-500">*</span></label>
                                <select wire:model.defer="stGender"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white @error('stGender') border-red-400 @enderror">
                                    <option value="">Select gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('stGender') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                                <input wire:model.defer="stDob" type="date"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stDob') border-red-400 @enderror" />
                                @error('stDob') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Family --}}
                    <div class="bg-gray-50 rounded-xl p-4 space-y-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Family Details</p>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Father's Name <span class="text-red-500">*</span></label>
                                <input wire:model.defer="stFatherName" type="text" placeholder="Father's name"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stFatherName') border-red-400 @enderror" />
                                @error('stFatherName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Mother's Name <span class="text-red-500">*</span></label>
                                <input wire:model.defer="stMotherName" type="text" placeholder="Mother's name"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stMotherName') border-red-400 @enderror" />
                                @error('stMotherName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Academic --}}
                    <div class="bg-blue-50 rounded-xl p-4 space-y-3">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Academic Details</p>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Class</label>
                                <select wire:model.live="stStandardId"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="">Select class</option>
                                    @foreach ($stStandards as $std)
                                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Section</label>
                                <select wire:model.defer="stSectionId"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white disabled:opacity-50"
                                    @disabled(!$stStandardId)>
                                    <option value="">Select section</option>
                                    @foreach ($stSections as $sec)
                                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Board <span class="text-red-500">*</span></label>
                                <input wire:model.defer="stBoard" type="text" placeholder="e.g. CBSE"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stBoard') border-red-400 @enderror" />
                                @error('stBoard') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date of Admission <span class="text-red-500">*</span></label>
                                <input wire:model.defer="stDateOfAdmission" type="date"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stDateOfAdmission') border-red-400 @enderror" />
                                @error('stDateOfAdmission') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Password note --}}
                    <div class="flex items-start gap-2 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                        <svg class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-amber-700">A random password will be generated and sent to the student's email automatically.</p>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex-shrink-0 px-5 py-4 border-t border-gray-100 flex items-center gap-2 bg-gray-50/50">
                    <button wire:click="saveStudent"
                        class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold
                               rounded-lg transition-colors flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Student
                    </button>
                    <button wire:click="closeAddStudentPanel"
                        class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
