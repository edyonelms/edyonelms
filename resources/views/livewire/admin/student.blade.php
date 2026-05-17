<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Students</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage student records and admissions</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                    <span class="pr-4">Total: <strong class="text-gray-800">{{ $totalStudents }}</strong></span>
                    <span class="px-4">Active: <strong class="text-emerald-600">{{ $activeStudents }}</strong></span>
                    <span class="px-4">Last Year: <strong
                            class="text-gray-800">{{ $lastYearStudents }}</strong></span>
                    <span class="pl-4">This Year: <strong
                            class="text-blue-600">{{ $thisYearStudents }}</strong></span>
                </div>
                <button wire:click="exportStudents"
                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-gray-100 hover:bg-gray-200
                           text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span class="hidden sm:inline">Export</span>
                </button>
                <button wire:click="onAddStudent"
                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Add Student</span>
                </button>
            </div>
        </div>

        {{-- Mobile / Tablet stats --}}
        <div class="flex lg:hidden items-center gap-3 sm:gap-4 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Total: <strong class="text-gray-800">{{ $totalStudents }}</strong></span>
            <span>Active: <strong class="text-emerald-600">{{ $activeStudents }}</strong></span>
            <span>Last Year: <strong class="text-gray-800">{{ $lastYearStudents }}</strong></span>
            <span>This Year: <strong class="text-blue-600">{{ $thisYearStudents }}</strong></span>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">

        {{-- ══════════════════════════════════════════════════
             FILTERS
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">

                {{-- Search --}}
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Search name, email, admission no, roll no, phone..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" />
                </div>

                {{-- Class --}}
                <select wire:model.live="filterClass"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">All Classes</option>
                    @foreach ($standards as $standard)
                        <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                    @endforeach
                </select>

                {{-- Section --}}
                <select wire:model.live="filterSection"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white
                           disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled(!$filterClass)>
                    <option value="">All Sections</option>
                    @foreach ($filterSections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </select>

                {{-- Gender --}}
                <select wire:model.live="filterGender"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Genders</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>

                {{-- Status + Clear --}}
                <div class="flex gap-2">
                    <select wire:model.live="filterStatus"
                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg
                               focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @if ($search || $filterClass || $filterSection || $filterGender || $filterStatus !== '')
                        <button wire:click="clearFilters" title="Clear all filters"
                            class="px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded-lg
                                   hover:bg-gray-50 transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             DESKTOP TABLE (hidden on mobile)
        ══════════════════════════════════════════════════ --}}
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                S.No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Student</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Mobile</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Email</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Admission No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Class</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($students as $index => $student)
                            <tr class="hover:bg-gray-50/70 transition-colors">

                                {{-- S.No --}}
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-500 font-medium">
                                        {{ $students->firstItem() + $index }}
                                    </span>
                                </td>

                                {{-- Student (Image + Name) --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($student->user?->image)
                                            <img src="{{ $student->user->image }}"
                                                class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0 cursor-pointer"
                                                wire:click="onImageClick({{ $student->user->id }})">
                                        @else
                                            <div
                                                class="w-9 h-9 rounded-full bg-indigo-100 flex items-center
                                                        justify-center flex-shrink-0">
                                                <span class="text-xs font-semibold text-indigo-600">
                                                    {{ strtoupper(substr($student->full_name ?? 'S', 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                {{ $student->full_name ?? '—' }}</p>
                                            <p class="text-xs text-gray-400 capitalize">{{ $student->gender ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Mobile --}}
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-700">{{ $student->phone ?? '—' }}</span>
                                </td>

                                {{-- Email --}}
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-600 truncate block max-w-[200px]">
                                        {{ $student->user?->email ?? '—' }}
                                    </span>
                                </td>

                                {{-- Admission No --}}
                                <td class="px-4 py-3">
                                    <span
                                        class="text-sm font-mono text-gray-700">{{ $student->admission_no ?? '—' }}</span>
                                </td>

                                {{-- Class / Section --}}
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @if ($student->standard)
                                            <span
                                                class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700
                                                rounded-full font-medium border border-blue-100">
                                                {{ $student->standard->name }}
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3">
                                    @if ($student->user?->is_active)
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1
                                            bg-green-50 text-green-700 rounded-full font-medium border border-green-100">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1
                                            bg-red-50 text-red-600 rounded-full font-medium border border-red-100">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="onViewStudentAdmin({{ $student->id }})"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button wire:click="onEditStudent({{ $student->id }})"
                                            class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="onDeleteStudent({{ $student->id }})"
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
                                <td colspan="8" class="px-6 py-16 text-center">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center
                                                justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">No students found</p>
                                    @if ($search || $filterClass || $filterGender || $filterStatus !== '')
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

            {{-- Pagination (Desktop) --}}
            @if ($students->hasPages())
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row
                            items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium text-gray-700">{{ $students->firstItem() }}</span>
                        to <span class="font-medium text-gray-700">{{ $students->lastItem() }}</span>
                        of <span class="font-medium text-gray-700">{{ $students->total() }}</span> students
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($students->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">
                                &laquo; Prev</span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300
                                       rounded-lg hover:bg-gray-50 transition-colors">&laquo;
                                Prev</button>
                        @endif

                        @foreach ($students->getUrlRange(max(1, $students->currentPage() - 2), min($students->lastPage(), $students->currentPage() + 2)) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm rounded-lg transition-colors
                                    {{ $page == $students->currentPage()
                                        ? 'bg-blue-600 text-white border border-blue-600'
                                        : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">
                                {{ $page }}
                            </button>
                        @endforeach

                        @if ($students->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300
                                       rounded-lg hover:bg-gray-50 transition-colors">Next
                                &raquo;</button>
                        @else
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">
                                Next &raquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════
             MOBILE CARDS (shown only on mobile)
        ══════════════════════════════════════════════════ --}}
        <div class="md:hidden space-y-3">
            @forelse ($students as $index => $student)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Card Header --}}
                    <div class="flex items-center gap-3 p-4 border-b border-gray-100">
                        <span class="text-xs font-bold text-gray-400 w-6 text-center">
                            {{ $students->firstItem() + $index }}
                        </span>
                        @if ($student->user?->image)
                            <img src="{{ $student->user->image }}"
                                class="w-10 h-10 rounded-full object-cover border border-gray-200 flex-shrink-0"
                                wire:click="onImageClick({{ $student->user->id }})">
                        @else
                            <div
                                class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-semibold text-indigo-600">
                                    {{ strtoupper(substr($student->full_name ?? 'S', 0, 1)) }}
                                </span>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $student->full_name ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $student->user?->email ?? '—' }}</p>
                        </div>
                        @if ($student->user?->is_active)
                            <span
                                class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                bg-green-50 text-green-700 rounded-full font-medium border border-green-100">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Active
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                bg-red-50 text-red-600 rounded-full font-medium border border-red-100">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                Inactive
                            </span>
                        @endif
                    </div>

                    {{-- Card Body --}}
                    <div class="px-4 py-3 space-y-2">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-xs text-gray-400">Mobile</p>
                                <p class="text-gray-700 font-medium">{{ $student->phone ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Admission No</p>
                                <p class="text-gray-700 font-mono text-xs font-medium">
                                    {{ $student->admission_no ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <p class="text-xs text-gray-400">Class/Section:</p>
                            <div class="flex gap-1.5">
                                @if ($student->standard)
                                    <span
                                        class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700
                                        rounded-full font-medium border border-blue-100">
                                        {{ $student->standard->name }}
                                    </span>
                                @endif
                                @if ($student->section)
                                    <span
                                        class="text-xs px-2 py-0.5 bg-purple-50 text-purple-700
                                        rounded-full font-medium border border-purple-100">
                                        {{ $student->section->name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Card Actions --}}
                    <div class="flex items-center border-t border-gray-100 divide-x divide-gray-100">
                        <button wire:click="onViewStudentAdmin({{ $student->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium
                                   text-blue-600 hover:bg-blue-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View
                        </button>
                        <button wire:click="onEditStudent({{ $student->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium
                                   text-amber-600 hover:bg-amber-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button wire:click="onDeleteStudent({{ $student->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium
                                   text-red-600 hover:bg-red-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm">No students found</p>
                    @if ($search || $filterClass || $filterGender || $filterStatus !== '')
                        <button wire:click="clearFilters"
                            class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Clear filters
                        </button>
                    @endif
                </div>
            @endforelse

            {{-- Pagination (Mobile) --}}
            @if ($students->hasPages())
                <div
                    class="flex items-center justify-between bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3">
                    <p class="text-xs text-gray-500">
                        {{ $students->firstItem() }}–{{ $students->lastItem() }} of {{ $students->total() }}
                    </p>
                    <div class="flex items-center gap-1">
                        @if (!$students->onFirstPage())
                            <button wire:click="previousPage"
                                class="px-2.5 py-1 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Prev
                            </button>
                        @endif
                        <span class="px-2.5 py-1 text-xs bg-blue-600 text-white rounded-lg">
                            {{ $students->currentPage() }}
                        </span>
                        @if ($students->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-2.5 py-1 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                                Next
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         ADD / EDIT MODAL
    ══════════════════════════════════════════════════ --}}
    @if ($open)
        <div class="fixed inset-0 z-[9999] flex items-start justify-end bg-black/30 backdrop-blur-sm pt-0">
            <div class="relative w-full max-w-3xl h-screen bg-white shadow-2xl flex flex-col">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">
                                {{ $editId ? 'Edit Student' : 'Add New Student' }}</h2>
                            <p class="text-xs text-gray-500">Fill in the student details below</p>
                        </div>
                    </div>
                    <button wire:click="closeModal" type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form Body (scrollable) --}}
                <form wire:submit.prevent="onSave" class="flex-1 overflow-y-auto">
                    <div class="p-6 space-y-6">

                        {{-- Profile Image --}}
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Profile Photo</p>
                            <div class="flex items-center gap-4">
                                @if ($editId && !$studentImage && $studentImageUrl)
                                    <img src="{{ $studentImageUrl }}"
                                        class="w-16 h-16 rounded-full object-cover border-2 border-white shadow">
                                @else
                                    <div
                                        class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center border-2 border-white shadow">
                                        <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" wire:model="studentImage" accept="image/*"
                                        class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3
                                               file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                               file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-400 mt-1">JPG, PNG up to 2MB</p>
                                    @error('studentImage')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Personal Information --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-5 h-0.5 bg-blue-500 rounded"></span>
                                Personal Information
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                {{-- Full Name --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="studentsName" type="text" placeholder="Enter full name"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('studentsName') border-red-400 @enderror">
                                    @error('studentsName')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="studentsEmail" type="email" placeholder="student@example.com"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('studentsEmail') border-red-400 @enderror">
                                    @error('studentsEmail')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Mobile --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Mobile Number <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="studentsMobile" type="tel" placeholder="10-digit mobile"
                                        maxlength="10"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('studentsMobile') border-red-400 @enderror">
                                    @error('studentsMobile')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Gender --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Gender <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="studentsGender"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors
                                               @error('studentsGender') border-red-400 @enderror">
                                        <option value="">Select Gender</option>
                                        @foreach (App\Helpers\Constants::GENDER as $gender)
                                            <option value="{{ $gender }}">{{ ucfirst($gender) }}</option>
                                        @endforeach
                                    </select>
                                    @error('studentsGender')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Date of Birth --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Date of Birth <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="dob" type="date"
                                        max="{{ now()->subDay()->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('dob') border-red-400 @enderror">
                                    @error('dob')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Date of Admission --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Date of Admission <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="dateOfAdmission" type="date"
                                        max="{{ now()->format('Y-m-d') }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('dateOfAdmission') border-red-400 @enderror">
                                    @error('dateOfAdmission')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Religion --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Religion</label>
                                    <input wire:model="religion" type="text" placeholder="e.g. Hindu, Muslim"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>

                                {{-- Aadhar --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Aadhar Number</label>
                                    <input wire:model="aadharNo" type="text" placeholder="12-digit Aadhar no."
                                        maxlength="12"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('aadharNo') border-red-400 @enderror">
                                    @error('aadharNo')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Parents Information --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-5 h-0.5 bg-purple-500 rounded"></span>
                                Parents Information
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Father's Name <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="fatherName" type="text" placeholder="Father's full name"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('fatherName') border-red-400 @enderror">
                                    @error('fatherName')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Mother's Name <span class="text-red-500">*</span>
                                    </label>
                                    <input wire:model="motherName" type="text" placeholder="Mother's full name"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('motherName') border-red-400 @enderror">
                                    @error('motherName')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Academic Information --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-5 h-0.5 bg-emerald-500 rounded"></span>
                                Academic Information
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                {{-- Board --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Board <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model="studentsBoard"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors
                                               @error('studentsBoard') border-red-400 @enderror">
                                        <option value="">Select Board</option>
                                        @foreach (App\Helpers\Constants::BOARD as $board)
                                            <option value="{{ $board }}">{{ $board }}</option>
                                        @endforeach
                                    </select>
                                    @error('studentsBoard')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Class --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                                    <select wire:model.live="studentsClass"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                                        <option value="">Select Class</option>
                                        @foreach ($standards as $standard)
                                            <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Section --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                                    <select wire:model="studentsSection"
                                        @disabled(!$studentsClass)
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors
                                               disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">Select Section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Appar ID --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apaar ID</label>
                                    <input wire:model="apparId" type="text" placeholder="Apaar ID"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>

                                {{-- Registration Number --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration
                                        Number</label>
                                    <input wire:model="registrationNumber" type="text"
                                        placeholder="Registration number"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                </div>
                            </div>
                        </div>

                        {{-- Address --}}
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
                                <span class="w-5 h-0.5 bg-orange-500 rounded"></span>
                                Address Details
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                {{-- State --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                    <select wire:model.live="selectedState"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors">
                                        <option value="">Select State</option>
                                        @foreach ($states as $state)
                                            <option value="{{ $state }}">{{ $state }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- City --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                    <select wire:model="selectedCity"
                                        @disabled(!$selectedState)
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-colors
                                               disabled:opacity-50 disabled:cursor-not-allowed">
                                        <option value="">Select City</option>
                                        @foreach ($cities as $city)
                                            <option value="{{ $city['name'] }}">{{ $city['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Pincode --}}
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pincode</label>
                                    <input wire:model="pincode" type="text" placeholder="6-digit pincode"
                                        maxlength="6"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors
                                               @error('pincode') border-red-400 @enderror">
                                    @error('pincode')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Local Address --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Local Address</label>
                                    <textarea wire:model="localAddress" rows="2" placeholder="Current/local address"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                                </div>

                                {{-- Permanent Address --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Permanent
                                        Address</label>
                                    <textarea wire:model="permanentAddress" rows="2"
                                        placeholder="Permanent address (if different)"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg
                                               focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- Settings --}}
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Settings</p>
                            <div class="flex flex-wrap gap-6">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" wire:model="transportationRequired" class="sr-only peer">
                                        <div
                                            class="w-10 h-5 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition-colors">
                                        </div>
                                        <div
                                            class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Transportation Required</span>
                                </label>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <div class="relative">
                                        <input type="checkbox" wire:model="studentsActive" class="sr-only peer">
                                        <div
                                            class="w-10 h-5 bg-gray-300 rounded-full peer peer-checked:bg-emerald-500 transition-colors">
                                        </div>
                                        <div
                                            class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5">
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                    </div>{{-- /p-6 --}}
                </form>

                {{-- Footer --}}
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <button type="button" wire:click="closeModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300
                               rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" form="student-form"
                        wire:click="onSave"
                        wire:loading.attr="disabled"
                        class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700
                               rounded-lg shadow-sm transition-colors disabled:opacity-50 disabled:cursor-not-allowed
                               flex items-center gap-2">
                        <span wire:loading wire:target="onSave">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 12 0 12 12h4z"></path>
                            </svg>
                        </span>
                        {{ $editId ? 'Update Student' : 'Add Student' }}
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         VIEW MODAL (Simple)
    ══════════════════════════════════════════════════ --}}
    <x-view-modal :show="$showViewModal" :title="$viewModalTitle" closeAction="closeViewModal" :image="$studentImageUrl ?? null">
        @if (!empty($viewData))
            <div class="space-y-5 text-left text-sm text-gray-700">

                {{-- Profile Header --}}
                <div class="flex items-center gap-4 pb-4 border-b border-gray-200">
                    @if ($studentImageUrl)
                        <img src="{{ $studentImageUrl }}"
                            class="w-16 h-16 rounded-full object-cover border border-gray-200">
                    @else
                        <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-xl font-bold text-indigo-600">
                                {{ strtoupper(substr($viewData['user']->name ?? 'S', 0, 1)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $viewData['user']->name ?? '—' }}</h3>
                        <p class="text-sm text-gray-500">{{ $viewData['detail']->admission_no ?? '—' }}</p>
                        @if ($viewData['user']->is_active ?? false)
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 mt-1 bg-green-50 text-green-700 rounded-full font-medium border border-green-100">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 mt-1 bg-red-50 text-red-600 rounded-full font-medium border border-red-100">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Personal Information --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Personal Information</h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <dt class="text-xs text-gray-400">Email</dt>
                            <dd class="font-medium">{{ $viewData['user']->email ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Mobile</dt>
                            <dd class="font-medium">{{ $viewData['user']->mobile_number ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Gender</dt>
                            <dd class="font-medium capitalize">{{ $viewData['detail']->gender ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Date of Birth</dt>
                            <dd class="font-medium">{{ $viewData['detail']->dob?->format('d M Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Father's Name</dt>
                            <dd class="font-medium">{{ $viewData['detail']->father_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Mother's Name</dt>
                            <dd class="font-medium">{{ $viewData['detail']->mother_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Religion</dt>
                            <dd class="font-medium">{{ $viewData['detail']->religion ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Aadhar No</dt>
                            <dd class="font-medium font-mono">{{ $viewData['detail']->aadhar_no ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Board</dt>
                            <dd class="font-medium">{{ $viewData['detail']->board ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Academic Information --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Academic Information</h4>
                    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <dt class="text-xs text-gray-400">Class</dt>
                            <dd class="font-medium">{{ $viewData['detail']->standard->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Section</dt>
                            <dd class="font-medium">{{ $viewData['detail']->section->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Roll No</dt>
                            <dd class="font-medium font-mono">{{ $viewData['detail']->roll_no ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs text-gray-400">Admission Date</dt>
                            <dd class="font-medium">{{ $viewData['detail']->date_of_admission?->format('d M Y') ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                {{-- Address --}}
                <div>
                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Address</h4>
                    <dl class="grid grid-cols-1 gap-3">
                        <div>
                            <dt class="text-xs text-gray-400">Local Address</dt>
                            <dd class="font-medium">{{ $viewData['detail']->local_address ?? '—' }}</dd>
                        </div>
                        @if ($viewData['detail']->permanent_address)
                            <div>
                                <dt class="text-xs text-gray-400">Permanent Address</dt>
                                <dd class="font-medium">{{ $viewData['detail']->permanent_address }}</dd>
                            </div>
                        @endif
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <dt class="text-xs text-gray-400">City</dt>
                                <dd class="font-medium">{{ $viewData['detail']->city ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400">State</dt>
                                <dd class="font-medium">{{ $viewData['detail']->state ?? '—' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-400">Pincode</dt>
                                <dd class="font-medium">{{ $viewData['detail']->pincode ?? '—' }}</dd>
                            </div>
                        </div>
                    </dl>
                </div>

                {{-- Additional Information --}}
                @if (
                    $viewData['detail']->appar_id ||
                        $viewData['detail']->registration_number ||
                        $viewData['detail']->transportation_required)
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Additional Information</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @if ($viewData['detail']->appar_id)
                                <div>
                                    <dt class="text-xs text-gray-400">Appar ID</dt>
                                    <dd class="font-medium">{{ $viewData['detail']->appar_id }}</dd>
                                </div>
                            @endif
                            @if ($viewData['detail']->registration_number)
                                <div>
                                    <dt class="text-xs text-gray-400">Registration No</dt>
                                    <dd class="font-medium">{{ $viewData['detail']->registration_number }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-xs text-gray-400">Transportation</dt>
                                <dd class="font-medium {{ $viewData['detail']->transportation_required ? 'text-green-700' : 'text-gray-500' }}">
                                    {{ $viewData['detail']->transportation_required ? 'Required' : 'Not Required' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                @endif
            </div>
        @endif
    </x-view-modal>

</div>
