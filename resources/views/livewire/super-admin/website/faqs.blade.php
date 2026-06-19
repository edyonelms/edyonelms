<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════ HEADER ══════════════════ --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('website-image/Group 11525.png') }}" alt="EDYONE LMS"
                        class="w-11 h-11 rounded-xl object-contain border border-gray-200 shadow-sm bg-white p-1 flex-shrink-0">
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 truncate">FAQs</h1>
                        <p class="text-xs text-gray-500 mt-0.5 truncate">Manage the questions shown on the website FAQ page.</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ url('web/faqs') }}" target="_blank" rel="noopener"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View Live
                    </a>
                    <button wire:click="openCreate"
                        class="inline-flex items-center gap-1.5 px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Add FAQ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 space-y-5">

        {{-- ── Search + category filter ── --}}
        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
            <div class="relative flex-1">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" /></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by question…"
                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
            </div>
            <select wire:model.live="categoryFilter"
                class="sm:w-56 px-3 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-white">
                <option value="">All categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        {{-- ── FAQ list ── --}}
        @if ($faqs->isEmpty())
            <div class="bg-white border border-gray-200 rounded-2xl p-12 text-center shadow-sm">
                <div class="text-4xl mb-3">❔</div>
                <h3 class="text-base font-semibold text-gray-800">No FAQs found</h3>
                <p class="text-sm text-gray-500 mt-1">Click <span class="font-semibold text-indigo-600">Add FAQ</span> to create your first question.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($faqs as $faq)
                    <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow" wire:key="faq-{{ $faq->id }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <span class="inline-block text-[11px] font-bold tracking-wide uppercase text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full mb-2">{{ $faq->category }}</span>
                                <h3 class="text-sm font-semibold text-gray-900">{{ $faq->question }}</h3>
                                <p class="text-sm text-gray-500 mt-1.5 leading-relaxed">{{ \Illuminate\Support\Str::limit($faq->answer, 180) }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <button wire:click="openEdit({{ $faq->id }})"
                                    class="px-2.5 py-1.5 text-xs font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50 transition-colors">Edit</button>
                                <button wire:click="confirmDelete({{ $faq->id }})"
                                    class="px-2.5 py-1.5 text-xs font-medium text-red-600 border border-red-200 rounded-lg hover:bg-red-50 transition-colors">Delete</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>{{ $faqs->links() }}</div>
        @endif
    </div>

    {{-- ══════════════════ SLIDE-IN PANEL ══════════════════ --}}
    @if ($showPanel)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.06] backdrop-blur-[1.5px]" wire:click="closePanel"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-lg bg-white shadow-2xl flex flex-col">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $editId ? 'Edit FAQ' : 'Add New FAQ' }}</h2>
                        <p class="text-xs text-gray-500 mt-0.5">This appears on the public FAQ page.</p>
                    </div>
                    <button wire:click="closePanel"
                        class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-6 py-6 space-y-5">

                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-rose-500">*</span></label>
                        <select wire:model="category"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 bg-white">
                            <option value="">— Choose existing —</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-2 my-2 text-xs text-gray-400">
                            <span class="h-px bg-gray-200 flex-1"></span> or add a new one <span class="h-px bg-gray-200 flex-1"></span>
                        </div>
                        <input type="text" wire:model="newCategory" placeholder="Type a new category (e.g. Payments)"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        <p class="text-xs text-gray-400 mt-1">If you type a new category, it will be created automatically.</p>
                        @error('category') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Question --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question <span class="text-rose-500">*</span></label>
                        <input type="text" wire:model="question" placeholder="e.g. Can parents pay fees online?"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        @error('question') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Answer / description --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description / Answer <span class="text-rose-500">*</span></label>
                        <textarea wire:model="answer" rows="8" placeholder="Write the answer here…"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 resize-y leading-relaxed"></textarea>
                        @error('answer') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-3.5 border-t border-gray-100 flex items-center justify-end gap-3 flex-shrink-0">
                    <button wire:click="closePanel" type="button" class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</button>
                    <button wire:click="save" type="button" wire:loading.attr="disabled" wire:target="save"
                        class="inline-flex items-center gap-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 disabled:opacity-60 text-white text-sm font-semibold px-5 py-2 rounded-lg">
                        <span wire:loading.remove wire:target="save">{{ $editId ? 'Update FAQ' : 'Add FAQ' }}</span>
                        <span wire:loading wire:target="save">Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════ DELETE CONFIRM ══════════════════ --}}
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
                    <h3 class="text-sm font-bold text-gray-900">Delete FAQ</h3>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600">Are you sure you want to delete this FAQ? This action cannot be undone.</p>
                </div>
                <div class="px-5 pb-5 flex items-center gap-2">
                    <button wire:click="deleteFaq"
                        class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">Yes, Delete</button>
                    <button wire:click="cancelDelete"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
