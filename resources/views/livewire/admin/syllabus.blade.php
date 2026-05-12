<div class="min-h-screen bg-gray-50" x-data="{
    expandedSubjects: @entangle('expandedSubjects').live,
    expandedChapters: @entangle('expandedChapters').live,
}">

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Syllabus</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage chapters and topics</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                    <span class="pr-4">Standards: <strong class="text-gray-800">{{ $totalStandards }}</strong></span>
                    <span class="px-4">Subjects: <strong class="text-purple-600">{{ $totalSubjects }}</strong></span>
                    <span class="px-4">Chapters: <strong class="text-blue-600">{{ $totalChapters }}</strong></span>
                    <span class="pl-4">Topics: <strong class="text-emerald-600">{{ $totalTopics }}</strong></span>
                </div>
                <button wire:click="onAddChapter"
                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Add Chapter</span>
                </button>
                <button wire:click="onAddTopic"
                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Add Topic</span>
                </button>
            </div>
        </div>
        <div class="flex lg:hidden items-center gap-3 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Standards: <strong class="text-gray-800">{{ $totalStandards }}</strong></span>
            <span>Subjects: <strong class="text-purple-600">{{ $totalSubjects }}</strong></span>
            <span>Chapters: <strong class="text-blue-600">{{ $totalChapters }}</strong></span>
            <span>Topics: <strong class="text-emerald-600">{{ $totalTopics }}</strong></span>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">

        {{-- ══════════════════════════════════════════════════
             FILTERS
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <div class="sm:col-span-2 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Search subjects, chapters, topics..."
                        class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                </div>
                <select wire:model.live="filterStandard"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">All Classes</option>
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
                <div class="flex gap-2">
                    <select wire:model.live="filterSubject"
                        class="flex-1 px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 bg-white disabled:opacity-50"
                        @disabled(!$filterStandard)>
                        <option value="">All Subjects</option>
                        @foreach ($filterSubjectsList as $subj)
                            <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                        @endforeach
                    </select>
                    @if ($search || $filterStandard || $filterSection || $filterSubject)
                        <button wire:click="clearFilters" title="Clear filters"
                            class="px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50 flex-shrink-0">
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
             SUBJECT → CHAPTER → TOPIC HIERARCHY
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-200">
                @forelse ($subjects as $subject)
                    <div>
                        {{-- Subject Header --}}
                        <div class="px-4 sm:px-6 py-4 flex items-center justify-between bg-gradient-to-r from-purple-50 to-indigo-50 cursor-pointer"
                            @click="expandedSubjects.includes({{ $subject->id }}) ? expandedSubjects = expandedSubjects.filter(i => i !== {{ $subject->id }}) : expandedSubjects = [...expandedSubjects, {{ $subject->id }}]">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <svg class="w-5 h-5 text-purple-600 transition-transform duration-200 flex-shrink-0"
                                    :class="{ 'rotate-90': expandedSubjects.includes({{ $subject->id }}) }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h2 class="text-sm sm:text-base font-bold text-gray-900">{{ $subject->name }}
                                        </h2>
                                        @if ($subject->standards->first())
                                            <span
                                                class="text-xs px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full font-medium">
                                                {{ $subject->standards->first()->name }}
                                            </span>
                                        @endif
                                        <span class="text-xs text-gray-500">{{ $subject->chapters->count() }}
                                            chapters</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Chapters --}}
                        <div x-show="expandedSubjects.includes({{ $subject->id }})" x-collapse class="bg-gray-50">
                            @forelse ($subject->chapters as $chapter)
                                <div class="border-b border-gray-200 last:border-b-0">
                                    {{-- Chapter Header --}}
                                    <div class="px-6 sm:px-10 py-3 flex items-center justify-between hover:bg-white transition cursor-pointer"
                                        @click="expandedChapters.includes({{ $chapter->id }}) ? expandedChapters = expandedChapters.filter(i => i !== {{ $chapter->id }}) : expandedChapters = [...expandedChapters, {{ $chapter->id }}]">
                                        <div class="flex items-center gap-3 flex-1 min-w-0">
                                            <svg class="w-4 h-4 text-gray-500 transition-transform duration-200 flex-shrink-0"
                                                :class="{ 'rotate-90': expandedChapters.includes({{ $chapter->id }}) }"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                            <div class="flex items-center gap-2 flex-wrap min-w-0">
                                                <span
                                                    class="text-xs font-medium text-gray-500 px-1.5 py-0.5 bg-blue-100 rounded">Ch
                                                    {{ $chapter->order }}</span>
                                                <h3 class="text-sm font-semibold text-gray-900 truncate">
                                                    {{ $chapter->name }}</h3>
                                                <span class="text-xs text-gray-400">{{ $chapter->topics->count() }}
                                                    topics</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1 ml-2 flex-shrink-0" @click.stop>
                                            <button wire:click="onEditChapter({{ $chapter->id }})"
                                                class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button wire:click="deleteChapter({{ $chapter->id }})"
                                                class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Topics --}}
                                    <div x-show="expandedChapters.includes({{ $chapter->id }})" x-collapse
                                        class="bg-white">
                                        @forelse ($chapter->topics as $topic)
                                            <div
                                                class="px-10 sm:px-16 py-2.5 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                                <div class="flex items-center gap-2 flex-1 min-w-0">
                                                    <span
                                                        class="w-1.5 h-1.5 bg-emerald-500 rounded-full flex-shrink-0"></span>
                                                    <span
                                                        class="text-sm text-gray-800 truncate">{{ $topic->topic_name }}</span>
                                                </div>
                                                <div class="flex items-center gap-1 ml-2 flex-shrink-0">
                                                    <button wire:click="onEditTopic({{ $topic->id }})"
                                                        class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                        title="Edit">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button wire:click="deleteTopic({{ $topic->id }})"
                                                        class="p-1 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Delete">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="px-16 py-4 text-center text-xs text-gray-400">No topics yet
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @empty
                                <div class="px-10 py-6 text-center text-sm text-gray-400">No chapters yet</div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-16 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm">No subjects found</p>
                        @if ($search || $filterStandard || $filterSection || $filterSubject)
                            <button wire:click="clearFilters"
                                class="mt-3 text-sm text-blue-600 hover:text-blue-800 font-medium">Clear
                                filters</button>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($subjects->hasPages())
                <div
                    class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <p class="text-sm text-gray-500">
                        Showing <span class="font-medium text-gray-700">{{ $subjects->firstItem() }}</span>
                        to <span class="font-medium text-gray-700">{{ $subjects->lastItem() }}</span>
                        of <span class="font-medium text-gray-700">{{ $subjects->total() }}</span>
                    </p>
                    <div class="flex items-center gap-1">
                        @if ($subjects->onFirstPage())
                            <span
                                class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-lg cursor-not-allowed">&laquo;
                                Prev</span>
                        @else
                            <button wire:click="previousPage"
                                class="px-3 py-1.5 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">&laquo;
                                Prev</button>
                        @endif
                        @foreach ($subjects->getUrlRange(max(1, $subjects->currentPage() - 2), min($subjects->lastPage(), $subjects->currentPage() + 2)) as $page => $url)
                            <button wire:click="gotoPage({{ $page }})"
                                class="px-3 py-1.5 text-sm rounded-lg {{ $page == $subjects->currentPage() ? 'bg-blue-600 text-white border border-blue-600' : 'text-gray-600 border border-gray-300 hover:bg-gray-50' }}">{{ $page }}</button>
                        @endforeach
                        @if ($subjects->hasMorePages())
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
    </div>

    {{-- ══════════════════════════════════════════════════
         ADD CHAPTER MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openChapterModal }}" title="Add Chapters" submitAction="onSaveChapters"
        submitButton="Save Chapters" closeAction="closeChapterModal">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <x-native-select label="Class *" wire:model.live="chapterStandardId" required>
                <option value="">Select Class</option>
                @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Section" wire:model.live="chapterSectionId" :disabled="!$chapterStandardId">
                <option value="">Select Section</option>
                @foreach ($chapterSections as $sec)
                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Subject *" wire:model.live="chapterSubjectId" required :disabled="!$chapterStandardId">
                <option value="">Select Subject</option>
                @foreach ($chapterSubjects as $subj)
                    <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                @endforeach
            </x-native-select>
        </div>

        @if ($chapterSubjectId)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Chapters</h3>
                    <button wire:click="addChapterRow" type="button"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Chapter
                    </button>
                </div>

                @if (empty($chapterRows))
                    <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-sm text-gray-400 mb-3">No chapters added yet</p>
                        <button wire:click="addChapterRow" type="button"
                            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-semibold text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200">
                            Add First Chapter
                        </button>
                    </div>
                @endif

                @foreach ($chapterRows as $i => $row)
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-400 uppercase">Chapter {{ $i + 1 }}</span>
                            <button wire:click="removeChapterRow({{ $i }})" type="button"
                                class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Chapter Name *</label>
                                <input type="text" wire:model="chapterRows.{{ $i }}.name"
                                    placeholder="e.g., Introduction to Algebra"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Description</label>
                                <input type="text" wire:model="chapterRows.{{ $i }}.description"
                                    placeholder="Optional"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Order *</label>
                                <input type="number" wire:model="chapterRows.{{ $i }}.order"
                                    min="1"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-sm text-gray-400">Please select class and subject to add chapters.</div>
        @endif
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         ADD TOPIC MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openTopicModal }}" title="Add Topics" submitAction="onSaveTopics"
        submitButton="Save Topics" closeAction="closeTopicModal">

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
            <x-native-select label="Class *" wire:model.live="topicStandardId" required>
                <option value="">Select Class</option>
                @foreach ($standards as $std)
                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Section" wire:model.live="topicSectionId" :disabled="!$topicStandardId">
                <option value="">Select Section</option>
                @foreach ($topicSections as $sec)
                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Subject *" wire:model.live="topicSubjectId" required :disabled="!$topicStandardId">
                <option value="">Select Subject</option>
                @foreach ($topicSubjects as $subj)
                    <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                @endforeach
            </x-native-select>
            <x-native-select label="Chapter *" wire:model.live="topicChapterId" required :disabled="!$topicSubjectId">
                <option value="">Select Chapter</option>
                @foreach ($topicChapters as $ch)
                    <option value="{{ $ch->id }}">Ch {{ $ch->order }} — {{ $ch->name }}</option>
                @endforeach
            </x-native-select>
        </div>

        @if ($topicChapterId)
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">Topics</h3>
                    <button wire:click="addTopicRow" type="button"
                        class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Topic
                    </button>
                </div>

                @if (empty($topicRows))
                    <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                        <p class="text-sm text-gray-400 mb-3">No topics added yet</p>
                        <button wire:click="addTopicRow" type="button"
                            class="inline-flex items-center gap-1 px-4 py-2 text-sm font-semibold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200">
                            Add First Topic
                        </button>
                    </div>
                @endif

                @foreach ($topicRows as $i => $row)
                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-400 uppercase">Topic {{ $i + 1 }}</span>
                            <button wire:click="removeTopicRow({{ $i }})" type="button"
                                class="p-1 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Topic Name *</label>
                                <input type="text" wire:model="topicRows.{{ $i }}.name"
                                    placeholder="e.g., Variables and Constants"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Order</label>
                                <input type="number" wire:model="topicRows.{{ $i }}.order"
                                    min="1"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-sm text-gray-400">Please select class, subject and chapter to add topics.
            </div>
        @endif
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         EDIT CHAPTER MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $editChapterModal }}" title="Edit Chapter" submitAction="onUpdateChapter"
        submitButton="Update" closeAction="closeEditChapterModal">
        <div class="space-y-4">
            <x-input wire:model="editChapterName" label="Chapter Name *" required />
            <x-textarea wire:model="editChapterDesc" label="Description" rows="2" />
            <x-input wire:model="editChapterOrder" label="Order *" type="number" min="1" required />
        </div>
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         EDIT TOPIC MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $editTopicModal }}" title="Edit Topic" submitAction="onUpdateTopic"
        submitButton="Update" closeAction="closeEditTopicModal">
        <div class="space-y-4">
            <x-input wire:model="editTopicName" label="Topic Name *" required />
        </div>
    </x-modal-form>

</div>
