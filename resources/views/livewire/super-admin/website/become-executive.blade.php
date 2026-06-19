<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('website-image/Group 11525.png') }}" alt="EDYONE LMS"
                        class="w-11 h-11 rounded-xl object-contain border border-gray-200 shadow-sm bg-white p-1 flex-shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">Become an Executive</h1>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">Partner applications submitted from the website.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    @if ($newCount > 0)
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">{{ $newCount }} new</span>
                    @endif
                    <a href="{{ url('web/become-an-executive') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View Live
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-5">

        {{-- ── Status filter ── --}}
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500">Filter:</span>
            <select wire:model.live="statusFilter"
                class="w-48 px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-white">
                <option value="">All statuses</option>
                @foreach ($statuses as $s)
                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>

        {{-- ── Applications table ── --}}
        @if ($applications->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                <div class="text-4xl mb-3">📭</div>
                <h3 class="text-base font-semibold text-gray-800">No applications yet</h3>
                <p class="text-sm text-gray-500 mt-1">Partner applications submitted from the website will appear here.</p>
            </div>
        @else
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                                <th class="px-5 py-3">Applicant</th>
                                <th class="px-5 py-3">Contact</th>
                                <th class="px-5 py-3">Date</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($applications as $app)
                                <tr class="hover:bg-gray-50/70">
                                    <td class="px-5 py-3">
                                        <div class="font-semibold text-gray-900">{{ $app->full_name }}</div>
                                        <div class="text-xs text-gray-400">{{ $app->qualification }}</div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">
                                        <div>{{ $app->mobile }}</div>
                                        <div class="text-xs text-gray-400">{{ $app->email }}</div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 whitespace-nowrap">{{ $app->created_at->format('d M Y') }}</td>
                                    <td class="px-5 py-3">
                                        @php
                                            $badge = [
                                                'new'       => 'bg-amber-100 text-amber-700',
                                                'contacted' => 'bg-blue-100 text-blue-700',
                                                'approved'  => 'bg-green-100 text-green-700',
                                                'rejected'  => 'bg-red-100 text-red-700',
                                            ][$app->status] ?? 'bg-gray-100 text-gray-600';
                                        @endphp
                                        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $badge }}">{{ ucfirst($app->status) }}</span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button wire:click="viewApplication({{ $app->id }})"
                                                class="px-2.5 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                                                View
                                            </button>
                                            @if ($app->document_path)
                                                <button wire:click="downloadDocument({{ $app->id }})"
                                                    class="px-2.5 py-1.5 text-xs font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                                                    Doc
                                                </button>
                                            @endif
                                            <button wire:click="confirmDeleteApplication({{ $app->id }})"
                                                class="px-2.5 py-1.5 text-xs font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $applications->links() }}</div>
        @endif
    </div>

    {{-- ── Application detail + status modal ── --}}
    @if ($viewing)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden max-h-[92vh] flex flex-col">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $viewing->full_name }}</h3>
                        <p class="text-xs text-gray-500">Partner / Executive application</p>
                    </div>
                    <button wire:click="closeApplication" class="text-gray-400 hover:text-gray-600 text-xl leading-none">&times;</button>
                </div>

                <div class="p-6 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Email</div>
                            <div class="text-sm text-gray-800 break-all">{{ $viewing->email }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Mobile</div>
                            <div class="text-sm text-gray-800">{{ $viewing->mobile }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Qualification</div>
                            <div class="text-sm text-gray-800">{{ $viewing->qualification ?: '—' }}</div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-gray-400 uppercase">Applied On</div>
                            <div class="text-sm text-gray-800">{{ $viewing->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase">Address</div>
                        <div class="text-sm text-gray-800 whitespace-pre-line">{{ $viewing->address ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase">About / Message</div>
                        <div class="text-sm text-gray-800 whitespace-pre-line">{{ $viewing->description ?: '—' }}</div>
                    </div>

                    {{-- Status + remark editor --}}
                    <div class="border-t border-gray-100 pt-4 space-y-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
                            <select wire:model="editStatus"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                                @foreach ($statuses as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            @error('editStatus') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Remark / Description</label>
                            <textarea wire:model="editRemark" rows="3" placeholder="Add a note about this application or status…"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 resize-y"></textarea>
                            @error('editRemark') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex items-center gap-2 bg-gray-50">
                    @if ($viewing->document_path)
                        <button wire:click="downloadDocument({{ $viewing->id }})"
                            class="px-4 py-2 text-sm font-semibold text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">
                            ⬇ Document
                        </button>
                    @endif
                    <button wire:click="saveStatus" wire:loading.attr="disabled" wire:target="saveStatus"
                        class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">
                        <span wire:loading.remove wire:target="saveStatus">Save Status</span>
                        <span wire:loading wire:target="saveStatus">Saving…</span>
                    </button>
                    <button wire:click="closeApplication"
                        class="ml-auto px-4 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Delete confirm ── --}}
    @if ($pendingDelete !== null)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-red-50 flex items-center gap-3">
                    <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Delete Application</h3>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600">Are you sure you want to delete this application? This also removes the attached document and cannot be undone.</p>
                </div>
                <div class="px-5 pb-5 flex items-center gap-2">
                    <button wire:click="deleteApplication"
                        class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">Yes, Delete</button>
                    <button wire:click="cancelDeleteApplication"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
