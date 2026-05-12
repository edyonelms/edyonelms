<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Timetable</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage class schedules and teacher assignments</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                    <span class="pr-4">Schedules: <strong class="text-gray-800">{{ $totalSchedules }}</strong></span>
                    <span class="px-4">Teachers: <strong class="text-emerald-600">{{ $totalTeachers }}</strong></span>
                    <span class="px-4">Classes: <strong class="text-blue-600">{{ $totalClasses }}</strong></span>
                    <span class="pl-4">Subjects: <strong class="text-purple-600">{{ $totalSubjects }}</strong></span>
                </div>
                <button wire:click="onCreateTimetable"
                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Create Timetable</span>
                </button>
            </div>
        </div>
        <div class="flex lg:hidden items-center gap-3 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Schedules: <strong class="text-gray-800">{{ $totalSchedules }}</strong></span>
            <span>Teachers: <strong class="text-emerald-600">{{ $totalTeachers }}</strong></span>
            <span>Classes: <strong class="text-blue-600">{{ $totalClasses }}</strong></span>
            <span>Subjects: <strong class="text-purple-600">{{ $totalSubjects }}</strong></span>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">

        {{-- ══════════════════════════════════════════════════
             FILTERS
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <select wire:model.live="filterClass"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Classes</option>
                    @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterSection"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white
                           disabled:opacity-50 disabled:cursor-not-allowed"
                    @disabled(!$filterClass)>
                    <option value="">All Sections</option>
                    @foreach ($filterSections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterTeacher"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Teachers</option>
                    @foreach ($allTeachers as $t)
                        <option value="{{ $t->id }}">{{ $t->user?->name ?? '—' }}</option>
                    @endforeach
                </select>
                <div class="flex items-center gap-2">
                    @if ($filterClass || $filterSection || $filterTeacher || !empty($filterDays))
                        <button wire:click="clearFilters" title="Clear all filters"
                            class="px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Day Chips --}}
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-medium text-gray-500 mr-1">Days:</span>
                @foreach ($daysOfWeek as $dayNum => $dayName)
                    <button wire:click="toggleFilterDay({{ $dayNum }})"
                        class="px-3 py-1 text-xs font-medium rounded-full border transition-colors
                            {{ in_array($dayNum, $filterDays)
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        {{ $dayName }}
                    </button>
                @endforeach
                @if (!empty($filterDays))
                    <button wire:click="clearFilterDays"
                        class="text-xs text-red-500 hover:text-red-700 font-medium ml-1">
                        Clear days
                    </button>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             DESKTOP TABLE
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
                                Day</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Class / Section</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Subject</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Teacher</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Time</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($timetable as $index => $entry)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-4 py-3">
                                    <span
                                        class="text-sm text-gray-500 font-medium">{{ $timetable->firstItem() + $index }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="text-xs px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full font-medium border border-indigo-100">
                                        {{ $daysOfWeekFull[$entry->day_of_week] ?? '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <span
                                            class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium border border-blue-100">
                                            {{ $entry->standard?->name ?? '—' }}
                                        </span>
                                        @if ($entry->section)
                                            <span
                                                class="text-xs px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full font-medium border border-purple-100">
                                                {{ $entry->section->name }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="text-sm font-medium text-gray-800">{{ $entry->subject?->name ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-semibold text-teal-600">
                                                {{ strtoupper(substr($entry->teacher?->user?->name ?? 'T', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span
                                            class="text-sm text-gray-700">{{ $entry->teacher?->user?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-sm text-gray-700">
                                        {{ \Carbon\Carbon::parse($entry->start_time)->format('h:i A') }}
                                        –
                                        {{ \Carbon\Carbon::parse($entry->end_time)->format('h:i A') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center">
                                        <button wire:click="deleteEntry({{ $entry->id }})"
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
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">No timetable entries found</p>
                                    @if ($filterClass || $filterSection || $filterTeacher || !empty($filterDays))
                                        <button wire:click="clearFilters"
                                            class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">Clear
                                            filters</button>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($timetable->hasPages())
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium text-gray-700">{{ $timetable->firstItem() }}</span>
                        to <span class="font-medium text-gray-700">{{ $timetable->lastItem() }}</span>
                        of <span class="font-medium text-gray-700">{{ $timetable->total() }}</span>
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($timetable->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&laquo;
                                Prev</span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">&laquo;
                                Prev</button>
                        @endif
                        @foreach ($timetable->getUrlRange(max(1, $timetable->currentPage() - 2), min($timetable->lastPage(), $timetable->currentPage() + 2)) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm rounded-lg {{ $page == $timetable->currentPage() ? 'bg-blue-600 text-white border border-blue-600' : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">{{ $page }}</button>
                        @endforeach
                        @if ($timetable->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Next
                                &raquo;</button>
                        @else
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">Next
                                &raquo;</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════
             MOBILE CARDS
        ══════════════════════════════════════════════════ --}}
        <div class="md:hidden space-y-3">
            @forelse ($timetable as $index => $entry)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs font-bold text-gray-400">{{ $timetable->firstItem() + $index }}</span>
                            <span
                                class="text-xs px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded-full font-medium border border-indigo-100">
                                {{ $daysOfWeekFull[$entry->day_of_week] ?? '—' }}
                            </span>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($entry->start_time)->format('h:i A') }} –
                            {{ \Carbon\Carbon::parse($entry->end_time)->format('h:i A') }}
                        </span>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-xs text-gray-400">Class / Section</p>
                                <div class="flex gap-1 mt-0.5">
                                    <span
                                        class="text-xs px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded font-medium">{{ $entry->standard?->name ?? '—' }}</span>
                                    @if ($entry->section)
                                        <span
                                            class="text-xs px-1.5 py-0.5 bg-purple-50 text-purple-700 rounded font-medium">{{ $entry->section->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Subject</p>
                                <p class="text-sm font-medium text-gray-800">{{ $entry->subject?->name ?? '—' }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Teacher</p>
                            <p class="text-sm text-gray-700">{{ $entry->teacher?->user?->name ?? '—' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center border-t border-gray-100">
                        <button wire:click="deleteEntry({{ $entry->id }})"
                            class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-xs font-medium text-red-600 hover:bg-red-50">
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
                    <p class="text-gray-500 text-sm">No timetable entries found</p>
                </div>
            @endforelse

            @if ($timetable->hasPages())
                <div
                    class="flex items-center justify-between bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3">
                    <p class="text-xs text-gray-500">{{ $timetable->firstItem() }}–{{ $timetable->lastItem() }} of
                        {{ $timetable->total() }}</p>
                    <div class="flex items-center gap-1">
                        @if (!$timetable->onFirstPage())
                            <button wire:click="previousPage"
                                class="px-2.5 py-1 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Prev</button>
                        @endif
                        <span
                            class="px-2.5 py-1 text-xs bg-blue-600 text-white rounded-lg">{{ $timetable->currentPage() }}</span>
                        @if ($timetable->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-2.5 py-1 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         CREATE TIMETABLE MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $open }}" title="Create Timetable" submitAction="onSaveTimetable"
        submitButton="Save All Schedules" closeAction="closeModal">

        {{-- Step 1: Class & Section --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <x-native-select label="Class *" wire:model.live="createStandardId">
                <option value="">Select Class</option>
                @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Section" wire:model.live="createSectionId" :disabled="!$createStandardId">
                <option value="">Select Section (Optional)</option>
                @foreach ($createSections as $sec)
                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                @endforeach
            </x-native-select>
        </div>

        {{-- Step 2: Subject Rows --}}
        @if ($createStandardId)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Subject Schedules</h3>
                    @if (count($availableSubjects) > count($usedSubjectIds))
                        <button wire:click="addScheduleRow" type="button"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold
                                   text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Subject
                        </button>
                    @endif
                </div>

                @if (empty($scheduleRows))
                    <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-sm text-gray-400 mb-3">No subjects added yet</p>
                        <button wire:click="addScheduleRow" type="button"
                            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-semibold
                                   text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add First Subject
                        </button>
                    </div>
                @endif

                @foreach ($scheduleRows as $i => $row)
                    @php
                        $rowSubjects = $this->getAvailableSubjectsForRow($i);
                        $conflict = $this->checkTeacherConflict($i);
                    @endphp
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
                        {{-- Row header --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase">Subject {{ $i + 1 }}</span>
                            <button wire:click="removeScheduleRow({{ $i }})" type="button"
                                class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Remove">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {{-- Subject --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Subject *</label>
                                <select wire:model.live="scheduleRows.{{ $i }}.subject_id"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="">Select Subject</option>
                                    @foreach ($rowSubjects as $subj)
                                        <option value="{{ $subj['id'] }}">{{ $subj['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Teacher --}}
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Teacher *</label>
                                <select wire:model.live="scheduleRows.{{ $i }}.teacher_id"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                    <option value="">Select Teacher</option>
                                    @foreach ($allTeachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->user?->name ?? '—' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Time --}}
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Start *</label>
                                    <input type="time"
                                        wire:model.live="scheduleRows.{{ $i }}.start_time"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">End *</label>
                                    <input type="time" wire:model.live="scheduleRows.{{ $i }}.end_time"
                                        class="w-full px-2 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        {{-- Days --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-medium text-gray-500">Days *</label>
                                <button wire:click="selectAllRowDays({{ $i }})" type="button"
                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">Select All</button>
                            </div>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($daysOfWeek as $dayNum => $dayName)
                                    <button wire:click="toggleRowDay({{ $i }}, {{ $dayNum }})"
                                        type="button"
                                        class="px-2.5 py-1 text-xs font-medium rounded-lg border transition-colors
                                            {{ in_array($dayNum, $row['selected_days'] ?? [])
                                                ? 'bg-blue-600 text-white border-blue-600'
                                                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                                        {{ $dayName }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Conflict Warning --}}
                        @if ($conflict)
                            <div
                                class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg">
                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                <p class="text-xs text-amber-700 font-medium">{{ $conflict }}</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-sm text-gray-400">
                Please select a class to start adding subjects.
            </div>
        @endif
    </x-modal-form>

</div>
