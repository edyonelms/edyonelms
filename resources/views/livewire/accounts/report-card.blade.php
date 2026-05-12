<div class="min-h-screen bg-gray-50">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-6 pt-4 pb-3">
            {{-- Title row --}}
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-900 leading-tight">Report Card (Fee)</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Fee collection analytics and student-wise report</p>
                </div>
                {{-- Collection Rate pill --}}
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-2">
                        <div class="flex flex-col items-end">
                            <span class="text-[10px] font-medium text-emerald-600 uppercase tracking-wider">Collection Rate</span>
                            <div class="flex items-center gap-2 mt-0.5">
                                <div class="w-24 h-1.5 bg-emerald-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full transition-all"
                                        style="width: {{ min(100, $analytics['collectionRate'] ?? 0) }}%"></div>
                                </div>
                                <span class="text-sm font-bold text-emerald-700">{{ $analytics['collectionRate'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analytics Strip --}}
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
                {{-- Total Students --}}
                <div class="bg-gray-50 rounded-xl border border-gray-200 px-3 py-2.5">
                    <p class="text-[10px] font-medium text-gray-500 uppercase tracking-wider leading-none mb-1">Total Students</p>
                    <p class="text-lg font-bold text-gray-800 leading-tight">{{ $analytics['totalStudents'] ?? 0 }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Enrolled</p>
                </div>

                {{-- Fully Paid --}}
                <div class="bg-emerald-50 rounded-xl border border-emerald-200 px-3 py-2.5">
                    <p class="text-[10px] font-medium text-emerald-600 uppercase tracking-wider leading-none mb-1">Fully Paid</p>
                    <p class="text-lg font-bold text-emerald-700 leading-tight">{{ $analytics['paidFull'] ?? 0 }}</p>
                    <p class="text-[10px] text-emerald-500 mt-0.5">Students</p>
                </div>

                {{-- Partial Payment --}}
                <div class="bg-amber-50 rounded-xl border border-amber-200 px-3 py-2.5">
                    <p class="text-[10px] font-medium text-amber-600 uppercase tracking-wider leading-none mb-1">Partial</p>
                    <p class="text-lg font-bold text-amber-700 leading-tight">{{ $analytics['paidPartial'] ?? 0 }}</p>
                    <p class="text-[10px] text-amber-500 mt-0.5">Students</p>
                </div>

                {{-- Unpaid --}}
                <div class="bg-red-50 rounded-xl border border-red-200 px-3 py-2.5">
                    <p class="text-[10px] font-medium text-red-500 uppercase tracking-wider leading-none mb-1">Unpaid</p>
                    <p class="text-lg font-bold text-red-600 leading-tight">{{ $analytics['unpaid'] ?? 0 }}</p>
                    <p class="text-[10px] text-red-400 mt-0.5">Students</p>
                </div>

                {{-- Total Collected --}}
                <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] font-medium text-emerald-100 uppercase tracking-wider leading-none mb-1">Collected</p>
                    <p class="text-sm font-bold text-white leading-tight truncate">
                        ₹{{ number_format($analytics['totalCollected'] ?? 0, 0) }}
                    </p>
                    <p class="text-[10px] text-emerald-200 mt-0.5 truncate">
                        Pending ₹{{ number_format($analytics['totalPending'] ?? 0, 0) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Page body --}}
    <div class="px-6 py-5 space-y-4">

        {{-- Filter Bar --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Class</label>
                    <select wire:model.live="filterStandardId"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Classes</option>
                        @foreach($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Section</label>
                    <select wire:model.live="filterSectionId"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                    <select wire:model.live="filterStatus"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Statuses</option>
                        <option value="paid">Fully Paid</option>
                        <option value="partial">Partial</option>
                        <option value="unpaid">Unpaid</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Search Student</label>
                    <div class="relative">
                        <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Name or Admission No..."
                            class="w-full pl-8 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    </div>
                </div>
            </div>
        </div>

        {{-- "Feature coming soon" notification (shown via $showReportCardNotice) --}}
        @if($showReportCardNotice ?? false)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="text-sm font-medium text-amber-700">Feature coming soon — Report card issuance is under development.</p>
                </div>
                <button wire:click="$set('showReportCardNotice', false)" class="text-amber-500 hover:text-amber-700 ml-4">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Student Report Table --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800">Student Fee Report</h2>
                <span class="text-xs text-gray-500">{{ count($reportList) }} {{ Str::plural('student', count($reportList)) }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-emerald-50 border-b border-emerald-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700 whitespace-nowrap">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700 whitespace-nowrap">Student Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700 whitespace-nowrap">Adm No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700 whitespace-nowrap">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700 whitespace-nowrap">Section</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-emerald-700 whitespace-nowrap">Total Fee</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-emerald-700 whitespace-nowrap">Paid</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-emerald-700 whitespace-nowrap">Pending</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-700 whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-700 whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($reportList as $index => $item)
                            <tr class="hover:bg-emerald-50/30 transition-colors {{ $item['status'] === 'unpaid' ? 'opacity-50' : '' }}">
                                <td class="px-4 py-3 text-xs text-gray-400 tabular-nums">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-xs font-semibold text-gray-800">{{ $item['name'] }}</p>
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-emerald-700 font-medium">{{ $item['admission_no'] }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $item['class'] }}</td>
                                <td class="px-4 py-3 text-xs text-gray-700">{{ $item['section'] }}</td>
                                <td class="px-4 py-3 text-right text-xs font-medium text-gray-800 tabular-nums">
                                    ₹{{ number_format($item['totalFee'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-xs font-semibold text-emerald-600 tabular-nums">
                                    ₹{{ number_format($item['paid'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right text-xs font-semibold tabular-nums
                                    {{ $item['pending'] > 0 ? 'text-red-500' : 'text-gray-300' }}">
                                    {{ $item['pending'] > 0 ? '₹' . number_format($item['pending'], 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($item['status'] === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700">
                                            Paid
                                        </span>
                                    @elseif($item['status'] === 'partial')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-amber-100 text-amber-700">
                                            Partial
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-red-100 text-red-600">
                                            Unpaid
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button wire:click="issueReportCard({{ $item['id'] }})"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg border border-emerald-300 text-[11px] font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 transition-colors whitespace-nowrap">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                        </svg>
                                        Issue Report Card
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="w-7 h-7 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500">No students found</p>
                                            <p class="text-xs text-gray-400 mt-0.5">Try adjusting your filters</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
