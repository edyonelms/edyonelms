<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════════════════════════════════════
         HEADER + TABS + FILTER BAR (exams-style)
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="px-4 sm:px-6 py-4 sm:py-5">
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
                        class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="hidden sm:inline">Create Timetable</span>
                        <span class="sm:hidden">New</span>
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

        {{-- Tabs --}}
        <div class="border-t border-gray-200 px-4 sm:px-6">
            <div class="flex gap-1">
                <button wire:click="setViewMode('class')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $viewMode === 'class' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                        Class View
                    </span>
                </button>
                <button wire:click="setViewMode('teacher')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors {{ $viewMode === 'teacher' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Teacher View
                    </span>
                </button>
            </div>
        </div>

        {{-- Filter bar --}}
        <div class="border-t border-gray-200 bg-gray-50 px-4 sm:px-6 py-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-1.5 text-sm font-semibold text-gray-700">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    Filter by:
                </div>

                @if ($viewMode === 'class')
                    <select wire:model.live="filterClass"
                        class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Classes</option>
                        @foreach ($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="filterSection" @disabled(!$filterClass)
                        class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        <option value="">All Sections</option>
                        @foreach ($filterSections as $sec)
                            <option value="{{ $sec['id'] }}">{{ $sec['name'] }}</option>
                        @endforeach
                    </select>
                @else
                    <select wire:model.live="filterTeacher"
                        class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[180px]">
                        <option value="">Select a Teacher</option>
                        @foreach ($allTeachers as $t)
                            <option value="{{ $t->id }}">{{ $t->user?->name ?? '—' }}</option>
                        @endforeach
                    </select>
                @endif

                {{-- Day chips --}}
                <div class="flex items-center gap-1.5 ml-1">
                    @foreach ($daysOfWeek as $dayNum => $dayName)
                        <button wire:click="toggleFilterDay({{ $dayNum }})"
                            class="px-2 py-1 text-xs font-medium rounded-md border transition-colors {{ in_array($dayNum, $filterDays) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                            {{ $dayName }}
                        </button>
                    @endforeach
                </div>

                @if ($filterClass || $filterSection || $filterTeacher || !empty($filterDays))
                    <button wire:click="clearFilters"
                        class="ml-auto inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-600 bg-white border border-red-200 rounded-md hover:bg-red-50">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">

        {{-- ══════════════════════════════════════════════════
             EMPTY STATE FOR TEACHER MODE WITHOUT TEACHER
        ══════════════════════════════════════════════════ --}}
        @if ($viewMode === 'teacher' && !$filterTeacher)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-12 text-center">
                <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <p class="text-sm text-gray-600 font-medium">Select a teacher to view their schedule.</p>
            </div>
        @else

            {{-- ══════════════════════════════════════════════════
                 DESKTOP TABLE (grouped)
            ══════════════════════════════════════════════════ --}}
            <div class="hidden md:block bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">S.No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Section</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teacher(s)</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">Time</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Days</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-28">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($groups as $i => $g)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-4 py-3 text-sm text-gray-500 font-medium">{{ $groups->firstItem() + $i }}</td>
                                    <td class="px-4 py-3">
                                        <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium border border-blue-100">
                                            {{ $g['standard'] ?? '—' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($g['section'])
                                            <span class="text-xs px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full font-medium border border-purple-100">{{ $g['section'] }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $g['subject'] ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="space-y-1">
                                            @foreach ($g['teachers'] as $t)
                                                <div class="flex items-start gap-2 text-sm">
                                                    <div class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                        <span class="text-[10px] font-semibold text-teal-600">{{ strtoupper(substr($t['teacher_name'], 0, 1)) }}</span>
                                                    </div>
                                                    <div>
                                                        <div class="text-gray-800 font-medium leading-tight">{{ $t['teacher_name'] }}</div>
                                                        <div class="text-[11px] text-gray-500 leading-tight">
                                                            {{ collect($t['days'])->map(fn($d) => $daysOfWeek[$d] ?? $d)->implode(', ') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($g['start_time'])->format('h:i A') }} – {{ \Carbon\Carbon::parse($g['end_time'])->format('h:i A') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($g['days'] as $d)
                                                <span class="text-[10px] px-1.5 py-0.5 bg-indigo-50 text-indigo-700 rounded font-medium border border-indigo-100">{{ $daysOfWeek[$d] ?? $d }}</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <button wire:click="onViewGroup('{{ $g['key'] }}')" title="View"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>
                                            <button wire:click="onEditGroup('{{ $g['key'] }}')" title="Edit"
                                                class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button wire:click="onDeleteGroup('{{ $g['key'] }}')" title="Delete"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-500 text-sm">No timetable entries found</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($groups->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $groups->links() }}
                    </div>
                @endif
            </div>

            {{-- ══════════════════════════════════════════════════
                 MOBILE CARDS
            ══════════════════════════════════════════════════ --}}
            <div class="md:hidden space-y-3">
                @forelse ($groups as $i => $g)
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="flex items-center justify-between p-4 border-b border-gray-100">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-bold text-gray-400">{{ $groups->firstItem() + $i }}</span>
                                <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium border border-blue-100">{{ $g['standard'] }}</span>
                                @if ($g['section'])
                                    <span class="text-xs px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full font-medium border border-purple-100">{{ $g['section'] }}</span>
                                @endif
                            </div>
                            <span class="text-xs text-gray-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($g['start_time'])->format('h:i A') }} – {{ \Carbon\Carbon::parse($g['end_time'])->format('h:i A') }}
                            </span>
                        </div>
                        <div class="px-4 py-3 space-y-2">
                            <div>
                                <p class="text-xs text-gray-400">Subject</p>
                                <p class="text-sm font-semibold text-gray-800">{{ $g['subject'] }}</p>
                            </div>
                            <div class="space-y-1.5">
                                @foreach ($g['teachers'] as $t)
                                    <div class="flex items-start gap-2 text-sm">
                                        <div class="w-6 h-6 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <span class="text-[10px] font-semibold text-teal-600">{{ strtoupper(substr($t['teacher_name'], 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-gray-800 font-medium leading-tight">{{ $t['teacher_name'] }}</div>
                                            <div class="text-[11px] text-gray-500 leading-tight">
                                                {{ collect($t['days'])->map(fn($d) => $daysOfWeek[$d] ?? $d)->implode(', ') }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center border-t border-gray-100">
                            <button wire:click="onViewGroup('{{ $g['key'] }}')" class="flex-1 py-2.5 text-xs font-medium text-blue-600 hover:bg-blue-50">View</button>
                            <button wire:click="onEditGroup('{{ $g['key'] }}')" class="flex-1 py-2.5 text-xs font-medium text-emerald-600 hover:bg-emerald-50 border-l border-gray-100">Edit</button>
                            <button wire:click="onDeleteGroup('{{ $g['key'] }}')" class="flex-1 py-2.5 text-xs font-medium text-red-600 hover:bg-red-50 border-l border-gray-100">Delete</button>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-8 text-center">
                        <p class="text-gray-500 text-sm">No timetable entries found</p>
                    </div>
                @endforelse

                @if ($groups->hasPages())
                    <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3">{{ $groups->links() }}</div>
                @endif
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════
         ADD / EDIT SLIDE-IN PANEL (exams-style)
    ══════════════════════════════════════════════════ --}}
    @if ($open)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closePanel"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-3xl bg-white shadow-2xl flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $isEdit ? 'Edit Timetable' : 'New Timetable' }}</h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $isEdit ? 'Update class schedule and teachers' : 'Pick class & section, then add subject schedules' }}
                        </p>
                    </div>
                    <button wire:click="closePanel" class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">
                    {{-- Step 1: Class & Section --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Class <span class="text-red-500">*</span></label>
                            <select wire:model.live="createStandardId" @disabled($isEdit)
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100">
                                <option value="">Select Class</option>
                                @foreach ($standards as $std)
                                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Section <span class="text-red-500">*</span></label>
                            <select wire:model.live="createSectionId" @disabled($isEdit || !$createStandardId)
                                class="w-full px-3.5 py-2.5 border border-gray-300 rounded-md text-sm focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:cursor-not-allowed">
                                <option value="">Select Section</option>
                                @foreach ($createSections as $sec)
                                    <option value="{{ $sec['id'] }}">{{ $sec['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Step 2: Subject schedules --}}
                    @if ($createStandardId && $createSectionId)
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-gray-700">Subject Schedules</h3>
                                @if (count($availableSubjects) > count($usedSubjectIds))
                                    <button wire:click="addScheduleRow" type="button"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Subject
                                    </button>
                                @endif
                            </div>

                            @if (empty($scheduleRows))
                                <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-lg">
                                    <p class="text-sm text-gray-400 mb-3">No subjects added yet</p>
                                    <button wire:click="addScheduleRow" type="button"
                                        class="inline-flex items-center gap-1 px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md border border-blue-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add First Subject
                                    </button>
                                </div>
                            @endif

                            <div class="space-y-4">
                                @foreach ($scheduleRows as $i => $row)
                                    @php
                                        $rowSubjects = $this->getAvailableSubjectsForRow($i);
                                        $fallbackDays = $this->getRowFallbackDays($i);
                                        $teacherConflict = $this->checkTeacherConflict($i);
                                        $slotConflict    = $this->checkSlotConflict($i);
                                    @endphp
                                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 space-y-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-gray-400 uppercase">Subject {{ $i + 1 }}</span>
                                            @if (!$isEdit)
                                                <button wire:click="removeScheduleRow({{ $i }})" type="button"
                                                    class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Remove">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">Subject *</label>
                                                <select wire:model.live="scheduleRows.{{ $i }}.subject_id"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-white focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Select Subject</option>
                                                    @foreach ($rowSubjects as $subj)
                                                        <option value="{{ $subj['id'] }}">{{ $subj['name'] }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">Primary Teacher *</label>
                                                <select wire:model.live="scheduleRows.{{ $i }}.primary_teacher_id"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-white focus:ring-1 focus:ring-blue-500">
                                                    <option value="">Select Teacher</option>
                                                    @foreach ($allTeachers as $t)
                                                        <option value="{{ $t->id }}">{{ $t->user?->name ?? '—' }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">Start Time *</label>
                                                <input type="time" wire:model.live="scheduleRows.{{ $i }}.start_time"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-white focus:ring-1 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-gray-500 mb-1">End Time *</label>
                                                <input type="time" wire:model.live="scheduleRows.{{ $i }}.end_time"
                                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md bg-white focus:ring-1 focus:ring-blue-500">
                                            </div>
                                        </div>

                                        {{-- Days --}}
                                        <div>
                                            <div class="flex items-center justify-between mb-1.5">
                                                <label class="text-xs font-medium text-gray-500">Days * <span class="text-gray-400 font-normal">(Mon–Sat by default)</span></label>
                                                <button wire:click="selectAllRowDays({{ $i }})" type="button"
                                                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">Reset to Mon–Sat</button>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5">
                                                @foreach ($daysOfWeek as $dayNum => $dayName)
                                                    <button wire:click="toggleRowDay({{ $i }}, {{ $dayNum }})" type="button"
                                                        class="px-2.5 py-1 text-xs font-medium rounded-md border transition-colors {{ in_array($dayNum, $row['selected_days'] ?? []) ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                                                        {{ $dayName }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Fallback teacher (appears when default days are unchecked) --}}
                                        @if (!empty($fallbackDays))
                                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-md space-y-2">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <div class="text-xs text-amber-800">
                                                        Primary teacher is not teaching on:
                                                        <strong>{{ collect($fallbackDays)->map(fn($d) => $daysOfWeekFull[$d])->implode(', ') }}</strong>.
                                                        Choose a fallback teacher for those days (same time).
                                                    </div>
                                                </div>
                                                <select wire:model.live="scheduleRows.{{ $i }}.fallback_teacher_id"
                                                    class="w-full px-3 py-2 text-sm border border-amber-300 rounded-md bg-white focus:ring-1 focus:ring-amber-500">
                                                    <option value="">Select Fallback Teacher</option>
                                                    @foreach ($allTeachers as $t)
                                                        @if ((int) $t->id !== (int) ($row['primary_teacher_id'] ?? 0))
                                                            <option value="{{ $t->id }}">{{ $t->user?->name ?? '—' }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif

                                        {{-- Conflict warnings --}}
                                        @if ($slotConflict)
                                            <div class="flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-200 rounded-md">
                                                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <p class="text-xs text-red-700 font-medium">{{ $slotConflict }}</p>
                                            </div>
                                        @endif
                                        @if ($teacherConflict)
                                            <div class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-md">
                                                <svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                <p class="text-xs text-amber-700 font-medium">{{ $teacherConflict }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="text-center py-10 text-sm text-gray-400">Please select a class and section to start adding subjects.</div>
                    @endif
                </div>

                <div class="px-6 py-3.5 border-t border-gray-200 flex items-center justify-end gap-2 flex-shrink-0">
                    <button wire:click="closePanel" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="onSaveTimetable" wire:loading.attr="disabled"
                        class="px-5 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-md flex items-center gap-1.5 disabled:opacity-60">
                        <span wire:loading.remove wire:target="onSaveTimetable">{{ $isEdit ? 'Update Timetable' : 'Create Timetable' }}</span>
                        <span wire:loading wire:target="onSaveTimetable">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         VIEW SLIDE-IN PANEL
    ══════════════════════════════════════════════════ --}}
    @if ($showView && !empty($viewData))
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeView"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-xl bg-white shadow-2xl flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <div class="min-w-0">
                        <h2 class="text-lg font-semibold text-gray-900 truncate">{{ $viewData['subject'] }}</h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $viewData['standard'] }}@if($viewData['section']) · {{ $viewData['section'] }} @endif
                            · {{ $viewData['start_time'] }} – {{ $viewData['end_time'] }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button wire:click="onEditFromView"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-md">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                        <button wire:click="closeView" class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-4">
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Class</span>
                        <span class="col-span-2 text-gray-800 font-medium">{{ $viewData['standard'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Section</span>
                        <span class="col-span-2 text-gray-800 font-medium">{{ $viewData['section'] ?? '—' }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Subject</span>
                        <span class="col-span-2 text-gray-800 font-medium">{{ $viewData['subject'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-sm">
                        <span class="text-xs text-gray-400 uppercase tracking-wider">Time</span>
                        <span class="col-span-2 text-gray-800 font-medium">{{ $viewData['start_time'] }} – {{ $viewData['end_time'] }}</span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wider mb-2">Teachers</p>
                        <div class="space-y-2">
                            @foreach ($viewData['teachers'] as $t)
                                <div class="flex items-start gap-3 p-3 bg-gray-50 border border-gray-200 rounded-md">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-xs font-semibold text-teal-600">{{ strtoupper(substr($t['teacher_name'], 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-800">{{ $t['teacher_name'] }}</div>
                                        <div class="text-xs text-gray-500">{{ implode(', ', $t['days']) }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="px-6 py-3.5 border-t border-gray-200 flex items-center justify-end gap-2 flex-shrink-0">
                    <button wire:click="closeView" class="px-5 py-2 text-sm font-medium text-white bg-gray-900 hover:bg-gray-800 rounded-md">Close</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         DELETE CONFIRM OVERLAY
    ══════════════════════════════════════════════════ --}}
    @if ($showDeleteConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[1.5px]" wire:click="cancelDelete"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900 mb-1">Delete schedule?</h3>
                        <p class="text-sm text-gray-500">This will remove all timetable entries for this class–subject–time group across all days.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-5">
                    <button wire:click="cancelDelete" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="confirmDelete" wire:loading.attr="disabled"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md disabled:opacity-60 flex items-center gap-1.5">
                        <span wire:loading.remove wire:target="confirmDelete">Delete</span>
                        <span wire:loading wire:target="confirmDelete">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
