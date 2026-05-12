<div class="p-4">

    {{-- ══════════ HEADER ══════════ --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5 sticky rounded-lg top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Contact Super Admin</h1>
                <p class="text-sm text-gray-500 mt-0.5">Send and manage your queries to Super Admin</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                    <span class="pr-4">Total: <strong class="text-gray-800">{{ $contacts->count() }}</strong></span>
                    <span class="px-4">Pending: <strong
                            class="text-amber-500">{{ $contacts->filter(fn($c) => empty($c->super_admin_reply))->count() }}</strong></span>
                    <span class="pl-4">Replied: <strong
                            class="text-emerald-600">{{ $contacts->filter(fn($c) => !empty($c->super_admin_reply))->count() }}</strong></span>
                </div>
                <button wire:click="onAddContact"
                    class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700
                       text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Contact Super Admin</span>
                    <span class="sm:hidden">New</span>
                </button>
            </div>
        </div>

        {{-- Mobile / Tablet stats --}}
        <div class="flex lg:hidden items-center gap-3 sm:gap-4 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Total: <strong class="text-gray-800">{{ $contacts->count() }}</strong></span>
            <span>Pending: <strong
                    class="text-amber-500">{{ $contacts->filter(fn($c) => empty($c->super_admin_reply))->count() }}</strong></span>
            <span>Replied: <strong
                    class="text-emerald-600">{{ $contacts->filter(fn($c) => !empty($c->super_admin_reply))->count() }}</strong></span>
        </div>
    </div>

    {{-- ══════════ FILTERS ══════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 mb-6 mt-4">
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
            <div
                class="bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md
                        transition-all duration-200 overflow-hidden">
                <div class="p-5">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3 flex-1 min-w-0">

                            {{-- Status icon --}}
                            <div
                                class="p-2.5 rounded-xl flex-shrink-0
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
                                    <span
                                        class="px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ $contact->super_admin_reply ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                                        {{ $contact->super_admin_reply ? 'Replied' : 'Pending' }}
                                    </span>
                                    @if ($contact->image)
                                        <span
                                            class="px-2 py-0.5 text-xs font-medium bg-purple-100 text-purple-700 rounded-full flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
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
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $contact->user->name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
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
                                class="p-2 rounded-lg border border-blue-200 text-blue-600
                                       hover:bg-blue-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>

                            {{-- Edit button (only if not yet replied) --}}
                            @if (!$contact->super_admin_reply)
                                <button wire:click="onEditContact({{ $contact->id }})" title="Edit"
                                    class="p-2 rounded-lg border border-amber-200 text-amber-600
                                           hover:bg-amber-50 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                                 m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            @endif

                            <button wire:click="onDeleteContact({{ $contact->id }})" title="Delete"
                                class="p-2 rounded-lg border border-red-200 text-red-500
                                       hover:bg-red-50 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7
                                             m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
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

    {{-- ══════════ ADD / EDIT MODAL ══════════ --}}
    <x-modal-form show="{{ $open }}" title="{{ $editId ? 'Edit Message' : 'Contact Super Admin' }}"
        submitAction="onSave" submitButton="{{ $editId ? 'Update Message' : 'Send Message' }}"
        closeAction="closeModal">

        <div class="space-y-4">
            <x-input wire:model.defer="topic" label="Topic *" placeholder="What is this about?" />
            @error('topic')
                <p class="text-xs text-red-500 -mt-2">{{ $message }}</p>
            @enderror

            <x-textarea wire:model.defer="admin_query" label="Your Message *"
                placeholder="Describe your query in detail..." rows="5" />
            @error('admin_query')
                <p class="text-xs text-red-500 -mt-2">{{ $message }}</p>
            @enderror

            {{-- Existing attachment when editing --}}
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
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
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

            {{-- File upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Attachment (Optional) — JPG, PNG, PDF up to 2MB
                </label>
                <div class="border-2 border-dashed border-gray-300 hover:border-blue-400 rounded-xl p-4
                            text-center transition-colors cursor-pointer"
                    onclick="document.getElementById('contactFileInput').click()">
                    <svg class="w-7 h-7 text-gray-400 mx-auto mb-1" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <p class="text-sm text-gray-500">Click to attach file</p>
                    <input id="contactFileInput" type="file" wire:model="image" accept="image/*,.pdf"
                        class="hidden">
                </div>

                {{-- Upload loading --}}
                <div wire:loading wire:target="image" class="flex items-center gap-1.5 text-xs text-blue-600 mt-1.5">
                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4" />
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Uploading...
                </div>

                {{-- New file preview --}}
                @if ($image)
                    @php
                        $ext = strtolower(
                            method_exists($image, 'getClientOriginalExtension')
                                ? $image->getClientOriginalExtension()
                                : pathinfo($image->getFilename(), PATHINFO_EXTENSION),
                        );
                    @endphp
                    <div class="mt-3 bg-gray-50 rounded-xl border border-gray-200 p-3" wire:loading.remove
                        wire:target="image">
                        @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                            <img src="{{ $image->temporaryUrl() }}"
                                class="h-32 w-auto rounded-lg border border-gray-200 shadow-sm">
                        @elseif($ext === 'pdf')
                            <div class="flex items-center gap-2">
                                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm text-gray-700 font-medium">
                                    {{ $image->getClientOriginalName() }}
                                </span>
                            </div>
                        @endif
                    </div>
                @endif
                @error('image')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </x-modal-form>

    {{-- ══════════ VIEW MODAL ══════════ --}}
    @if ($showViewModal && !empty($viewData))
        <x-view-modal :show="$showViewModal" :title="$viewModalTitle" closeAction="closeViewModal">
            <div class="space-y-5 text-left">

                {{-- Meta info --}}
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-gray-50 rounded-xl p-3.5">
                        <p class="text-xs font-medium text-gray-400 mb-1">Organization</p>
                        <p class="text-sm font-semibold text-gray-800">
                            {{ $viewData['organization']->name ?? '—' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3.5">
                        <p class="text-xs font-medium text-gray-400 mb-1">Sent By</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $viewData['user']->name ?? '—' }}</p>
                        <p class="text-xs text-gray-400">{{ $viewData['user']->email ?? '' }}</p>
                    </div>
                </div>

                {{-- Status + Date --}}
                <div class="flex items-center justify-between">
                    <span
                        class="px-3 py-1 text-sm font-medium rounded-full
                    {{ $viewData['contact']->super_admin_reply ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                        {{ $viewData['contact']->super_admin_reply ? '✓ Replied' : '⏳ Pending' }}
                    </span>
                    <span class="text-xs text-gray-400">
                        {{ $viewData['contact']->created_at->format('d M Y, g:i A') }}
                    </span>
                </div>

                {{-- Topic --}}
                <div class="bg-gray-50 rounded-xl p-3.5">
                    <p class="text-xs font-medium text-gray-400 mb-1">Topic</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $viewData['contact']->topic }}</p>
                </div>

                {{-- Message --}}
                <div class="bg-gray-50 rounded-xl p-3.5">
                    <p class="text-xs font-medium text-gray-400 mb-2">Message</p>
                    <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">
                        {{ $viewData['contact']->admin_query }}
                    </p>
                </div>

                {{-- ── My Attachment ── --}}
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
                            <img src="{{ $attUrl }}"
                                class="max-w-full rounded-lg shadow-sm border border-purple-100" alt="Attachment">
                            <a href="{{ $attUrl }}" target="_blank"
                                class="text-xs text-blue-600 hover:underline mt-1.5 inline-block">
                                ↗ Open in new tab
                            </a>
                        @elseif($attExt === 'pdf')
                            <iframe src="{{ $attUrl }}"
                                class="w-full h-72 border rounded-lg shadow-sm"></iframe>
                            <a href="{{ $attUrl }}" target="_blank"
                                class="text-xs text-blue-600 hover:underline mt-1.5 inline-block">
                                ↗ Open / Download PDF
                            </a>
                        @else
                            <a href="{{ $attUrl }}" target="_blank"
                                class="text-sm text-blue-600 hover:underline">View Attachment</a>
                        @endif
                    </div>
                @endif

                {{-- ── Super Admin Reply ── --}}
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

                        {{-- ── Reply Attachment (image / pdf) ── --}}
                        @if (
                            !empty($viewData['contact']->super_admin_reply) &&
                                $viewData['contact']->super_admin_reply !== '1' &&
                                filter_var($viewData['contact']->super_admin_reply, FILTER_VALIDATE_URL))
                            @php
                                $replyUrl = $viewData['contact']->super_admin_reply;
                                $replyExt = strtolower(
                                    pathinfo(parse_url($replyUrl, PHP_URL_PATH), PATHINFO_EXTENSION),
                                );
                            @endphp
                            <div class="mt-2">
                                <p class="text-xs font-medium text-blue-500 mb-2">Reply Attachment:</p>
                                @if (in_array($replyExt, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <img src="{{ $replyUrl }}"
                                        class="max-w-full rounded-lg border border-blue-100 shadow-sm">
                                    <a href="{{ $replyUrl }}" target="_blank"
                                        class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                        ↗ Open in new tab
                                    </a>
                                @elseif($replyExt === 'pdf')
                                    <iframe src="{{ $replyUrl }}"
                                        class="w-full h-72 border rounded-lg shadow-sm mt-1"></iframe>
                                    <a href="{{ $replyUrl }}" target="_blank"
                                        class="text-xs text-blue-600 hover:underline mt-1 inline-block">
                                        ↗ Open / Download PDF
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-amber-50 rounded-xl border border-amber-100 p-4 flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="text-sm font-medium text-amber-700">Awaiting response from Super Admin</span>
                    </div>
                @endif

            </div>
        </x-view-modal>
    @endif

</div>
