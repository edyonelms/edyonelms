<div class="min-h-screen bg-gray-50">

@php
$gradeBadge = function($grade) {
    return match(true) {
        in_array($grade, ['A+', 'A']) => 'bg-green-100 text-green-700',
        in_array($grade, ['B+', 'B']) => 'bg-blue-100 text-blue-700',
        in_array($grade, ['C+', 'C']) => 'bg-yellow-100 text-yellow-700',
        $grade === 'D'               => 'bg-orange-100 text-orange-700',
        default                      => 'bg-red-100 text-red-700',
    };
};
$perfRemark = function($pct) {
    if ($pct >= 90) return ['label' => 'Outstanding', 'cls' => 'bg-emerald-100 text-emerald-700'];
    if ($pct >= 80) return ['label' => 'Excellent',   'cls' => 'bg-green-100 text-green-700'];
    if ($pct >= 70) return ['label' => 'Very Good',   'cls' => 'bg-blue-100 text-blue-700'];
    if ($pct >= 60) return ['label' => 'Good',        'cls' => 'bg-indigo-100 text-indigo-700'];
    if ($pct >= 50) return ['label' => 'Average',     'cls' => 'bg-yellow-100 text-yellow-700'];
    if ($pct >= 40) return ['label' => 'Below Avg',   'cls' => 'bg-orange-100 text-orange-700'];
    if ($pct >= 33) return ['label' => 'Pass',        'cls' => 'bg-gray-100 text-gray-600'];
    return ['label' => 'Fail', 'cls' => 'bg-red-100 text-red-700'];
};
@endphp

{{-- ===== STICKY HEADER + TABS ===== --}}
<div class="bg-white border-b border-gray-200 sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        <div class="py-3 sm:py-4 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-gray-900">Performance</h1>
                <p class="text-xs text-gray-500 hidden sm:block">Track & manage student exam results</p>
            </div>
            <button wire:click="openUploadMarks"
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span class="hidden sm:inline">Upload Marks</span>
                <span class="sm:hidden">Marks</span>
            </button>
        </div>

        <nav class="flex overflow-x-auto">
            @foreach ([
                'subject'    => 'View by Subject',
                'student'    => 'View by Student',
                'performers' => 'Performers',
            ] as $tab => $label)
            <button wire:click="showTab('{{ $tab }}')"
                class="py-3.5 px-5 text-sm font-semibold whitespace-nowrap border-b-2 transition-colors
                    {{ $activeTab === $tab ? 'border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
            @endforeach
        </nav>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-5">

    {{-- ═══════════════════════════════════════════════════════════════════════
         VIEW BY SUBJECT TAB
    ═══════════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'subject')

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" placeholder="Name, exam, subject…"
                        class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                </div>
            </div>
            @foreach ([
                ['filterExam',     'Exam',    $exams,     'exam_name', 'All Exams'],
                ['filterStandard', 'Class',   $standards, 'name',      'All Classes'],
                ['filterSection',  'Section', $sections,  'name',      'All Sections'],
                ['filterSubject',  'Subject', $subjects,  'name',      'All Subjects'],
            ] as [$model, $label, $options, $key, $placeholder])
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ $label }}</label>
                <select wire:model.live="{{ $model }}"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                    <option value="">{{ $placeholder }}</option>
                    @foreach ($options as $opt)
                    <option value="{{ $opt->id }}">{{ $opt->$key }}</option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>
        <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-gray-100">
            <div class="flex items-center gap-2">
                <label class="text-xs text-gray-500">Show</label>
                <select wire:model.live="perPage"
                    class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <label class="text-xs text-gray-500">per page</label>
            </div>
            <p class="text-xs text-gray-400">
                @if ($examCopies instanceof \Illuminate\Pagination\LengthAwarePaginator && $examCopies->total() > 0)
                    Showing {{ $examCopies->firstItem() }}–{{ $examCopies->lastItem() }} of {{ $examCopies->total() }} records
                @else
                    No records found
                @endif
            </p>
        </div>
    </div>

    {{-- Records list --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-10">Sr</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Adm No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Class</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Exam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Marks</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Grade</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($examCopies as $ec)
                    @php $sr = ($examCopies->currentPage() - 1) * $examCopies->perPage() + $loop->iteration; @endphp

                    @if ($editingMarkId === $ec->id)
                    {{-- Edit row --}}
                    <tr class="bg-blue-50">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $sr }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if ($ec->studentDetail?->image)
                                <img src="{{ $ec->studentDetail->image }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                                @else
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-indigo-600">{{ strtoupper(substr($ec->studentDetail?->user?->name ?? 'S', 0, 1)) }}</span>
                                </div>
                                @endif
                                <span class="font-medium text-gray-800 text-sm">{{ $ec->studentDetail?->user?->name ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $ec->studentDetail?->admission_no ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $ec->standard?->name ?? '' }} {{ $ec->section?->name ?? '' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $ec->exam?->exam_name ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 text-xs">{{ $ec->subject?->name ?? '—' }}</td>
                        <td class="px-4 py-3" colspan="2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <input type="number" wire:model="editMarkData.marks_obtained"
                                    class="w-20 px-2 py-1.5 border border-blue-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Obtained">
                                <span class="text-gray-400 text-xs">/ </span>
                                <input type="number" wire:model="editMarkData.max_marks"
                                    class="w-20 px-2 py-1.5 border border-blue-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Max">
                                <input type="text" wire:model="editMarkData.remarks"
                                    class="w-32 px-2 py-1.5 border border-blue-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400"
                                    placeholder="Remark">
                            </div>
                            @error('editMarkData.marks_obtained') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 justify-center">
                                <button wire:click="saveEditMark"
                                    class="px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg hover:bg-blue-700 transition">Save</button>
                                <button wire:click="cancelEdit"
                                    class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-200 transition">Cancel</button>
                            </div>
                        </td>
                    </tr>
                    @else
                    {{-- Normal row --}}
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-gray-400 text-xs font-medium">{{ $sr }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                @if ($ec->studentDetail?->image)
                                <img src="{{ $ec->studentDetail->image }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border border-gray-100">
                                @else
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-semibold text-indigo-600">{{ strtoupper(substr($ec->studentDetail?->user?->name ?? 'S', 0, 1)) }}</span>
                                </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $ec->studentDetail?->user?->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">Roll: {{ $ec->studentDetail?->roll_no ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $ec->studentDetail?->admission_no ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium text-gray-600">{{ $ec->standard?->name ?? '' }}</span>
                            <span class="text-xs text-gray-400"> · {{ $ec->section?->name ?? '' }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-600 text-sm">{{ $ec->exam?->exam_name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-sm text-gray-700">{{ $ec->subject?->name ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div>
                                <span class="font-semibold text-gray-800">{{ $ec->marks_obtained }}</span>
                                <span class="text-gray-400 text-xs">/{{ $ec->max_marks }}</span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $ec->percentage }}%</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $gradeBadge($ec->grade) }}">
                                {{ $ec->grade }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-1.5 justify-center">
                                <button wire:click="onView({{ $ec->id }})"
                                    title="View"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-blue-600 hover:bg-blue-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
                                <button wire:click="onEdit({{ $ec->id }})"
                                    title="Edit"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-amber-600 hover:bg-amber-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <button wire:click="onDelete({{ $ec->id }})"
                                    title="Delete"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-red-500 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                </div>
                                <p class="text-sm font-medium text-gray-600">No exam records found</p>
                                <p class="text-xs text-gray-400">
                                    {{ ($search || $filterExam || $filterStandard || $filterSection || $filterSubject) ? 'Try adjusting your filters' : 'Upload marks to see records here' }}
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($examCopies instanceof \Illuminate\Pagination\LengthAwarePaginator && $examCopies->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $examCopies->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════
         VIEW BY STUDENT TAB
    ═══════════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'student')

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">Search Student Performance</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            @foreach ([
                ['selectedExam',     'Exam',    $exams,     'exam_name', null],
                ['selectedStandard', 'Class',   $standards, 'name',      null],
                ['selectedSection',  'Section', $sections,  'name',      'selectedStandard'],
                ['selectedStudent',  'Student', $students,  null,        'selectedSection'],
            ] as [$model, $label, $items, $nameKey, $dep])
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ $label }}</label>
                <select wire:model.live="{{ $model }}"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 disabled:opacity-50"
                    {{ ($dep && !$$dep) ? 'disabled' : '' }}>
                    <option value="">Select {{ $label }}</option>
                    @foreach ($items as $item)
                    <option value="{{ $item->id }}">
                        {{ $nameKey ? $item->$nameKey : ($item->user?->name ?? $item->full_name ?? 'N/A') }}
                    </option>
                    @endforeach
                </select>
            </div>
            @endforeach
        </div>
        <div class="flex justify-end gap-2">
            <button wire:click="searchPerformance"
                class="px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition shadow-sm">
                <span wire:loading.remove wire:target="searchPerformance">Search</span>
                <span wire:loading wire:target="searchPerformance">Searching…</span>
            </button>
        </div>
    </div>

    @if (count($studentPerformance) > 0)
    @php
        $student = collect($students)->firstWhere('id', $selectedStudent);
        $totalObt = collect($studentPerformance)->sum('marks_obtained');
        $totalMax = collect($studentPerformance)->sum('max_marks');
        $overallPct = $totalMax > 0 ? round(($totalObt / $totalMax) * 100, 2) : 0;
    @endphp

    {{-- Student summary card --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center gap-4">
            @if ($student?->image)
            <img src="{{ $student->image }}" class="w-16 h-16 rounded-2xl object-cover border-2 border-gray-100 shadow-sm">
            @else
            <div class="w-16 h-16 rounded-2xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                <span class="text-xl font-bold text-indigo-600">{{ strtoupper(substr($student?->user?->name ?? 'S', 0, 1)) }}</span>
            </div>
            @endif
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-900">{{ $student?->user?->name ?? 'N/A' }}</h2>
                <div class="flex flex-wrap gap-3 mt-1">
                    <span class="text-xs text-gray-500">Roll: {{ $student?->roll_no ?? '—' }}</span>
                    <span class="text-xs text-gray-500">Adm: {{ $student?->admission_no ?? '—' }}</span>
                </div>
            </div>
            <div class="text-right">
                <p class="text-2xl font-bold text-blue-600">{{ $overallPct }}%</p>
                <p class="text-xs text-gray-400">Overall</p>
            </div>
        </div>
    </div>

    {{-- Subject-wise table --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Subject-wise Performance</h3>
            <span class="ml-auto text-xs text-gray-400">{{ count($studentPerformance) }} subject(s)</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Sr</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Subject</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Obtained</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Max</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">%</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Grade</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($studentPerformance as $i => $perf)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-gray-400 text-xs font-medium">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-800">{{ $perf['subject']['name'] ?? '—' }}</td>
                        <td class="px-5 py-3 font-bold text-blue-600">{{ $perf['marks_obtained'] ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $perf['max_marks'] ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-200 rounded-full h-1.5 w-16">
                                    <div class="h-1.5 rounded-full {{ ($perf['percentage'] ?? 0) >= 60 ? 'bg-green-500' : (($perf['percentage'] ?? 0) >= 33 ? 'bg-yellow-500' : 'bg-red-500') }}"
                                        style="width: {{ min($perf['percentage'] ?? 0, 100) }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-600">{{ $perf['percentage'] ?? 0 }}%</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $gradeBadge($perf['grade'] ?? 'F') }}">
                                {{ $perf['grade'] ?? '—' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $perf['remarks'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                    {{-- Total row --}}
                    <tr class="bg-blue-50 font-semibold">
                        <td class="px-5 py-3" colspan="2">
                            <span class="text-sm text-blue-700">Total</span>
                        </td>
                        <td class="px-5 py-3 text-blue-700">{{ $totalObt }}</td>
                        <td class="px-5 py-3 text-blue-600">{{ $totalMax }}</td>
                        <td class="px-5 py-3 text-blue-700 font-bold">{{ $overallPct }}%</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @elseif ($selectedExam && $selectedStudent)
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-8 text-center">
        <svg class="w-10 h-10 text-yellow-400 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        <p class="text-sm font-medium text-yellow-700">No performance records found for the selected criteria.</p>
    </div>
    @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════
         PERFORMERS TAB
    ═══════════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'performers')

    {{-- Mode switcher --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Filter Mode</p>
        <div class="flex flex-wrap gap-2 mb-5">
            @foreach ([
                'class-section'         => 'Class → Section',
                'exam-class-section'    => 'Exam → Class → Section',
                'class-section-subject' => 'Class → Section → Subject',
            ] as $mode => $label)
            <button wire:click="$set('perfMode', '{{ $mode }}')"
                class="px-4 py-2 text-sm font-semibold rounded-xl border transition
                    {{ $perfMode === $mode
                        ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                        : 'bg-white text-gray-600 border-gray-200 hover:border-blue-300 hover:text-blue-600' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Filters for each mode --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Exam (for exam mode) --}}
            @if ($perfMode === 'exam-class-section')
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Exam</label>
                <select wire:model.live="perfExam"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                    <option value="">Select Exam</option>
                    @foreach ($exams as $exam)
                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Class --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Class</label>
                <select wire:model.live="perfStandard"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50">
                    <option value="">Select Class</option>
                    @foreach ($standards as $standard)
                    <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Section --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Section</label>
                <select wire:model.live="perfSection"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 disabled:opacity-50"
                    {{ !$perfStandard ? 'disabled' : '' }}>
                    <option value="">Select Section</option>
                    @foreach ($perfSections as $sec)
                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Subject (for subject mode) --}}
            @if ($perfMode === 'class-section-subject')
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Subject</label>
                <select wire:model.live="perfSubject"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50 disabled:opacity-50"
                    {{ !$perfSection ? 'disabled' : '' }}>
                    <option value="">Select Subject</option>
                    @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </div>

    @if (count($performers) > 0)

    {{-- Top 3 Podium --}}
    @php
        $top3 = array_slice($performers, 0, 3);
        $podiumOrder = count($top3) >= 3 ? [1, 0, 2] : array_keys($top3); // 2nd, 1st, 3rd display order
        $podiumColors = [
            0 => ['bg' => 'bg-amber-400',   'ring' => 'ring-amber-300',   'text' => 'text-amber-700',   'label' => '🥇 1st', 'card' => 'from-amber-50  to-yellow-50  border-amber-200'],
            1 => ['bg' => 'bg-slate-400',   'ring' => 'ring-slate-300',   'text' => 'text-slate-600',   'label' => '🥈 2nd', 'card' => 'from-slate-50  to-gray-50    border-slate-200'],
            2 => ['bg' => 'bg-orange-400',  'ring' => 'ring-orange-300',  'text' => 'text-orange-700',  'label' => '🥉 3rd', 'card' => 'from-orange-50 to-amber-50   border-orange-200'],
        ];
    @endphp

    <div class="grid grid-cols-3 gap-3 sm:gap-5">
        @foreach ($podiumOrder as $idx)
        @if (isset($top3[$idx]))
        @php
            $p = $top3[$idx];
            $c = $podiumColors[$idx];
            $remark = $perfRemark($p['percentage']);
        @endphp
        <div class="bg-gradient-to-b {{ $c['card'] }} border rounded-2xl p-4 sm:p-6 text-center shadow-sm {{ $idx === 0 ? 'sm:-mt-4 ring-2 ring-amber-300 shadow-lg' : '' }}">
            <span class="inline-block px-2 py-0.5 text-xs font-bold rounded-full mb-3 {{ $c['bg'] }} text-white shadow-sm">
                {{ $c['label'] }}
            </span>
            <div class="flex justify-center mb-3">
                @if ($p['student']?->image)
                <img src="{{ $p['student']->image }}" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full object-cover border-4 border-white shadow-md ring-2 {{ $c['ring'] }}">
                @else
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full {{ $c['bg'] }} border-4 border-white shadow-md flex items-center justify-center">
                    <span class="text-xl sm:text-2xl font-bold text-white">{{ strtoupper(substr($p['student']?->user?->name ?? 'S', 0, 1)) }}</span>
                </div>
                @endif
            </div>
            <p class="font-bold text-gray-900 text-sm sm:text-base truncate">{{ $p['student']?->user?->name ?? 'N/A' }}</p>
            <p class="text-xs text-gray-500 truncate mb-2">{{ $p['student']?->admission_no ?? '—' }}</p>
            <p class="text-2xl sm:text-3xl font-bold {{ $c['text'] }}">{{ $p['percentage'] }}%</p>
            <p class="text-xs text-gray-500 mt-0.5">{{ $p['total_obtained'] }} / {{ $p['total_max'] }}</p>
            <span class="inline-block mt-2 px-2 py-0.5 text-xs font-semibold rounded-full {{ $remark['cls'] }}">
                {{ $remark['label'] }}
            </span>
        </div>
        @endif
        @endforeach
    </div>

    {{-- Full ranked list --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-xl flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
                </svg>
            </div>
            <h3 class="font-semibold text-gray-900">Class Rankings</h3>
            <span class="ml-auto text-xs font-medium bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full">
                {{ count($performers) }} students
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-16">Rank</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Adm No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Class</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Marks</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">%</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Grade</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Remark</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($performers as $p)
                    @php
                        $remark = $perfRemark($p['percentage']);
                        $rankBg = match($p['rank']) {
                            1 => 'bg-amber-400 text-white',
                            2 => 'bg-slate-400 text-white',
                            3 => 'bg-orange-400 text-white',
                            default => 'bg-gray-100 text-gray-600',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors {{ $p['rank'] <= 3 ? 'bg-amber-50/30' : '' }}">
                        <td class="px-4 py-3">
                            <span class="inline-flex w-7 h-7 items-center justify-center rounded-full text-xs font-bold {{ $rankBg }}">
                                {{ $p['rank'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                @if ($p['student']?->image)
                                <img src="{{ $p['student']->image }}" class="w-9 h-9 rounded-full object-cover border border-gray-100 flex-shrink-0">
                                @else
                                <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-sm font-bold text-indigo-600">{{ strtoupper(substr($p['student']?->user?->name ?? 'S', 0, 1)) }}</span>
                                </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $p['student']?->user?->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">Roll: {{ $p['student']?->roll_no ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $p['student']?->admission_no ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs font-medium text-gray-600">
                                {{ $p['student']?->standard?->name ?? '—' }} {{ $p['student']?->section?->name ?? '' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-gray-800">{{ $p['total_obtained'] }}</span>
                            <span class="text-gray-400 text-xs">/{{ $p['total_max'] }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-blue-600">{{ $p['percentage'] }}%</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $gradeBadge($p['grade']) }}">
                                {{ $p['grade'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded-full {{ $remark['cls'] }}">
                                {{ $remark['label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @elseif ($perfStandard && $perfSection && ($perfMode === 'class-section' || ($perfMode === 'exam-class-section' && $perfExam) || ($perfMode === 'class-section-subject' && $perfSubject)))
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-8 text-center">
        <svg class="w-10 h-10 text-blue-400 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
        </svg>
        <p class="text-sm font-medium text-blue-700">No performance data found. Upload marks first.</p>
    </div>
    @else
    <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-10 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 007.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 002.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 012.916.52 6.003 6.003 0 01-5.395 4.972m0 0a6.726 6.726 0 01-2.749 1.35m0 0a6.772 6.772 0 01-3.044 0" />
        </svg>
        <p class="text-sm font-medium text-gray-500">Select filters to view performers</p>
    </div>
    @endif

    @endif

</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
     UPLOAD MARKS MODAL (full-height drawer)
════════════════════════════════════════════════════════════════════════════ --}}
@if ($showUploadModal)
<div class="fixed inset-0 bg-black/40 z-[999] flex justify-end" x-data>
    <div class="w-full max-w-5xl h-screen bg-white flex flex-col shadow-2xl"
        style="animation: slideIn 0.2s ease-out;">

        {{-- Modal header --}}
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-200 bg-white flex-shrink-0">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-gray-900">Upload Marks</h2>
                <p class="text-xs text-gray-500">Select exam, class, section & subject to enter marks</p>
            </div>
            <button wire:click="closeUploadModal" class="ml-auto text-gray-400 hover:text-gray-600 transition p-1.5 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Filter selects --}}
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50 flex-shrink-0">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                @foreach ([
                    ['uploadExam',     'Exam',    $exams,     'exam_name', null],
                    ['uploadStandard', 'Class',   $standards, 'name',      null],
                    ['uploadSection',  'Section', $sections,  'name',      'uploadStandard'],
                    ['uploadSubject',  'Subject', $subjects,  'name',      'uploadSection'],
                ] as [$model, $label, $items, $nameKey, $dep])
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ $label }}</label>
                    <select wire:model.live="{{ $model }}"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-white disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ ($dep && !$$dep) ? 'disabled' : '' }}>
                        <option value="">Select {{ $label }}</option>
                        @foreach ($items as $item)
                        <option value="{{ $item->id }}">{{ $item->$nameKey }}</option>
                        @endforeach
                    </select>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Student list --}}
        <div class="flex-1 overflow-y-auto">
            @if ($uploadExam && $uploadStandard && $uploadSection && $uploadSubject)
            @if (count($studentMarks) > 0)
            @php
                $uploadStd = collect($standards)->firstWhere('id', $uploadStandard);
                $uploadSec = collect($sections)->firstWhere('id', $uploadSection);
                $uploadSub = collect($subjects)->firstWhere('id', $uploadSubject);
            @endphp

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gray-100 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-10">Sr</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[160px]">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Adm No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Subject</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-24">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Marks</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide min-w-[120px]">Remark</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($studentMarks as $studentId => $marks)
                        @php
                            $isSaved   = $marks['saved'] ?? false;
                            $isEditing = $editingStudents[$studentId] ?? false;
                            $showInput = !$isSaved || $isEditing;
                        @endphp
                        <tr class="transition-colors {{ $isSaved && !$isEditing ? 'bg-green-50/40' : 'bg-white' }} hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400 text-xs font-medium">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    @if (!empty($marks['image']))
                                    <img src="{{ $marks['image'] }}" class="w-9 h-9 rounded-full object-cover border border-gray-100 flex-shrink-0">
                                    @else
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-indigo-600">{{ strtoupper(substr($marks['student_name'], 0, 1)) }}</span>
                                    </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $marks['student_name'] }}</p>
                                        <p class="text-xs text-gray-400">Roll: {{ $marks['roll_no'] ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $marks['admission_no'] ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                {{ $uploadStd?->name ?? '' }} <span class="text-gray-400">·</span> {{ $uploadSec?->name ?? '' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600">{{ $uploadSub?->name ?? '—' }}</td>

                            @if ($showInput)
                            {{-- Edit/New input mode --}}
                            <td class="px-4 py-3">
                                <input type="number"
                                    wire:model.live="studentMarks.{{ $studentId }}.max_marks"
                                    class="w-20 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50"
                                    min="1" placeholder="Max">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number"
                                    wire:model.live="studentMarks.{{ $studentId }}.marks_obtained"
                                    class="w-24 px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50"
                                    min="0" step="0.01" placeholder="Obtained">
                            </td>
                            <td class="px-4 py-3">
                                <input type="text"
                                    wire:model.live="studentMarks.{{ $studentId }}.remarks"
                                    class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 bg-gray-50"
                                    placeholder="Remark…">
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($isEditing)
                                <button wire:click="toggleEditStudent({{ $studentId }})"
                                    class="text-xs text-gray-500 hover:text-red-600 transition font-medium">Cancel</button>
                                @endif
                            </td>
                            @else
                            {{-- Saved state --}}
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-gray-700">{{ $marks['max_marks'] }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <span class="font-bold text-green-700">{{ $marks['marks_obtained'] }}</span>
                                    @if (!empty($marks['grade']))
                                    <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold {{ $gradeBadge($marks['grade']) }}">{{ $marks['grade'] }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ $marks['remarks'] ?: '—' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1 justify-center">
                                    @if ($marks['exam_copy_id'])
                                    <button wire:click="onView({{ $marks['exam_copy_id'] }})"
                                        title="View"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-blue-600 hover:bg-blue-50 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </button>
                                    <button wire:click="toggleEditStudent({{ $studentId }})"
                                        title="Edit"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-amber-600 hover:bg-amber-50 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button wire:click="onDelete({{ $marks['exam_copy_id'] }})"
                                        title="Delete"
                                        class="w-6 h-6 flex items-center justify-center rounded-md text-red-500 hover:bg-red-50 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-600">No students found in this section</p>
            </div>
            @endif
            @else
            <div class="flex flex-col items-center justify-center py-20 text-center px-8">
                <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 006-6v-1.5m-6 7.5a6 6 0 01-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 01-3-3V4.5a3 3 0 116 0v8.25a3 3 0 01-3 3z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-700">Select Exam, Class, Section & Subject</p>
                <p class="text-xs text-gray-400 mt-1">Student list will appear here</p>
            </div>
            @endif
        </div>

        {{-- Modal footer --}}
        @if ($uploadExam && $uploadStandard && $uploadSection && $uploadSubject && count($studentMarks) > 0)
        <div class="flex items-center justify-between px-5 py-4 border-t border-gray-200 bg-gray-50/50 flex-shrink-0">
            <p class="text-xs text-gray-400">{{ count($studentMarks) }} students · {{ collect($studentMarks)->where('saved', true)->count() }} saved</p>
            <div class="flex gap-2">
                <button wire:click="closeUploadModal"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                    Close
                </button>
                <button wire:click="uploadMarks" wire:loading.attr="disabled"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm disabled:opacity-60">
                    <span wire:loading.remove wire:target="uploadMarks">Save All Marks</span>
                    <span wire:loading wire:target="uploadMarks">Saving…</span>
                </button>
            </div>
        </div>
        @endif
    </div>
</div>
<style>
@keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
</style>
@endif

{{-- ═══════════════════════════════════════════════════════════════════════════
     VIEW DETAIL SLIDER
════════════════════════════════════════════════════════════════════════════ --}}
@if ($showSlider)
<div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
    <div class="relative w-full max-w-2xl mx-4 my-4">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col max-h-[calc(100vh-5rem)]">

            <div class="flex items-center justify-between p-5 border-b border-gray-100 flex-shrink-0">
                <h3 class="text-base font-bold text-gray-900">{{ $sliderTitle }}</h3>
                <button wire:click="closeSlider" class="text-gray-400 hover:text-gray-600 transition p-1 hover:bg-gray-100 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-5">
                @if (isset($sliderData['exam_copy']))
                @php $ec = $sliderData['exam_copy']; @endphp

                <div class="grid grid-cols-2 gap-3">
                    @foreach ([
                        ['Student',  $ec->studentDetail?->user?->name ?? 'N/A', 'indigo'],
                        ['Exam',     $ec->exam?->exam_name ?? 'N/A',            'blue'],
                        ['Class',    ($ec->standard?->name ?? '') . ' ' . ($ec->section?->name ?? ''), 'purple'],
                        ['Subject',  $ec->subject?->name ?? 'N/A',              'teal'],
                    ] as [$label, $value, $color])
                    <div class="p-3 bg-{{ $color }}-50 rounded-xl border border-{{ $color }}-100">
                        <p class="text-xs font-semibold uppercase tracking-wide text-{{ $color }}-500 mb-0.5">{{ $label }}</p>
                        <p class="font-semibold text-gray-800 text-sm">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @foreach ([
                        ['Obtained',   $ec->marks_obtained,    'bg-blue-50   border-blue-100   text-blue-600   text-blue-700'],
                        ['Max Marks',  $ec->max_marks,         'bg-green-50  border-green-100  text-green-600  text-green-700'],
                        ['Percentage', $ec->percentage . '%',  'bg-purple-50 border-purple-100 text-purple-600 text-purple-700'],
                        ['Grade',      $ec->grade,             'bg-orange-50 border-orange-100 text-orange-600 text-orange-700'],
                    ] as [$label, $value, $cls])
                    @php [$bg, $border, $label_cls, $val_cls] = explode(' ', $cls); @endphp
                    <div class="p-4 {{ $bg }} rounded-xl border {{ $border }} text-center">
                        <p class="text-xs font-semibold {{ $label_cls }} mb-1">{{ $label }}</p>
                        <p class="text-2xl font-bold {{ $val_cls }}">{{ $value }}</p>
                    </div>
                    @endforeach
                </div>

                @if ($ec->remarks)
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Remarks</p>
                    <p class="text-sm text-gray-700">{{ $ec->remarks }}</p>
                </div>
                @endif
                @endif
            </div>

            <div class="flex justify-end gap-2 px-5 py-4 border-t border-gray-100 flex-shrink-0">
                <button wire:click="closeSlider"
                    class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition">Close</button>
            </div>
        </div>
    </div>
</div>
@endif

</div>
