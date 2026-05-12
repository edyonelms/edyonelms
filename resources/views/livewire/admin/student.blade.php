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
    <x-modal-form show="{{ $open }}" title="{{ $editId ? 'Edit Student' : 'Add Student' }}"
        submitAction="onSave" submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="closeModal">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-4">
            <div class="sm:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student Profile Image</label>
                        @if ($editId && !$studentImage)
                            @php $user = \App\Models\User::find($editId) @endphp
                            @if ($user?->image)
                                <div class="flex items-center gap-2 mb-2">
                                    <img src="{{ $user->image }}" class="h-16 w-16 rounded-full object-cover">
                                    <button wire:click="$set('studentImage', null)" type="button"
                                        class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                                </div>
                            @endif
                        @endif
                        <input type="file" wire:model="studentImage"
                            class="block w-full text-sm text-gray-500
                                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                hover:file:bg-blue-100">
                        @error('studentImage')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <x-input wire:model.defer="studentsName" label="Full Name" required />
            <x-input wire:model.defer="studentsEmail" label="Email" required />
            <x-input wire:model.defer="fatherName" label="Father's Name" required />
            <x-input wire:model.defer="motherName" label="Mother's Name" required />
            <x-datetime-picker label="Date Of Birth" without-time required wire:model.defer="dob" />
            <x-datetime-picker label="Date Of Admission" without-time required wire:model.defer="dateOfAdmission"
                :min="null" :max="null" />
            <x-input wire:model.defer="studentsMobile" label="Mobile Number" required />
            <x-input wire:model.defer="aadharNo" label="Aadhar Number" />
            <x-input wire:model.defer="apparId" label="Apaar Id" />
            <x-input wire:model.defer="registrationNumber" label="Registration Number" />

            <x-native-select label="Gender" wire:model.defer="studentsGender" required>
                <option value="">Select Gender</option>
                @foreach (App\Helpers\Constants::GENDER as $gender)
                    <option value="{{ $gender }}">{{ $gender }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Board" wire:model.defer="studentsBoard" required>
                <option value="">Select Board</option>
                @foreach (App\Helpers\Constants::BOARD as $board)
                    <option value="{{ $board }}">{{ $board }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Class" wire:model.live="studentsClass">
                <option value="">Select Class</option>
                @foreach ($standards as $standard)
                    <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Section" wire:model.defer="studentsSection">
                <option value="">Select Section</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="State" wire:model.live="selectedState">
                <option value="">Select State</option>
                @foreach ($states as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="City" wire:model.live="selectedCity">
                <option value="">Select City</option>
                @foreach ($cities as $city)
                    <option value="{{ $city['name'] }}">{{ $city['name'] }}</option>
                @endforeach
            </x-native-select>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-4">
            <x-input wire:model.defer="pincode" label="Pincode" />
            <x-input wire:model.defer="religion" label="Religion" />
            <x-textarea wire:model.defer="localAddress" label="Local Address" />
            <x-textarea wire:model.defer="permanentAddress" label="Permanent Address" />
        </div>

        <div class="flex gap-4 py-4">
            <x-toggle label="Transportation Required" wire:model.defer="transportationRequired" />
            <x-toggle label="Active" wire:model.defer="studentsActive" />
        </div>
    </x-modal-form>

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
