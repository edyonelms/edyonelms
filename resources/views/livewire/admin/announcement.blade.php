<div class="p-4 sm:p-6">

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-6 py-5 mb-6 rounded-xl shadow-sm sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Announcements</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage and publish announcements for your organization</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                {{-- Stats — desktop --}}
                <div class="hidden sm:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                    <span class="pr-4">
                        Total: <strong class="text-gray-800">{{ $stats['total'] }}</strong>
                    </span>
                    <span class="px-4">
                        This Month: <strong class="text-gray-800">{{ $stats['this_month'] }}</strong>
                    </span>
                    <span class="pl-4">
                        Last Month: <strong class="text-gray-800">{{ $stats['last_month'] }}</strong>
                    </span>
                </div>

                {{-- Date Filter --}}
                <x-native-select wire:model.live="dateFilter" class="!w-36 !text-sm">
                    <option value="all">All Time</option>
                    <option value="7">Last 7 Days</option>
                    <option value="15">Last 15 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="60">Last 60 Days</option>
                </x-native-select>

                {{-- Add Button --}}
                <button wire:click="openModal"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Announcement
                </button>
            </div>
        </div>

        {{-- Stats — mobile --}}
        <div class="flex sm:hidden items-center gap-4 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Total: <strong class="text-gray-800">{{ $stats['total'] }}</strong></span>
            <span>This Month: <strong class="text-gray-800">{{ $stats['this_month'] }}</strong></span>
            <span>Last Month: <strong class="text-gray-800">{{ $stats['last_month'] }}</strong></span>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════
         ANNOUNCEMENT LIST
    ══════════════════════════════════════════════════ --}}
    <div class="space-y-4">
        @forelse ($announcements as $announcement)
            <div wire:click="viewAnnouncement({{ $announcement->id }})"
                class="p-5 bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-200
                       border border-gray-100 cursor-pointer hover:border-blue-200 group">
                <div class="flex items-start gap-4">

                    {{-- Icon --}}
                    <div class="flex-shrink-0">
                        <div class="p-3 bg-blue-50 rounded-lg group-hover:bg-blue-100 transition-colors duration-200">
                            <x-icon name="megaphone" class="h-6 w-6 text-blue-600" />
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1 min-w-0">

                                {{-- Title + Badge --}}
                                <div class="flex items-center gap-3 mb-1.5">
                                    <h3
                                        class="font-bold text-base text-gray-900 truncate group-hover:text-blue-700 transition-colors">
                                        {{ $announcement->announcement_name }}
                                    </h3>
                                    <span
                                        class="flex-shrink-0 px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ $announcement->type === 'all'
                                            ? 'bg-purple-100 text-purple-700'
                                            : ($announcement->type === 'user'
                                                ? 'bg-green-100 text-green-700'
                                                : 'bg-orange-100 text-orange-700') }}">
                                        {{ ucfirst($announcement->type) }}
                                    </span>
                                </div>

                                {{-- Content --}}
                                <p class="text-sm text-gray-600 leading-relaxed mb-3 line-clamp-2">
                                    {{ $announcement->announcement_content }}
                                </p>

                                {{-- Attachments --}}
                                @if ($announcement->announcement_image || $announcement->announcement_pdf)
                                    <div class="flex items-center gap-3 mb-3">
                                        @if ($announcement->announcement_image)
                                            <span class="inline-flex items-center gap-1 text-xs text-blue-600">
                                                <i class="fas fa-image"></i> Image
                                            </span>
                                        @endif
                                        @if ($announcement->announcement_pdf)
                                            <span class="inline-flex items-center gap-1 text-xs text-red-500">
                                                <i class="fas fa-file-pdf"></i> PDF
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Meta --}}
                                <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-user-circle"></i>
                                        {{ $announcement->user->name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i class="fas fa-clock"></i>
                                        {{ $announcement->created_at->format('M j, Y') }} at
                                        {{ $announcement->created_at->format('g:i A') }}
                                    </span>
                                </div>
                            </div>

                            {{-- ✅ Icon Action Buttons --}}
                            <div class="flex items-center gap-1 flex-shrink-0" onclick="event.stopPropagation()">

                                {{-- Edit --}}
                                <button wire:click="edit({{ $announcement->id }})" title="Edit"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg
                                           text-gray-400 hover:text-blue-600 hover:bg-blue-50
                                           transition-colors border border-transparent hover:border-blue-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5
                                               m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                {{-- Delete --}}
                                <button wire:click="onDelete({{ $announcement->id }})" title="Delete"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg
                                           text-gray-400 hover:text-red-600 hover:bg-red-50
                                           transition-colors border border-transparent hover:border-red-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858
                                               L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="w-20 h-20 mx-auto mb-4 bg-blue-50 rounded-full flex items-center justify-center">
                    <i class="fas fa-bullhorn text-3xl text-blue-500"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No announcements yet</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto text-sm">
                    Create your first announcement to share important information with your organization.
                </p>
                <button wire:click="openModal"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Announcement
                </button>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $announcements->links() }}
    </div>

    {{-- ══════════════════════════════════════════════════
         CREATE / EDIT MODAL (unchanged)
    ══════════════════════════════════════════════════ --}}
    <x-modal-form show="{{ $open }}" title="{{ $editId ? 'Edit Announcement' : 'Add Announcement' }}"
        submitAction="save" submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="closeModal">
        <div class="grid grid-cols-1 gap-4">
            <x-input wire:model.defer="announcementName" label="Announcement Name" required />
            <x-textarea wire:model.defer="announcementContent" label="Announcement Content" required />
            <x-native-select wire:model.defer="type" label="Type" required>
                <option value="all">All</option>
                <option value="user">Student</option>
                <option value="teacher">Teacher</option>
            </x-native-select>

            {{-- Image Upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Announcement Image</label>
                @if ($editId && !$announcementImage)
                    @php $ann = \App\Models\Admin\Announcement::find($editId) @endphp
                    @if ($ann?->announcement_image)
                        <div class="flex items-center gap-3 mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                            <img src="{{ $ann->announcement_image }}" class="h-14 w-14 object-cover rounded">
                            <button wire:click="deleteFile('image')" type="button"
                                class="text-xs text-red-600 hover:text-red-800 font-medium">Remove</button>
                        </div>
                    @endif
                @endif
                <input type="file" wire:model="announcementImage"
                    class="block w-full text-sm text-gray-500
                           file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                           file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100">
                @error('announcementImage')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- PDF Upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Announcement PDF</label>
                @if ($editId && !$announcementPdf)
                    @php $ann = \App\Models\Admin\Announcement::find($editId) @endphp
                    @if ($ann?->announcement_pdf)
                        <div class="flex items-center gap-3 mb-2 p-2 bg-gray-50 rounded-lg border border-gray-200">
                            <a href="{{ $ann->announcement_pdf }}" target="_blank"
                                class="text-sm text-blue-600 hover:text-blue-800 font-medium">View PDF</a>
                            <button wire:click="deleteFile('pdf')" type="button"
                                class="text-xs text-red-600 hover:text-red-800 font-medium">Remove</button>
                        </div>
                    @endif
                @endif
                <input type="file" wire:model="announcementPdf" accept=".pdf"
                    class="block w-full text-sm text-gray-500
                           file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                           file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100">
                @error('announcementPdf')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </x-modal-form>

    {{-- ══════════════════════════════════════════════════
         VIEW MODAL (unchanged logic, same structure)
    ══════════════════════════════════════════════════ --}}
    <x-content-modal show="viewModal" title="Announcement Details" closeAction="closeViewModal" maxWidth="3xl">
        <div class="space-y-6">
            @if ($selectedAnnouncement)
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">{{ $selectedAnnouncement->announcement_name }}
                        </h2>
                        <div class="flex items-center gap-3 mt-2">
                            <span
                                class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $selectedAnnouncement->type === 'all'
                                    ? 'bg-purple-100 text-purple-700'
                                    : ($selectedAnnouncement->type === 'user'
                                        ? 'bg-green-100 text-green-700'
                                        : 'bg-orange-100 text-orange-700') }}">
                                {{ ucfirst($selectedAnnouncement->type) }}
                            </span>
                            <span class="text-sm text-gray-400">
                                {{ $selectedAnnouncement->created_at->format('F j, Y \a\t g:i A') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div>
                    <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                        {{ $selectedAnnouncement->announcement_content }}
                    </p>
                </div>

                @if ($selectedAnnouncement->announcement_image || $selectedAnnouncement->announcement_pdf)
                    <div class="border-t border-gray-100 pt-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Attachments</h3>
                        <div class="space-y-2">
                            @if ($selectedAnnouncement->announcement_image)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="p-2 bg-blue-50 rounded">
                                        <i class="fas fa-image text-blue-500"></i>
                                    </div>
                                    <span class="flex-1 text-sm text-gray-700 font-medium">Image Attachment</span>
                                    <a href="{{ $selectedAnnouncement->announcement_image }}" target="_blank"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i> View
                                    </a>
                                </div>
                            @endif
                            @if ($selectedAnnouncement->announcement_pdf)
                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="p-2 bg-red-50 rounded">
                                        <i class="fas fa-file-pdf text-red-500"></i>
                                    </div>
                                    <span class="flex-1 text-sm text-gray-700 font-medium">PDF Document</span>
                                    <a href="{{ $selectedAnnouncement->announcement_pdf }}" target="_blank"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium flex items-center gap-1">
                                        <i class="fas fa-external-link-alt"></i> View
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="border-t border-gray-100 pt-4 flex items-center justify-between text-xs text-gray-400">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-user-circle"></i> Posted by {{ $selectedAnnouncement->user->name }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="fas fa-clock"></i> {{ $selectedAnnouncement->created_at->diffForHumans() }}
                    </span>
                </div>
            @endif
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-button wire:click="closeViewModal" label="Close" class="!px-4 !py-2" />
                @if ($selectedAnnouncement)
                    <x-button wire:click="editFromView({{ $selectedAnnouncement->id }})" positive label="Edit"
                        class="!px-4 !py-2" />
                @endif
            </div>
        </x-slot>
    </x-content-modal>

</div>
