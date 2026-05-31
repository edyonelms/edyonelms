<div>

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-6 py-5">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Academic Structure</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage classes, sections & subjects</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button wire:click="onStandard"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 hover:bg-purple-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Class
                </button>
                <button wire:click="onSection"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Section
                </button>
                <button wire:click="onSubject"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Subject
                </button>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         TABS
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-6">
        <nav class="flex gap-1">
            @php
                $tabs = [
                    [
                        'key'   => 'standard',
                        'label' => 'Classes',
                        'color' => 'purple',
                        'count' => $filteredStandards->total(),
                    ],
                    [
                        'key'   => 'section',
                        'label' => 'Sections',
                        'color' => 'blue',
                        'count' => $filteredSections->total(),
                    ],
                    [
                        'key'   => 'subject',
                        'label' => 'Subjects',
                        'color' => 'emerald',
                        'count' => $filteredSubjects->total(),
                    ],
                ];
            @endphp
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
                        : 'bg-gray-100 text-gray-600' }}">
                            {{ $tab['count'] }}
                        </span>
                    @endif

                    {{-- breadcrumb indicator when drilled-in --}}
                    @if ($tab['key'] === 'section' && $filterStandard)
                        <span class="ml-1 w-2 h-2 bg-blue-500 rounded-full inline-block"></span>
                    @endif
                    @if ($tab['key'] === 'subject' && ($filterSubjectStandard || $filterSection))
                        <span class="ml-1 w-2 h-2 bg-emerald-500 rounded-full inline-block"></span>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    {{-- ══════════════════════════════════════════════════
         FILTERS
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-100 px-6 py-3">
        <div class="flex flex-wrap items-center gap-3">
            {{-- Search --}}
            <div class="relative flex-1 min-w-48">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input wire:model.live.debounce.300ms="search" type="text"
                    placeholder="Search {{ $activeTab === 'standard' ? 'classes' : ($activeTab === 'section' ? 'sections' : 'subjects') }}..."
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-purple-400 focus:border-purple-400 bg-white" />
            </div>

            @if ($activeTab === 'section')
                <select wire:model.live="filterStandard"
                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-400 bg-white">
                    <option value="">Select Class</option>
                    @foreach ($allStandards as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            @endif

            @if ($activeTab === 'subject')
                <select wire:model.live="filterSubjectStandard"
                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 bg-white">
                    <option value="">Select Class</option>
                    @foreach ($allStandards as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterSection"
                    class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-emerald-400 bg-white"
                    @disabled(!$filterSubjectStandard)>
                    <option value="">Select Section</option>
                    @foreach ($availableSections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->name }}
                            @if ($sec->code) ({{ $sec->code }}) @endif
                        </option>
                    @endforeach
                </select>
            @endif

            <select wire:model.live="filterStatus"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-300 bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <select wire:model.live="perPage"
                class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-gray-300 bg-white w-20">
                <option value="10">10</option>
                <option value="25">25</option>
                <option value="50">50</option>
                <option value="100">100</option>
            </select>

            @if ($search || $filterStandard || $filterStatus || $filterSubjectStandard || $filterSection)
                <button wire:click="resetFilters"
                    class="inline-flex items-center gap-1 text-xs text-red-600 border border-red-200
                       hover:bg-red-50 px-2.5 py-2 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear
                </button>
            @endif
        </div>

        {{-- Drill-down breadcrumb --}}
        @if ($activeTab === 'section' && $filterStandard)
            @php $breadStd = $allStandards->firstWhere('id', $filterStandard); @endphp
            @if ($breadStd)
                <div class="mt-2 flex items-center gap-1.5 text-xs text-blue-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Showing sections for <strong>{{ $breadStd->name }}</strong>
                    <button wire:click="$set('filterStandard','')"
                        class="ml-1 text-gray-400 hover:text-red-500">✕</button>
                </div>
            @endif
        @endif
        @if ($activeTab === 'subject' && $filterSection)
            @php $breadSec = $availableSections->firstWhere('id', $filterSection); @endphp
            @if ($breadSec)
                <div class="mt-2 flex items-center gap-1.5 text-xs text-emerald-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    Showing subjects for section <strong>{{ $breadSec->name }}</strong>
                    <button wire:click="$set('filterSection','')"
                        class="ml-1 text-gray-400 hover:text-red-500">✕</button>
                </div>
            @endif
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════
         CONTENT
    ══════════════════════════════════════════════════ --}}
    <div class="p-6">

        {{-- Loading --}}
        <div wire:loading.flex class="justify-center py-16">
            <div class="text-center">
                <div
                    class="w-10 h-10 border-4 border-purple-200 border-t-purple-600 rounded-full animate-spin mx-auto">
                </div>
                <p class="text-sm text-gray-500 mt-3">Loading...</p>
            </div>
        </div>

        <div wire:loading.remove>

            {{-- ════════════════ CLASSES (List) ════════════════ --}}
            @if ($activeTab === 'standard')
                @if ($filteredStandards->count())
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                        <div class="grid grid-cols-12 px-4 py-2.5 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <div class="col-span-1">#</div>
                            <div class="col-span-3">Class</div>
                            <div class="col-span-2">Code</div>
                            <div class="col-span-2">Board</div>
                            <div class="col-span-1 text-center">Sections</div>
                            <div class="col-span-1 text-center">Status</div>
                            <div class="col-span-2 text-right">Actions</div>
                        </div>
                        @foreach ($filteredStandards as $idx => $std)
                            <div class="grid grid-cols-12 items-center px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-purple-50/40 transition-colors cursor-pointer"
                                wire:click="drillIntoClass({{ $std->id }})">
                                <div class="col-span-1 text-sm text-gray-500">{{ $filteredStandards->firstItem() + $idx }}</div>
                                <div class="col-span-3 flex items-center gap-2.5">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $std->name }}</p>
                                        <p class="text-xs text-purple-500 mt-0.5">View Sections →</p>
                                    </div>
                                </div>
                                <div class="col-span-2 text-sm text-gray-700">{{ $std->code }}</div>
                                <div class="col-span-2 text-sm text-gray-600">{{ $std->board }}</div>
                                <div class="col-span-1 text-center text-sm font-semibold text-gray-800">
                                    {{ $std->sections_count ?? ($std->sections->count() ?? 0) }}
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium
                                        {{ $std->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $std->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="col-span-2 flex items-center justify-end gap-1" wire:click.stop>
                                    <button wire:click.stop="onViewStandardAdmin({{ $std->id }})"
                                        class="p-1.5 rounded-lg hover:bg-green-50 text-green-600" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click.stop="editStandard({{ $std->id }})"
                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click.stop="onDeleteStandard({{ $std->id }})"
                                        class="p-1.5 rounded-lg hover:bg-red-50 text-red-500" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">No classes found</h3>
                        <p class="text-sm text-gray-400 mb-5">
                            @if ($search || $filterStatus)
                                No classes match your filters. Try clearing them.
                            @else
                                You haven't added any classes yet.
                            @endif
                        </p>
                        <div class="flex justify-center gap-2">
                            @if ($search || $filterStatus)
                                <button wire:click="resetFilters"
                                    class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-600">
                                    Clear Filters
                                </button>
                            @endif
                            <button wire:click="onStandard"
                                class="px-4 py-2 text-sm bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium">
                                Add First Class
                            </button>
                        </div>
                    </div>
                @endif
            @endif

            {{-- ════════════════ SECTIONS (List) ════════════════ --}}
            @if ($activeTab === 'section')
                @if (!$filterStandard)
                    <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-blue-200">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Select a class to view sections</h3>
                        <p class="text-sm text-gray-400">Use the <strong>Class</strong> filter above, or click a class from the Classes tab.</p>
                    </div>
                @elseif ($filteredSections->count())
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                        <div class="grid grid-cols-12 px-4 py-2.5 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <div class="col-span-1">#</div>
                            <div class="col-span-3">Section</div>
                            <div class="col-span-2">Code</div>
                            <div class="col-span-3">Class</div>
                            <div class="col-span-1 text-center">Status</div>
                            <div class="col-span-2 text-right">Actions</div>
                        </div>
                        @foreach ($filteredSections as $idx => $section)
                            <div class="grid grid-cols-12 items-center px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-blue-50/40 transition-colors cursor-pointer"
                                wire:click="drillIntoSection({{ $section->id }})">
                                <div class="col-span-1 text-sm text-gray-500">{{ $filteredSections->firstItem() + $idx }}</div>
                                <div class="col-span-3 flex items-center gap-2.5">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $section->name }}</p>
                                        <p class="text-xs text-blue-500 mt-0.5">View Subjects →</p>
                                    </div>
                                </div>
                                <div class="col-span-2 text-sm text-gray-700">{{ $section->code }}</div>
                                <div class="col-span-3 text-sm text-gray-600">{{ $section->standard->name ?? '—' }}</div>
                                <div class="col-span-1 text-center">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium
                                        {{ $section->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $section->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="col-span-2 flex items-center justify-end gap-1" wire:click.stop>
                                    <button wire:click.stop="onViewSectionAdmin({{ $section->id }})"
                                        class="p-1.5 rounded-lg hover:bg-green-50 text-green-600" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click.stop="editSection({{ $section->id }})"
                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click.stop="onDeleteSection({{ $section->id }})"
                                        class="p-1.5 rounded-lg hover:bg-red-50 text-red-500" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">No sections in this class</h3>
                        <p class="text-sm text-gray-400 mb-5">Add a section to get started.</p>
                        <button wire:click="onSection"
                            class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                            Add Section
                        </button>
                    </div>
                @endif
            @endif

            {{-- ════════════════ SUBJECTS (List) ════════════════ --}}
            @if ($activeTab === 'subject')
                @if (!$filterSection)
                    <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-emerald-200">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">Select a section to view subjects</h3>
                        <p class="text-sm text-gray-400">Pick a <strong>class</strong> then a <strong>section</strong> from the filters above, or drill in from the Sections tab.</p>
                    </div>
                @elseif ($filteredSubjects->count())
                    <div class="bg-white border border-gray-200 rounded-2xl overflow-hidden">
                        <div class="grid grid-cols-12 px-4 py-2.5 bg-gray-50 border-b border-gray-200 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            <div class="col-span-1">#</div>
                            <div class="col-span-3">Subject</div>
                            <div class="col-span-2">Code</div>
                            <div class="col-span-3">Class · Sections</div>
                            <div class="col-span-1 text-center">Status</div>
                            <div class="col-span-2 text-right">Actions</div>
                        </div>
                        @foreach ($filteredSubjects as $idx => $subject)
                            <div class="grid grid-cols-12 items-center px-4 py-3 border-b border-gray-100 last:border-0 hover:bg-emerald-50/40 transition-colors cursor-pointer"
                                wire:click="drillIntoSubject({{ $subject->id }})">
                                <div class="col-span-1 text-sm text-gray-500">{{ $filteredSubjects->firstItem() + $idx }}</div>
                                <div class="col-span-3 flex items-center gap-2.5">
                                    @if ($subject->image)
                                        <img src="{{ $subject->image }}" alt="{{ $subject->name }}"
                                            class="w-8 h-8 rounded-lg object-cover border border-gray-100 flex-shrink-0">
                                    @else
                                        <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900 text-sm truncate">{{ $subject->name }}</p>
                                        <p class="text-xs text-emerald-500 mt-0.5">Open Syllabus →</p>
                                    </div>
                                </div>
                                <div class="col-span-2 text-sm text-gray-700">{{ $subject->code }}</div>
                                <div class="col-span-3 text-sm text-gray-600 truncate">
                                    {{ $subject->standards->pluck('name')->implode(', ') ?: '—' }}
                                    @if ($subject->sections->count())
                                        <span class="text-gray-400">· {{ $subject->sections->pluck('name')->implode(', ') }}</span>
                                    @endif
                                </div>
                                <div class="col-span-1 text-center">
                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full font-medium
                                        {{ $subject->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                        {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="col-span-2 flex items-center justify-end gap-1" wire:click.stop>
                                    <button wire:click.stop="onViewSubjectAdmin({{ $subject->id }})"
                                        class="p-1.5 rounded-lg hover:bg-green-50 text-green-600" title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click.stop="editSubject({{ $subject->id }})"
                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click.stop="onDeleteSubject({{ $subject->id }})"
                                        class="p-1.5 rounded-lg hover:bg-red-50 text-red-500" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-white rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">No subjects in this section</h3>
                        <p class="text-sm text-gray-400 mb-5">Add a subject to get started.</p>
                        <button wire:click="onSubject"
                            class="px-4 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium">
                            Add Subject
                        </button>
                    </div>
                @endif
            @endif

            {{-- Pagination --}}
            @php
                $paginatedResult =
                    $activeTab === 'standard'
                        ? $filteredStandards
                        : ($activeTab === 'section'
                            ? $filteredSections
                            : $filteredSubjects);
            @endphp
            @if ($paginatedResult->hasPages())
                <div class="mt-6 pt-4 border-t border-gray-100">
                    {{ $paginatedResult->links() }}
                </div>
            @endif

        </div>{{-- end wire:loading.remove --}}
    </div>

    {{-- ══════════════════════════════════════════════════
         CLASS MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openStandard }}" title="{{ $editId ? 'Edit Class' : 'Add Class' }}"
        submitAction="saveStandard" submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="closeModal">
        <div class="space-y-4">
            <x-input wire:model.defer="standardName" label="Class Name *" placeholder="e.g. Class 10" />
            @error('standardName')
                <p class="text-xs text-red-500 -mt-2">{{ $message }}</p>
            @enderror

            <x-input wire:model.defer="standardCode" label="Code *" placeholder="e.g. STD-10" />
            @error('standardCode')
                <p class="text-xs text-red-500 -mt-2">{{ $message }}</p>
            @enderror

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Board</label>
                <input type="text" value="{{ $standardBoard ?: 'Not set for organization' }}" readonly
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-gray-50 text-gray-600 cursor-not-allowed" />
                <p class="text-xs text-gray-400 mt-1">Inherited from your organization's board.</p>
            </div>

            <x-input wire:model.defer="standardOrder" label="Display Order" type="number" placeholder="0" />
            <x-toggle label="Active" wire:model.defer="standardActive" />
        </div>
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         SECTION MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openSection }}" title="{{ $editId ? 'Edit Section' : 'Add Section' }}"
        submitAction="saveSection" submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="closeModal">
        <div class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <x-input wire:model.defer="sectionName" label="Section Name *" placeholder="e.g. A" />
                    @error('sectionName')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <x-input wire:model.defer="sectionCode" label="Code *" placeholder="e.g. SEC-A" />
                    @error('sectionCode')
                        <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <x-native-select label="Class *" wire:model.defer="selectedStandard">
                <option value="">Select Class</option>
                @foreach ($standards as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </x-native-select>
            <x-textarea wire:model.defer="sectionDescription" label="Description" placeholder="Optional description"
                rows="3" />
            <x-toggle label="Active" wire:model.defer="sectionActive" />
        </div>
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         SUBJECT MODAL
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $openSubject }}" title="{{ $editId ? 'Edit Subject' : 'Add Subject' }}"
        submitAction="saveSubject" submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="closeModal">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <div>
                <x-input wire:model.defer="subjectName" label="Subject Name *" placeholder="e.g. Mathematics" />
                @error('subjectName')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-input wire:model.defer="subjectCode" label="Code *" placeholder="e.g. MATH" />
                @error('subjectCode')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-native-select label="Class *" wire:model.live="selectedStandardForSubject">
                    <option value="">Select Class</option>
                    @foreach ($standards as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </x-native-select>
            </div>

            <div>
                <x-select label="Sections *" wire:model="selectedSectionsForSubject" :options="$sections->map(fn($s) => ['value' => $s->id, 'label' => $s->name])->toArray()"
                    option-value="value" option-label="label" multiselect
                    placeholder="{{ $selectedStandardForSubject ? 'Select sections' : 'Select class first' }}"
                    :disabled="!$selectedStandardForSubject" />
                @error('selectedSectionsForSubject')
                    <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Subject Image --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Subject Image</label>

                @if ($subjectImagePreview)
                    <div class="mb-3 flex items-center gap-3">
                        <img src="{{ $subjectImagePreview }}"
                            class="w-20 h-20 rounded-xl object-cover border-2 border-gray-200 shadow-sm">
                        <button type="button"
                            wire:click="$set('subjectImagePreview', null); $set('subjectImageUrl', null)"
                            class="text-xs text-red-600 hover:text-red-800 border border-red-200 px-2.5 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                            Remove Image
                        </button>
                    </div>
                @endif

                <div class="border-2 border-dashed border-gray-300 hover:border-purple-400 rounded-xl p-5
                            text-center transition-colors cursor-pointer"
                    onclick="document.getElementById('subjectImageInput').click()">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to upload image</p>
                    <p class="text-xs text-gray-400 mt-0.5">PNG, JPG up to 2MB</p>
                    <input id="subjectImageInput" type="file" wire:model="subjectImage" accept="image/*"
                        class="hidden">
                </div>
                @error('subjectImage')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror

                <div wire:loading wire:target="subjectImage"
                    class="text-xs text-purple-600 mt-1.5 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Uploading...
                </div>
            </div>

            <div class="sm:col-span-2">
                <x-textarea wire:model.defer="subjectDescription" label="Description"
                    placeholder="Optional description" rows="3" />
            </div>

            <div><x-toggle label="Mandatory Subject" wire:model.defer="isMandatory" /></div>
            <div><x-toggle label="Active" wire:model.defer="subjectActive" /></div>
        </div>
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         VIEW MODAL
    ══════════════════════════════════════════════════ --}}
    <x-view-modal :show="$showViewModal" :title="$viewModalTitle" closeAction="closeViewModal">
        <div class="space-y-4 text-sm">
            @foreach ($viewData as $key => $val)
                @if (!in_array($key, ['image', 'detail_image']) && $val !== null && $val !== '')
                    <div class="flex items-start gap-2">
                        <span
                            class="text-gray-400 capitalize w-28 flex-shrink-0">{{ str_replace('_', ' ', $key) }}</span>
                        <span class="font-medium text-gray-800">{{ $val }}</span>
                    </div>
                @endif
            @endforeach
            @if (!empty($viewData['image']))
                <div>
                    <p class="text-gray-400 mb-1.5">Image</p>
                    <img src="{{ $viewData['image'] }}" class="w-24 h-24 rounded-xl object-cover border">
                </div>
            @endif
        </div>
    </x-view-modal>

</div>
