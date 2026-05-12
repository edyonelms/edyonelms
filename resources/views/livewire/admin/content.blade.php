<div class="min-h-screen bg-gray-50" x-data="{ expandedChapters: @entangle('expandedChapters').live }">

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Content Management</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage chapter and topic content</p>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 divide-x divide-gray-200">
                    <span class="pr-4">Chapters: <strong class="text-blue-600">{{ $totalChapters }}</strong></span>
                    <span class="px-4">Topics: <strong class="text-emerald-600">{{ $totalTopics }}</strong></span>
                    <span class="pl-4">With Content: <strong
                            class="text-purple-600">{{ $withContent }}</strong></span>
                </div>
            </div>
        </div>
        <div class="flex lg:hidden items-center gap-3 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Chapters: <strong class="text-blue-600">{{ $totalChapters }}</strong></span>
            <span>Topics: <strong class="text-emerald-600">{{ $totalTopics }}</strong></span>
            <span>With Content: <strong class="text-purple-600">{{ $withContent }}</strong></span>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-4 sm:space-y-5">

        {{-- ══════════════════════════════════════════════════
             FILTERS (must select class + subject to see list)
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 sm:p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
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
                <div class="flex items-center">
                    @if ($filterStandard || $filterSection || $filterSubject)
                        <button wire:click="clearFilters" title="Clear filters"
                            class="px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            @if (!$showList)
                <p class="text-xs text-amber-600 mt-2">Please select a class and subject to view content.</p>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════
             CONTENT LIST (Chapter → Topic hierarchy)
        ══════════════════════════════════════════════════ --}}
        @if ($showList)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @forelse ($chapters as $chapter)
                        @php $chHasContent = !empty($chapter->description) || !empty($chapter->file_path) || !empty($chapter->image_path) || !empty($chapter->pdf_path); @endphp
                        <div>
                            {{-- Chapter Row --}}
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
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $chapter->name }}</h3>
                                    <span class="text-xs text-gray-400 flex-shrink-0">{{ $chapter->topics->count() }}
                                        topics</span>
                                </div>

                                {{-- Chapter actions --}}
                                <div class="flex items-center gap-1 ml-2 flex-shrink-0" @click.stop>
                                    @if ($chHasContent)
                                        <button wire:click="onViewContent('chapter', {{ $chapter->id }})"
                                            class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="View Content">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        <button wire:click="onEditContent('chapter', {{ $chapter->id }})"
                                            class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Edit Content">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button wire:click="deleteContent('chapter', {{ $chapter->id }})"
                                            class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Remove Content">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    @else
                                        <button wire:click="onAddContent('chapter', {{ $chapter->id }})"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Add Content
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Topics --}}
                            <div x-show="expandedChapters.includes({{ $chapter->id }})" x-collapse
                                class="bg-gray-50">
                                @forelse ($chapter->topics as $topic)
                                    @php $tpHasContent = !empty($topic->topic_content) || !empty($topic->image_path) || !empty($topic->pdf_path); @endphp
                                    <div
                                        class="px-8 sm:px-12 py-2.5 flex items-center justify-between hover:bg-white border-b border-gray-100 last:border-b-0 transition">
                                        <div class="flex items-center gap-2 flex-1 min-w-0">
                                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full flex-shrink-0"></span>
                                            <span
                                                class="text-sm text-gray-800 truncate">{{ $topic->topic_name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 ml-2 flex-shrink-0">
                                            @if ($tpHasContent)
                                                <button wire:click="onViewContent('topic', {{ $topic->id }})"
                                                    class="p-1 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                                    title="View">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="onEditContent('topic', {{ $topic->id }})"
                                                    class="p-1 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                    title="Edit">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="deleteContent('topic', {{ $topic->id }})"
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Remove">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @else
                                                <button wire:click="onAddContent('topic', {{ $topic->id }})"
                                                    class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add Content
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-12 py-4 text-center text-xs text-gray-400">No topics in this chapter
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div
                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <p class="text-gray-500 text-sm">No chapters found for this selection</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            {{-- Prompt to select filters --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-6 py-16 text-center">
                <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <p class="text-gray-700 text-sm font-medium">Select a class and subject to manage content</p>
                <p class="text-gray-400 text-xs mt-1">Use the filters above to view chapters and topics</p>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════
         ADD / EDIT CONTENT MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openContentModal }}"
        title="{{ $contentEditMode ? 'Edit' : 'Add' }} Content — {{ $contentTargetName }}"
        submitAction="onSaveContent" submitButton="{{ $contentEditMode ? 'Update Content' : 'Save Content' }}"
        closeAction="closeContentModal">

        {{-- Content Type Selection --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-gray-700 mb-2">Content Type</label>
            <div class="flex flex-wrap gap-2">
                @foreach (['all' => 'All', 'text' => 'Text', 'url' => 'URL', 'image' => 'Image', 'pdf' => 'PDF'] as $val => $label)
                    <button type="button" wire:click="$set('contentType', '{{ $val }}')"
                        class="px-4 py-2 text-sm font-medium rounded-lg border transition-colors
                            {{ $contentType === $val
                                ? 'bg-blue-600 text-white border-blue-600'
                                : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Text --}}
        @if ($contentType === 'text' || $contentType === 'all')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Content Text</label>
                <textarea wire:model="contentText" rows="{{ $contentType === 'all' ? 4 : 6 }}" placeholder="Enter content text..."
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
        @endif

        {{-- URL --}}
        @if ($contentType === 'url' || $contentType === 'all')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                <input type="url" wire:model="contentUrl" placeholder="https://example.com/resource"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-400 mt-1">Enter a video link, external resource, or any URL</p>
            </div>
        @endif

        {{-- Image --}}
        @if ($contentType === 'image' || $contentType === 'all')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Image</label>
                @if ($existingImage && !$contentImage)
                    <div class="flex items-center gap-3 mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                        <img src="{{ $existingImage }}"
                            class="h-16 w-16 rounded object-cover border border-gray-200">
                        <span class="text-xs text-gray-600">Current image</span>
                    </div>
                @endif
                <input type="file" wire:model="contentImage" accept="image/*"
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                        file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-400 mt-1">Max: 2MB (JPG, PNG, GIF)</p>
                @error('contentImage')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        @endif

        {{-- PDF --}}
        @if ($contentType === 'pdf' || $contentType === 'all')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload PDF</label>
                @if ($existingPdf && !$contentPdf)
                    <div class="flex items-center gap-3 mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span class="text-xs text-gray-600">Current PDF</span>
                    </div>
                @endif
                <input type="file" wire:model="contentPdf" accept="application/pdf"
                    class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                        file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                <p class="text-xs text-gray-400 mt-1">Max: 5MB</p>
                @error('contentPdf')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>
        @endif
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         VIEW CONTENT MODAL
    ══════════════════════════════════════════════════ --}}
    <x-view-modal :show="$showViewModal" :title="$viewContentTitle" closeAction="closeViewModal">
        @if (!empty($viewContentData))
            <div class="space-y-4 text-left">
                @if (!empty($viewContentData['text']))
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Text Content</p>
                        <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $viewContentData['text'] }}</p>
                    </div>
                @endif

                @if (!empty($viewContentData['url']))
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">URL</p>
                        <a href="{{ $viewContentData['url'] }}" target="_blank"
                            class="text-sm text-blue-600 hover:text-blue-800 underline break-all">
                            {{ $viewContentData['url'] }}
                        </a>
                    </div>
                @endif

                @if (!empty($viewContentData['image']))
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Image</p>
                        <img src="{{ $viewContentData['image'] }}" alt="Content Image"
                            class="max-w-full rounded-lg border border-gray-200">
                    </div>
                @endif

                @if (!empty($viewContentData['pdf']))
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">PDF</p>
                        <iframe src="{{ $viewContentData['pdf'] }}"
                            class="w-full h-64 rounded-lg border border-gray-200"></iframe>
                        <a href="{{ $viewContentData['pdf'] }}" target="_blank"
                            class="inline-flex items-center gap-1 mt-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download PDF
                        </a>
                    </div>
                @endif

                @if (empty($viewContentData['text']) &&
                        empty($viewContentData['url']) &&
                        empty($viewContentData['image']) &&
                        empty($viewContentData['pdf']))
                    <div class="text-center py-8 text-sm text-gray-400">No content available</div>
                @endif
            </div>
        @endif
    </x-view-modal>

</div>
