<div class="min-h-screen bg-gray-50" x-data="{ expandedChapters: @entangle('expandedChapters').live, viewSlide: 0 }">

    {{-- HEADER --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">MCQ Management</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage questions and student answers</p>
            </div>
            <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 divide-x divide-gray-200">
                <span class="pr-4">Questions: <strong class="text-blue-600">{{ $totalQuestions }}</strong></span>
                <span class="pl-4">Answers: <strong class="text-emerald-600">{{ $totalAnswers }}</strong></span>
            </div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6">
        <nav class="flex gap-1">
            @php $tabs = [['key' => 'mcq_questions', 'label' => 'MCQ Questions', 'color' => 'blue', 'count' => $totalQuestions], ['key' => 'student_answers', 'label' => 'Student Answers', 'color' => 'emerald', 'count' => $totalAnswers]]; @endphp
            @foreach ($tabs as $tab)
                <button wire:click="showTab('{{ $tab['key'] }}')"
                    class="relative py-3.5 px-4 text-sm font-semibold transition-colors border-b-2
                        {{ $activeTab === $tab['key']
                            ? 'border-' . $tab['color'] . '-500 text-' . $tab['color'] . '-700'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $tab['label'] }}
                    @if ($tab['count'] > 0)
                        <span
                            class="ml-1.5 px-1.5 py-0.5 text-xs rounded-full
                            {{ $activeTab === $tab['key']
                                ? 'bg-' . $tab['color'] . '-100 text-' . $tab['color'] . '-700'
                                : 'bg-gray-100 text-gray-600' }}">{{ $tab['count'] }}</span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">

        {{-- FILTERS --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-{{ $activeTab === 'student_answers' ? '5' : '4' }} gap-3">
                <select wire:model.live="filterStandard"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">Select Class *</option>
                    @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterSection"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white disabled:opacity-50"
                    @disabled(!$filterStandard)>
                    <option value="">All Sections</option>
                    @foreach ($filterSections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterSubject"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white disabled:opacity-50"
                    @disabled(!$filterStandard)>
                    <option value="">Select Subject *</option>
                    @foreach ($filterSubjects as $subj)
                        <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                    @endforeach
                </select>
                @if ($activeTab === 'student_answers')
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input wire:model.live.debounce.300ms="answerSearch" type="text"
                            placeholder="Search student..."
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                    </div>
                @endif
                <div class="flex items-center">
                    @if ($filterStandard || $filterSection || $filterSubject || $answerSearch)
                        <button wire:click="clearFilters"
                            class="px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            @if ($activeTab === 'mcq_questions' && !$showList)
                <p class="text-xs text-amber-600 mt-2">Select a class and subject to view chapters & topics.</p>
            @endif
        </div>

        {{-- ═══════════════════════════════════════════════
             TAB: MCQ QUESTIONS
        ═══════════════════════════════════════════════ --}}
        @if ($activeTab === 'mcq_questions')
            @if ($showList)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="divide-y divide-gray-200">
                        @forelse ($chapters as $chapter)
                            @php $chMcqCount = $chapterMcqCounts[$chapter->id] ?? 0; @endphp
                            <div>
                                <div class="px-4 sm:px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition cursor-pointer"
                                    @click="expandedChapters.includes({{ $chapter->id }}) ? expandedChapters = expandedChapters.filter(i => i !== {{ $chapter->id }}) : expandedChapters = [...expandedChapters, {{ $chapter->id }}]">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 flex-shrink-0"
                                            :class="{ 'rotate-90': expandedChapters.includes({{ $chapter->id }}) }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                        <span
                                            class="text-xs font-medium text-gray-500 px-1.5 py-0.5 bg-blue-100 rounded flex-shrink-0">Ch
                                            {{ $chapter->order }}</span>
                                        <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $chapter->name }}
                                        </h3>
                                        <span
                                            class="text-xs text-gray-400 flex-shrink-0">{{ $chapter->topics->count() }}
                                            topics</span>
                                        @if ($chMcqCount > 0)
                                            <span
                                                class="text-xs px-1.5 py-0.5 bg-blue-100 text-blue-700 rounded font-medium flex-shrink-0">{{ $chMcqCount }}
                                                MCQ</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-1 ml-2 flex-shrink-0" @click.stop>
                                        @if ($chMcqCount > 0)
                                            <button wire:click="onViewMcq('chapter', {{ $chapter->id }})"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg"
                                                title="View"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg></button>
                                            <button wire:click="onEditMcq('chapter', {{ $chapter->id }})"
                                                class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg"
                                                title="Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg></button>
                                            <button wire:click="onDeleteMcq('chapter', {{ $chapter->id }})"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg"
                                                title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg></button>
                                        @else
                                            <button wire:click="onAddMcq('chapter', {{ $chapter->id }})"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg> Add MCQ
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                {{-- Topics --}}
                                <div x-show="expandedChapters.includes({{ $chapter->id }})" x-collapse
                                    class="bg-gray-50">
                                    @forelse ($chapter->topics as $topic)
                                        @php $tpMcqCount = $topicMcqCounts[$topic->id] ?? 0; @endphp
                                        <div
                                            class="px-8 sm:px-12 py-2.5 flex items-center justify-between hover:bg-white border-b border-gray-100 last:border-b-0 transition">
                                            <div class="flex items-center gap-2 flex-1 min-w-0">
                                                <span
                                                    class="w-1.5 h-1.5 bg-emerald-500 rounded-full flex-shrink-0"></span>
                                                <span
                                                    class="text-sm text-gray-800 truncate">{{ $topic->topic_name }}</span>
                                                @if ($tpMcqCount > 0)
                                                    <span
                                                        class="text-xs px-1.5 py-0.5 bg-emerald-100 text-emerald-700 rounded font-medium flex-shrink-0">{{ $tpMcqCount }}
                                                        MCQ</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-1 ml-2 flex-shrink-0">
                                                @if ($tpMcqCount > 0)
                                                    <button wire:click="onViewMcq('topic', {{ $topic->id }})"
                                                        class="p-1 text-blue-600 hover:bg-blue-50 rounded-lg"
                                                        title="View"><svg class="w-3.5 h-3.5" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg></button>
                                                    <button wire:click="onEditMcq('topic', {{ $topic->id }})"
                                                        class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg"
                                                        title="Edit"><svg class="w-3.5 h-3.5" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg></button>
                                                    <button wire:click="onDeleteMcq('topic', {{ $topic->id }})"
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded-lg"
                                                        title="Delete"><svg class="w-3.5 h-3.5" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg></button>
                                                @else
                                                    <button wire:click="onAddMcq('topic', {{ $topic->id }})"
                                                        class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg> Add MCQ
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @empty
                                        <div class="px-12 py-4 text-center text-xs text-gray-400">No topics</div>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div class="px-6 py-16 text-center">
                                <p class="text-gray-500 text-sm">No chapters found for this selection</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @elseif (!$showList)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-16 text-center">
                    <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <p class="text-gray-700 text-sm font-medium">Select a class and subject to manage MCQs</p>
                </div>
            @endif
        @endif

        {{-- ═══════════════════════════════════════════════
             TAB: STUDENT ANSWERS
        ═══════════════════════════════════════════════ --}}
        @if ($activeTab === 'student_answers')
            @if ($answers && $answers->count() > 0)
                <div class="hidden md:block bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-12">
                                        S.No</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Question</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Selected Answer</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                        Result</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($answers as $i => $ans)
                                    <tr class="hover:bg-gray-50/70">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $answers->firstItem() + $i }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $ans->user?->name ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700 max-w-[250px] truncate">
                                            {{ $ans->question?->question_text ?? '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $ans->selectedOption?->option_text ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($ans->is_correct)
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-green-50 text-green-700 rounded-full font-medium border border-green-100">
                                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span> Correct
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-1 text-xs px-2.5 py-1 bg-red-50 text-red-600 rounded-full font-medium border border-red-100">
                                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span> Wrong
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $ans->time_taken ? $ans->time_taken . 's' : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($answers->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                            <p class="text-sm text-gray-500">{{ $answers->firstItem() }}–{{ $answers->lastItem() }}
                                of {{ $answers->total() }}</p>
                            <div class="flex items-center gap-1">
                                @if (!$answers->onFirstPage())
                                    <button wire:click="previousPage"
                                        class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">&laquo;
                                        Prev</button>
                                @endif
                                @if ($answers->hasMorePages())
                                    <button wire:click="nextPage"
                                        class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">Next
                                        &raquo;</button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Mobile cards --}}
                <div class="md:hidden space-y-3">
                    @foreach ($answers as $i => $ans)
                        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900">{{ $ans->user?->name ?? '—' }}</span>
                                @if ($ans->is_correct)
                                    <span
                                        class="text-xs px-2 py-0.5 bg-green-50 text-green-700 rounded-full font-medium border border-green-100">Correct</span>
                                @else
                                    <span
                                        class="text-xs px-2 py-0.5 bg-red-50 text-red-600 rounded-full font-medium border border-red-100">Wrong</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 truncate">Q: {{ $ans->question?->question_text ?? '—' }}
                            </p>
                            <p class="text-xs text-gray-600">Ans: {{ $ans->selectedOption?->option_text ?? '—' }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-16 text-center">
                    <p class="text-gray-500 text-sm">No student answers found</p>
                </div>
            @endif
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════
         ADD MCQ MODAL
    ═══════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openAddModal }}" title="Add MCQs — {{ $addTargetName }}" submitAction="onSaveMcqs"
        submitButton="Save MCQs" closeAction="closeAddModal">

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700">Questions</h3>
                <button wire:click="addMcqRow" type="button"
                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Question
                </button>
            </div>

            @foreach ($mcqRows as $qi => $row)
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase">Question {{ $qi + 1 }}</span>
                        @if (count($mcqRows) > 1)
                            <button wire:click="removeMcqRow({{ $qi }})" type="button"
                                class="p-1 text-red-400 hover:text-red-600 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-medium text-gray-500 mb-1">Question *</label>
                            <input type="text" wire:model="mcqRows.{{ $qi }}.question_text"
                                placeholder="Enter question..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Time (sec)</label>
                            <input type="number" wire:model="mcqRows.{{ $qi }}.time_limit" min="5"
                                max="300"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($row['options'] as $oi => $opt)
                            <div class="flex items-center gap-2">
                                <button wire:click="setCorrectOption({{ $qi }}, {{ $oi }})"
                                    type="button"
                                    class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors
                                        {{ $opt['is_correct'] ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 hover:border-emerald-400' }}">
                                    @if ($opt['is_correct'])
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                                <input type="text"
                                    wire:model="mcqRows.{{ $qi }}.options.{{ $oi }}.text"
                                    placeholder="Option {{ $oi + 1 }}"
                                    class="flex-1 px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500
                                        {{ $opt['is_correct'] ? 'border-emerald-300 bg-emerald-50' : 'border-gray-300' }}">
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400">Click the circle to mark the correct answer</p>
                </div>
            @endforeach
        </div>
    </x-modal-form>

    {{-- ═══════════════════════════════════════════════
         EDIT MCQ MODAL
    ═══════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openEditModal }}" title="Edit MCQs — {{ $editTargetName }}" submitAction="onUpdateMcqs"
        submitButton="Update MCQs" closeAction="closeEditModal">

        <div class="space-y-4">
            @foreach ($editMcqs as $qi => $mcq)
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
                    <span class="text-xs font-bold text-gray-400 uppercase">Question {{ $qi + 1 }}</span>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-3">
                            <input type="text" wire:model="editMcqs.{{ $qi }}.question_text"
                                placeholder="Question..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <input type="number" wire:model="editMcqs.{{ $qi }}.time_limit"
                                min="5"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ($mcq['options'] as $oi => $opt)
                            <div class="flex items-center gap-2">
                                <button wire:click="setEditCorrectOption({{ $qi }}, {{ $oi }})"
                                    type="button"
                                    class="flex-shrink-0 w-6 h-6 rounded-full border-2 flex items-center justify-center transition-colors
                                        {{ $opt['is_correct'] ? 'border-emerald-500 bg-emerald-500' : 'border-gray-300 hover:border-emerald-400' }}">
                                    @if ($opt['is_correct'])
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </button>
                                <input type="text"
                                    wire:model="editMcqs.{{ $qi }}.options.{{ $oi }}.text"
                                    class="flex-1 px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-blue-500
                                        {{ $opt['is_correct'] ? 'border-emerald-300 bg-emerald-50' : 'border-gray-300' }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </x-modal-form>

    {{-- ═══════════════════════════════════════════════
         VIEW MCQ MODAL (Swipeable)
    ═══════════════════════════════════════════════ --}}
    @if ($openViewModal)
        <div class="fixed inset-0 z-[60] overflow-y-auto" aria-modal="true" x-data="{ slide: 0, total: {{ count($viewMcqs) }} }"
            x-init="slide = 0" @keydown.right.window="slide = Math.min(total - 1, slide + 1)"
            @keydown.left.window="slide = Math.max(0, slide - 1)">
            <div class="flex items-center justify-center min-h-screen px-4 py-8">
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" wire:click="closeViewModal"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-auto z-10 overflow-hidden">

                    {{-- Header --}}
                    <div
                        class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <div>
                            <h2 class="text-base font-bold text-gray-900">{{ $viewTargetName }}</h2>
                            <p class="text-xs text-gray-500 mt-0.5"
                                x-text="'Question ' + (slide + 1) + ' of ' + total"></p>
                        </div>
                        <button wire:click="closeViewModal"
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-white/60 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Progress bar --}}
                    <div class="h-1 bg-gray-100">
                        <div class="h-1 bg-blue-500 transition-all duration-300 rounded-r"
                            :style="'width: ' + ((slide + 1) / total * 100) + '%'"></div>
                    </div>

                    {{-- Slides --}}
                    <div class="px-6 py-6 min-h-[320px]">
                        @foreach ($viewMcqs as $qi => $mcq)
                            <div x-show="slide === {{ $qi }}"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 translate-x-8"
                                x-transition:enter-end="opacity-100 translate-x-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

                                <div class="flex items-center gap-2 mb-4">
                                    <span
                                        class="text-xs font-bold text-white bg-blue-600 px-2.5 py-1 rounded-lg">Q{{ $qi + 1 }}</span>
                                    @if ($mcq['time_limit'])
                                        <span
                                            class="inline-flex items-center gap-1 text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-lg">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $mcq['time_limit'] }}s
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-base font-semibold text-gray-900 mb-5 leading-relaxed">
                                    {{ $mcq['question_text'] }}</h3>

                                <div class="space-y-2.5">
                                    @foreach ($mcq['options'] as $oi => $opt)
                                        <div
                                            class="flex items-center gap-3 px-4 py-3 rounded-xl border-2 transition-all
                                        {{ $opt['is_correct']
                                            ? 'border-emerald-400 bg-emerald-50 shadow-sm shadow-emerald-100'
                                            : 'border-gray-200 bg-white' }}">
                                            <span
                                                class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                            {{ $opt['is_correct'] ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-500' }}">
                                                {{ chr(65 + $oi) }}
                                            </span>
                                            <span
                                                class="text-sm flex-1 {{ $opt['is_correct'] ? 'text-emerald-800 font-semibold' : 'text-gray-700' }}">
                                                {{ $opt['text'] }}
                                            </span>
                                            @if ($opt['is_correct'])
                                                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0"
                                                    fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Footer Navigation --}}
                    <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        <button @click="slide = Math.max(0, slide - 1)" :disabled="slide === 0"
                            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                            Prev
                        </button>

                        {{-- Dots --}}
                        <div class="flex items-center gap-1.5 max-w-[200px] overflow-x-auto px-2">
                            @foreach ($viewMcqs as $qi => $mcq)
                                <button @click="slide = {{ $qi }}"
                                    class="flex-shrink-0 w-2.5 h-2.5 rounded-full transition-all duration-200"
                                    :class="slide === {{ $qi }} ? 'bg-blue-600 scale-125' :
                                        'bg-gray-300 hover:bg-gray-400'"></button>
                            @endforeach
                        </div>

                        <button @click="slide = Math.min(total - 1, slide + 1)" :disabled="slide === total - 1"
                            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-40 disabled:cursor-not-allowed transition">
                            Next
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════
         DELETE MCQ MODAL (Select from list)
    ═══════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openDeleteModal }}" title="Delete MCQs — {{ $deleteTargetName }}"
        submitAction="onConfirmDelete" submitButton="Delete Selected" closeAction="closeDeleteModal">

        <div class="space-y-3">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-gray-600">Select MCQs to delete:</p>
                <button wire:click="selectAllForDelete" type="button"
                    class="text-xs text-blue-600 hover:text-blue-800 font-medium">Select All</button>
            </div>

            @foreach ($deleteMcqList as $mcq)
                <div class="flex items-start gap-3 p-3 rounded-lg border transition-colors cursor-pointer
                    {{ in_array($mcq['id'], $selectedDeleteIds) ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}"
                    wire:click="toggleDeleteSelect({{ $mcq['id'] }})">
                    <div class="flex-shrink-0 mt-0.5">
                        <div
                            class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors
                            {{ in_array($mcq['id'], $selectedDeleteIds) ? 'border-red-500 bg-red-500' : 'border-gray-300' }}">
                            @if (in_array($mcq['id'], $selectedDeleteIds))
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </div>
                    </div>
                    <p class="text-sm text-gray-800 flex-1">{{ $mcq['question_text'] }}</p>
                </div>
            @endforeach

            @if (!empty($selectedDeleteIds))
                <p class="text-xs text-red-600 font-medium mt-2">{{ count($selectedDeleteIds) }} MCQ(s) selected for
                    deletion</p>
            @endif
        </div>
    </x-modal-form>

</div>
