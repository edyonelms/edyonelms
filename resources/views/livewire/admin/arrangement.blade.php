<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Teacher Arrangements</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage substitute teacher assignments</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                    <span class="pr-4">Total: <strong class="text-gray-800">{{ $totalTeachers }}</strong></span>
                    <span class="px-4">Absent: <strong class="text-red-500">{{ $absentCount }}</strong></span>
                    <span class="px-4">Available: <strong
                            class="text-emerald-600">{{ $availableCount }}</strong></span>
                    <span class="pl-4">Arrangements: <strong
                            class="text-blue-600">{{ $arrangementCount }}</strong></span>
                </div>
                <button wire:click="onCreateArrangement"
                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Create Arrangement</span>
                </button>
            </div>
        </div>
        <div class="flex lg:hidden items-center gap-3 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Total: <strong class="text-gray-800">{{ $totalTeachers }}</strong></span>
            <span>Absent: <strong class="text-red-500">{{ $absentCount }}</strong></span>
            <span>Available: <strong class="text-emerald-600">{{ $availableCount }}</strong></span>
            <span>Arrangements: <strong class="text-blue-600">{{ $arrangementCount }}</strong></span>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">

        {{-- ══════════════════════════════════════════════════
             FILTERS
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

                {{-- Date --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Date</label>
                    <input type="date" wire:model.live="date"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Class filter --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Class</label>
                    <select wire:model.live="filterClass"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">All Classes</option>
                        @foreach ($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Teacher filter (only absent teachers) --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Absent Teacher</label>
                    <select wire:model.live="filterTeacher"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">All Absent Teachers</option>
                        @foreach ($absentTeachers as $t)
                            <option value="{{ $t->id }}">{{ $t->user?->name ?? '—' }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Clear --}}
                <div class="flex items-end">
                    @if ($filterClass || $filterTeacher)
                        <button wire:click="$set('filterClass', ''); $set('filterTeacher', '')" title="Clear filters"
                            class="px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50">
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
             ARRANGEMENTS TABLE (Desktop)
        ══════════════════════════════════════════════════ --}}
        <div class="hidden md:block bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-3 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-700">
                    Arrangements — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                S.No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Original Teacher</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Substitute</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Class / Section</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Subject</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Time</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Reason</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-20">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($arrangements as $index => $arr)
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-4 py-3">
                                    <span
                                        class="text-sm text-gray-500 font-medium">{{ $arrangements->firstItem() + $index }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-semibold text-red-600">
                                                {{ strtoupper(substr($arr->originalTeacher?->user?->name ?? 'T', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $arr->originalTeacher?->user?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-xs font-semibold text-emerald-600">
                                                {{ strtoupper(substr($arr->substituteTeacher?->user?->name ?? 'S', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span
                                            class="text-sm font-medium text-emerald-700">{{ $arr->substituteTeacher?->user?->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @if ($arr->timetable?->standard)
                                            <span
                                                class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium border border-blue-100">
                                                {{ $arr->timetable->standard->name }}
                                            </span>
                                        @endif
                                        @if ($arr->timetable?->section)
                                            <span
                                                class="text-xs px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full font-medium border border-purple-100">
                                                {{ $arr->timetable->section->name }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="text-sm text-gray-800">{{ $arr->timetable?->subject?->name ?? '—' }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-sm text-gray-700">
                                        {{ $arr->timetable ? \Carbon\Carbon::parse($arr->timetable->start_time)->format('h:i A') . ' – ' . \Carbon\Carbon::parse($arr->timetable->end_time)->format('h:i A') : '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm text-gray-600 truncate block max-w-[150px]"
                                        title="{{ $arr->reason }}">
                                        {{ \Illuminate\Support\Str::limit($arr->reason, 30) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center">
                                        <button wire:click="deleteArrangement({{ $arr->id }})"
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
                                        class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-sm">No arrangements for
                                        {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($arrangements->hasPages())
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium text-gray-700">{{ $arrangements->firstItem() }}</span>
                        to <span class="font-medium text-gray-700">{{ $arrangements->lastItem() }}</span>
                        of <span class="font-medium text-gray-700">{{ $arrangements->total() }}</span>
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($arrangements->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&laquo;
                                Prev</span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">&laquo;
                                Prev</button>
                        @endif
                        @foreach ($arrangements->getUrlRange(max(1, $arrangements->currentPage() - 2), min($arrangements->lastPage(), $arrangements->currentPage() + 2)) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm rounded-lg {{ $page == $arrangements->currentPage() ? 'bg-blue-600 text-white border border-blue-600' : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">{{ $page }}</button>
                        @endforeach
                        @if ($arrangements->hasMorePages())
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
            @forelse ($arrangements as $index => $arr)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between p-4 border-b border-gray-100">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-xs font-bold text-gray-400">{{ $arrangements->firstItem() + $index }}</span>
                            <span class="text-xs text-gray-500">
                                {{ $arr->timetable ? \Carbon\Carbon::parse($arr->timetable->start_time)->format('h:i A') . ' – ' . \Carbon\Carbon::parse($arr->timetable->end_time)->format('h:i A') : '—' }}
                            </span>
                        </div>
                        <div class="flex gap-1">
                            @if ($arr->timetable?->standard)
                                <span
                                    class="text-xs px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded font-medium">{{ $arr->timetable->standard->name }}</span>
                            @endif
                            @if ($arr->timetable?->section)
                                <span
                                    class="text-xs px-1.5 py-0.5 bg-purple-50 text-purple-700 rounded font-medium">{{ $arr->timetable->section->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="px-4 py-3 space-y-2">
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-xs text-gray-400">Original Teacher</p>
                                <p class="text-red-600 font-medium">{{ $arr->originalTeacher?->user?->name ?? '—' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Substitute</p>
                                <p class="text-emerald-700 font-medium">
                                    {{ $arr->substituteTeacher?->user?->name ?? '—' }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <p class="text-xs text-gray-400">Subject</p>
                                <p class="text-gray-800">{{ $arr->timetable?->subject?->name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Reason</p>
                                <p class="text-gray-600 truncate" title="{{ $arr->reason }}">
                                    {{ \Illuminate\Support\Str::limit($arr->reason, 25) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center border-t border-gray-100">
                        <button wire:click="deleteArrangement({{ $arr->id }})"
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
                    <p class="text-gray-500 text-sm">No arrangements for
                        {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</p>
                </div>
            @endforelse

            @if ($arrangements->hasPages())
                <div
                    class="flex items-center justify-between bg-white rounded-xl border border-gray-200 shadow-sm px-4 py-3">
                    <p class="text-xs text-gray-500">{{ $arrangements->firstItem() }}–{{ $arrangements->lastItem() }}
                        of {{ $arrangements->total() }}</p>
                    <div class="flex items-center gap-1">
                        @if (!$arrangements->onFirstPage())
                            <button wire:click="previousPage"
                                class="px-2.5 py-1 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Prev</button>
                        @endif
                        <span
                            class="px-2.5 py-1 text-xs bg-blue-600 text-white rounded-lg">{{ $arrangements->currentPage() }}</span>
                        @if ($arrangements->hasMorePages())
                            <button wire:click="nextPage"
                                class="px-2.5 py-1 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Next</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         CREATE ARRANGEMENT MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $open }}"
        title="Create Arrangement — {{ \Carbon\Carbon::parse($date)->format('d M Y') }}"
        submitAction="onSaveArrangements" submitButton="Save Arrangements" closeAction="closeModal">

        {{-- Date display --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
            <input type="date" wire:model.live="date"
                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                min="{{ now()->format('Y-m-d') }}">
        </div>

        {{-- Absent Teacher Selection --}}
        <div class="mb-5">
            <x-native-select label="Select Absent Teacher *" wire:model.live="selectedTeacherId">
                <option value="">Choose absent teacher</option>
                @foreach ($absentTeachers as $teacher)
                    <option value="{{ $teacher->id }}">
                        {{ $teacher->user?->name ?? '—' }}
                    </option>
                @endforeach
            </x-native-select>
            @if ($absentTeachers->isEmpty())
                <p class="text-xs text-emerald-600 mt-1">No teachers marked absent for this date.</p>
            @endif
        </div>

        {{-- Timetable Slots with Substitute Selection --}}
        @if ($selectedTeacherId)
            @if (empty($teacherSlots))
                <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-xl">
                    <p class="text-sm text-gray-400">No remaining slots to arrange for this teacher on this day.</p>
                </div>
            @else
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-gray-700">Class Slots to Arrange</h3>

                    @foreach ($teacherSlots as $i => $slot)
                        @php
                            $availableSubs = $this->getAvailableSubstitutesForSlot($i);
                        @endphp
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
                            {{-- Slot info --}}
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-xs font-bold text-gray-400 uppercase">Slot
                                        {{ $i + 1 }}</span>
                                    <span
                                        class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full font-medium border border-blue-100">
                                        {{ $slot['standard']['name'] ?? '—' }}
                                    </span>
                                    @if (!empty($slot['section']))
                                        <span
                                            class="text-xs px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full font-medium border border-purple-100">
                                            {{ $slot['section']['name'] }}
                                        </span>
                                    @endif
                                    <span
                                        class="text-xs font-medium text-gray-600">{{ $slot['subject']['name'] ?? '—' }}</span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($slot['start_time'])->format('h:i A') }}
                                    –
                                    {{ \Carbon\Carbon::parse($slot['end_time'])->format('h:i A') }}
                                </span>
                            </div>

                            {{-- Substitute selection --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Substitute
                                        Teacher</label>
                                    <select wire:model="slotSubstitutes.{{ $i }}"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                                        <option value="">Select substitute</option>
                                        @foreach ($availableSubs as $sub)
                                            <option value="{{ $sub['id'] }}">{{ $sub['user']['name'] ?? '—' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if (empty($availableSubs))
                                        <p class="text-xs text-amber-600 mt-1">No teachers available for this time
                                            slot.</p>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Reason</label>
                                    <input type="text" wire:model="slotReasons.{{ $i }}"
                                        placeholder="e.g. Sick leave"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="text-center py-6 text-sm text-gray-400">
                Select an absent teacher to see their class slots.
            </div>
        @endif

    </x-modal-form>

</div>
