<div class="p-6 space-y-6">
    {{-- Page Header --}}
    <div>
        <h1 class="text-2xl font-bold text-emerald-700">Accounts Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Overview of fee collection and student data</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Fee Collected --}}
        <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-lg bg-emerald-50">
                    <x-icon name="banknotes" class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Fee Collected</p>
                    <p class="text-xl font-bold text-emerald-700 mt-1">
                        <span class="text-sm font-normal">INR</span> {{ number_format($totalFeeCollected, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-lg bg-red-50">
                    <x-icon name="clock" class="w-6 h-6 text-red-500" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pending</p>
                    <p class="text-xl font-bold text-red-600 mt-1">
                        <span class="text-sm font-normal">INR</span> {{ number_format($totalPending, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Today's Collection --}}
        <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-lg bg-emerald-50">
                    <x-icon name="calendar" class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Today's Collection</p>
                    <p class="text-xl font-bold text-emerald-700 mt-1">
                        <span class="text-sm font-normal">INR</span> {{ number_format($todayCollection, 2) }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Total Students --}}
        <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-5">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-lg bg-emerald-50">
                    <x-icon name="academic-cap" class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Students</p>
                    <p class="text-xl font-bold text-emerald-700 mt-1">{{ number_format($totalStudents) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    {{-- <div class="bg-white rounded-xl shadow-sm border border-emerald-100 p-6">
        <h2 class="text-lg font-semibold text-emerald-700 mb-4">Quick Links</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            <a href="{{ route('accounts.fee-structure') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="document-text" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">Fee Structure</span>
            </a>
            <a href="{{ route('accounts.fee-submission') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="pencil-square" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">Fee Submission</span>
            </a>
            <a href="{{ route('accounts.view-fee') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="eye" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">View Fee</span>
            </a>
            <a href="{{ route('accounts.payments') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="credit-card" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">Payments</span>
            </a>
            <a href="{{ route('accounts.penalties') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="exclamation-triangle" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">Penalties</span>
            </a>
            <a href="{{ route('accounts.fee-cycles') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="arrow-path" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">Fee Cycles</span>
            </a>
            <a href="{{ route('accounts.report-card') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="chart-bar" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">Report Card</span>
            </a>
            <a href="{{ route('accounts.profile') }}"
               class="flex items-center gap-3 p-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 transition group">
                <x-icon name="user-circle" class="w-5 h-5 text-emerald-600 group-hover:text-emerald-700" />
                <span class="text-sm font-medium text-emerald-700">Profile</span>
            </a>
        </div>
    </div> --}}
</div>
