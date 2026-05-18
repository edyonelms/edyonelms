<div class="p-4">

    {{-- ══════════ HEADER ══════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky rounded-lg top-0 z-30">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Contact Super Admin</h1>
                <p class="text-sm text-gray-500 mt-0.5">Send and manage your queries to Super Admin</p>
            </div>
            <button wire:click="onAddContact"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg shadow-sm transition-colors w-fit">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden sm:inline">Contact Super Admin</span>
                <span class="sm:hidden">New</span>
            </button>
        </div>
    </div>

    {{-- ══════════ ANALYTICS CARDS ══════════ --}}
    @php
        $totalCount   = $contacts->count();
        $pendingCount = $contacts->filter(fn($c) => empty($c->super_admin_reply))->count();
        $repliedCount = $contacts->filter(fn($c) => !empty($c->super_admin_reply))->count();
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4 mb-4">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Total Messages</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ $totalCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Pending Reply</p>
                <p class="text-2xl font-bold text-amber-500 mt-0.5">{{ $pendingCount }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide">Replied</p>
                <p class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $repliedCount }}</p>
            </div>
        </div>
    </div>

    {{-- ══════════ FILTERS ══════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-700">Filter by:</span>

            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Last:</span>
                <div class="flex gap-1">
                    @foreach ([7, 15, 30] as $days)
                        <button wire:click="applyFilter('days', {{ $days }})"
                            class="px-3 py-1 text-sm rounded-md transition-colors
                                   {{ $filterDays == $days
                                       ? 'bg-blue-100 text-blue-700 border border-blue-300'
                                       : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $days }} days
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">Status:</span>
                <select wire:model.live="statusFilter"
                    class="text-sm border border-gray-300 rounded-md px-3 py-1.5
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All</option>
                    <option value="pending">Pending Reply</option>
                    <option value="replied">Replied</option>
                </select>
            </div>

            @if ($filterDays || $statusFilter)
                <button wire:click="clearFilters"
                    class="inline-flex items-center gap-1 px-3 py-1 text-sm text-red-600
                           bg-red-50 rounded-md hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Clear Filters
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════ CONTACTS LIST ══════════ --}}
    <div class="space-y-4">
        @forelse($contacts as $contact)
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md
                        transition-all duration-200 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1 min-w-0">

                            {{-- Status icon --}}
                            <div class="p-2.5 rounded-xl flex-shrink-0
                                {{ $contact->super_admin_reply ? 'bg-green-50' : 'bg-orange-50' }}">
                                <svg class="w-5 h-5 {{ $contact->super_admin_reply ? 'text-green-600' : 'text-orange-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                {{-- Title + Badges --}}
                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                    <h3 class="font-bold text-gray-900">{{ $contact->topic }}</h3>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ $contact->super_admin_reply ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $contact->super_admin_reply ? 'Replied' : 'Pending' }}
                                    </span>
                                    @if ($contact->image)
                                        <span class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                            </svg>
                                            Attachment
                                        </span>
                                    @endif
                                </div>

                                {{-- Meta --}}
                                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mb-2">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $contact->user->name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $contact->created_at->format('M j, Y g:i A') }}
                                    </span>
                                    <span class="text-gray-400">{{ $contact->created_at->diffForHumans() }}</span>
                                </div>

                                {{-- Query preview --}}
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    {{ Str::limit($contact->admin_query, 120) }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex items-center gap-1.5 flex-shrink-0">
                            <button wire:click="onViewContact({{ $contact->id }})" title="View"
                                class="p-2 rounded-lg border border-blue-200 text-blue-600 hover:bg-blue-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            @if (!$contact->super_admin_reply)
                                <button wire:click="onEditContact({{ $contact->id }})" title="Edit"
                                    class="p-2 rounded-lg border border-amber-200 text-amber-600 hover:bg-amber-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            @endif

                            <button wire:click="onDeleteContact({{ $contact->id }})" title="Delete"
                                class="p-2 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">No messages found</h3>
                <p class="text-sm text-gray-400">Messages sent to Super Admin will appear here.</p>
            </div>
        @endforelse
    </div>

    {{-- ══════════ ADD / EDIT SLIDE-IN PANEL ══════════ --}}
    @if ($open)
        <div class="fixed inset-0 z-50 overflow-hidden">
            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/30 backdrop-blur-[1px]" wire:click="closeModal"></div>

            {{-- Panel --}}
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-lg bg-white shadow-2xl flex flex-col">

                {{-- Panel Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-800">
                                {{ $editId ? 'Edit Message' : 'Contact Super Admin' }}
                            </h2>
                            <p class="text-xs text-gray-500">{{ $editId ? 'Update your query' : 'Send a new query' }}</p>
                        </div>
                    </div>
                    <button wire:click="closeModal"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Panel Body --}}
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Topic <span class="text-red-400">*</span></label>
                        <input wire:model.defer="topic" type="text" placeholder="What is this about?"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-400 transition-colors" />
                        @error('topic')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Your Message <span class="text-red-400">*</span></label>
                        <textarea wire:model.defer="admin_query" rows="5" placeholder="Describe your query in detail..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-400 resize-none transition-colors"></textarea>
                        @error('admin_query')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    @if ($editId && $existingImage && !$image)
                        @php $extExisting = strtolower(pathinfo($existingImage, PATHINFO_EXTENSION)); @endphp
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-3">
                            <p class="text-xs font-medium text-gray-500 mb-2">Current Attachment:</p>
                            @if (in_array($extExisting, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <div class="flex items-center gap-3">
                                    <img src="{{ $existingImage }}"
                                        class="w-20 h-20 rounded-lg object-cover border border-gray-200 shadow-sm">
                                    <a href="{{ $existingImage }}" target="_blank"
                                        class="text-xs text-blue-600 hover:underline">View full image</a>
                                </div>
                            @elseif($extExisting === 'pdf')
                                <div class="flex items-center gap-2">
                                    <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm text-gray-700 font-medium">PDF Attached</p>
                                        <a href="{{ $existingImage }}" target="_blank"
                                            class="text-xs text-blue-600 hover:underline">Open PDF</a>
                                    </div>
                                </div>
                            @endif
                            <p class="text-xs text-gray-400 mt-2">Upload a new file below to replace it.</p>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Attachment <span class="text-gray-400 font-normal">(Optional — JPG, PNG, PDF up to 2MB)</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 hover:border-blue-400 rounded-xl p-5
                                    text-center transition-colors cursor-pointer"
                            onclick="document.getElementById('contactFileInput').click()">
                            <svg class="w-7 h-7 text-gray-400 mx-auto mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <p class="text-sm text-gray-500">Click to attach file</p>
                            <input id="contactFileInput" type="file" wire:model="image" accept="image/*,.pdf" class="hidden">
                        </div>

                        <div wire:loading wire:target="image" class="flex items-center gap-1.5 text-xs text-blue-600 mt-1.5">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                            </svg>
                            Uploading...
                        </div>

                        @if ($image)
                            @php
                                $ext = strtolower(
                                    method_exists($image, 'getClientOriginalExtension')
                                        ? $image->getClientOriginalExtension()
                                        : pathinfo($image->getFilename(), PATHINFO_EXTENSION),
                                );
                            @endphp
                            <div class="mt-3 bg-gray-50 rounded-xl border border-gray-200 p-3" wire:loading.remove wire:target="image">
                                @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $image->temporaryUrl() }}"
                                        class="h-32 w-auto rounded-lg border border-gray-200 shadow-sm">
                                @elseif($ext === 'pdf')
                                    <div class="flex items-center gap-2">
                                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm text-gray-700 font-medium">{{ $image->getClientOriginalName() }}</span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        @error('image')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Panel Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3 flex-shrink-0">
                    <button type="button" wire:click="closeModal"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="onSave" wire:loading.attr="disabled"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl
                               transition-all disabled:opacity-60 disabled:cursor-not-allowed flex items-center gap-2">
                        <span wire:loading.remove wire:target="onSave">
                            {{ $editId ? 'Update Message' : 'Send Message' }}
                        </span>
                        <span wire:loading wire:target="onSave" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ VIEW SLIDE-IN PANEL ══════════ --}}
    @if ($showViewModal && !empty($viewData))
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/30 backdrop-blur-[1px]" wire:click="closeViewModal"></div>

            <div class="absolute top-0 right-0 bottom-0 w-full max-w-lg bg-white shadow-2xl flex flex-col">

                {{-- Panel Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0
                    {{ $viewData['contact']->super_admin_reply ? 'bg-gradient-to-r from-green-50 to-emerald-50' : 'bg-gradient-to-r from-orange-50 to-amber-50' }}">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center
                            {{ $viewData['contact']->super_admin_reply ? 'bg-green-100' : 'bg-orange-100' }}">
                            <svg class="w-5 h-5 {{ $viewData['contact']->super_admin_reply ? 'text-green-600' : 'text-orange-500' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-800">Message Details</h2>
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium
                                {{ $viewData['contact']->super_admin_reply ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $viewData['contact']->super_admin_reply ? 'Replied' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    <button wire:click="closeViewModal"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Panel Body --}}
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs font-medium text-gray-400 mb-1">Organization</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $viewData['organization']->name ?? '—' }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-3.5">
                            <p class="text-xs font-medium text-gray-400 mb-1">Sent By</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $viewData['user']->name ?? '—' }}</p>
                            <p class="text-xs text-gray-400">{{ $viewData['user']->email ?? '' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-xs text-gray-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $viewData['contact']->created_at->format('d M Y, g:i A') }}
                        <span class="text-gray-300">·</span>
                        {{ $viewData['contact']->created_at->diffForHumans() }}
                    </div>

                    <div class="bg-gray-50 rounded-xl p-3.5">
                        <p class="text-xs font-medium text-gray-400 mb-1">Topic</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $viewData['contact']->topic }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-3.5">
                        <p class="text-xs font-medium text-gray-400 mb-2">Message</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                            {{ $viewData['contact']->admin_query }}
                        </p>
                    </div>

                    @if (!empty($viewData['contact']->image))
                        @php
                            $attUrl = $viewData['contact']->image;
                            $attExt = strtolower(pathinfo(parse_url($attUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                        @endphp
                        <div class="bg-purple-50 rounded-xl border border-purple-100 p-3.5">
                            <p class="text-xs font-medium text-purple-500 mb-2 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                </svg>
                                My Attachment
                            </p>
                            @if (in_array($attExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                <img src="{{ $attUrl }}" class="max-w-full rounded-lg shadow-sm border border-purple-100" alt="Attachment">
                                <a href="{{ $attUrl }}" target="_blank"
                                    class="text-xs text-blue-600 hover:underline mt-1.5 inline-block">↗ Open in new tab</a>
                            @elseif($attExt === 'pdf')
                                <iframe src="{{ $attUrl }}" class="w-full h-64 border rounded-lg shadow-sm"></iframe>
                                <a href="{{ $attUrl }}" target="_blank"
                                    class="text-xs text-blue-600 hover:underline mt-1.5 inline-block">↗ Open / Download PDF</a>
                            @else
                                <a href="{{ $attUrl }}" target="_blank" class="text-sm text-blue-600 hover:underline">View Attachment</a>
                            @endif
                        </div>
                    @endif

                    @if ($viewData['contact']->super_admin_reply)
                        <div class="bg-blue-50 rounded-xl border border-blue-100 p-4">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-sm font-semibold text-blue-700 flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    Super Admin's Reply
                                </p>
                                <span class="text-xs text-blue-400">
                                    {{ $viewData['contact']->updated_at->format('d M Y, g:i A') }}
                                </span>
                            </div>
                            <div class="bg-white rounded-lg border border-blue-100 p-3 mb-3">
                                <p class="text-sm text-blue-800 whitespace-pre-line leading-relaxed">
                                    {{ $viewData['contact']->super_admin_text }}
                                </p>
                            </div>

                            @if (
                                !empty($viewData['contact']->super_admin_reply) &&
                                $viewData['contact']->super_admin_reply !== '1' &&
                                filter_var($viewData['contact']->super_admin_reply, FILTER_VALIDATE_URL))
                                @php
                                    $replyUrl = $viewData['contact']->super_admin_reply;
                                    $replyExt = strtolower(pathinfo(parse_url($replyUrl, PHP_URL_PATH), PATHINFO_EXTENSION));
                                @endphp
                                <div class="mt-2">
                                    <p class="text-xs font-medium text-blue-500 mb-2">Reply Attachment:</p>
                                    @if (in_array($replyExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                        <img src="{{ $replyUrl }}" class="max-w-full rounded-lg border border-blue-100 shadow-sm">
                                        <a href="{{ $replyUrl }}" target="_blank"
                                            class="text-xs text-blue-600 hover:underline mt-1 inline-block">↗ Open in new tab</a>
                                    @elseif($replyExt === 'pdf')
                                        <iframe src="{{ $replyUrl }}" class="w-full h-64 border rounded-lg shadow-sm mt-1"></iframe>
                                        <a href="{{ $replyUrl }}" target="_blank"
                                            class="text-xs text-blue-600 hover:underline mt-1 inline-block">↗ Open / Download PDF</a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="bg-amber-50 rounded-xl border border-amber-100 p-4 flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span class="text-sm font-medium text-amber-700">Awaiting response from Super Admin</span>
                        </div>
                    @endif
                </div>

                {{-- Panel Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between flex-shrink-0">
                    @if (!$viewData['contact']->super_admin_reply)
                        <button type="button" wire:click="onEditContact({{ $viewData['contact']->id }})"
                            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-amber-600
                                   bg-amber-50 border border-amber-200 rounded-xl hover:bg-amber-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                    @else
                        <div></div>
                    @endif
                    <button type="button" wire:click="closeViewModal"
                        class="px-5 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ DELETE CONFIRM OVERLAY ══════════ --}}
    @if ($showDeleteConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px]" wire:click="cancelDelete"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-1">Delete Message?</h3>
                <p class="text-sm text-gray-500 mb-6">This action cannot be undone. The message and its attachment will be permanently removed.</p>
                <div class="flex items-center justify-center gap-3">
                    <button type="button" wire:click="cancelDelete"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" wire:click="confirmDelete" wire:loading.attr="disabled"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-red-500 hover:bg-red-600 rounded-xl
                               transition-colors disabled:opacity-60 flex items-center gap-2">
                        <span wire:loading.remove wire:target="confirmDelete">Yes, Delete</span>
                        <span wire:loading wire:target="confirmDelete" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
