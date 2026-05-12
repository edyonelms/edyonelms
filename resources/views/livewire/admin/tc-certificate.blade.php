<div class="min-h-screen bg-gray-50">

    {{-- ══════════════ HEADER ══════════════ --}}
    <div class="bg-gradient-to-r from-yellow-50 to-amber-50 border-b border-gray-200">
        <div class="px-4 py-6 sm:px-6 lg:px-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Certificates</h1>
                <p class="mt-1 text-sm text-gray-500">Achievement, Participation & Transfer Certificates</p>
            </div>
            @if ($activeTab === 'tc')
                <button wire:click="createTc"
                    class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-lg font-medium shadow-md flex items-center gap-2 transition-all">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Issue TC
                </button>
            @else
                <button wire:click="createCert"
                    class="px-6 py-2.5 bg-gradient-to-r from-yellow-500 to-amber-500 hover:from-yellow-600 hover:to-amber-600 text-white rounded-lg font-medium shadow-md flex items-center gap-2 transition-all">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Issue Certificate
                </button>
            @endif
        </div>
    </div>

    <div class="p-6">

        {{-- ══════════════ STATS ══════════════ --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-white border-l-4 border-l-yellow-500 shadow-sm rounded-lg p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Achievement</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->statistics['achievement'] }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-full">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
            </div>
            <div
                class="bg-white border-l-4 border-l-blue-500 shadow-sm rounded-lg p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Participation</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->statistics['participation'] }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>
            <div
                class="bg-white border-l-4 border-l-red-500 shadow-sm rounded-lg p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer (TC)</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->statistics['tc'] }}</p>
                </div>
                <div class="p-3 bg-red-50 rounded-full">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- ══════════════ TABS ══════════════ --}}
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-1">
                @foreach ([['key' => 'achievement', 'label' => 'Achievement', 'emoji' => '🏆', 'ac' => 'yellow'], ['key' => 'participation', 'label' => 'Participation', 'emoji' => '🎖', 'ac' => 'blue'], ['key' => 'tc', 'label' => 'Transfer Certificate', 'emoji' => '📄', 'ac' => 'red']] as $tab)
                    <button wire:click="$set('activeTab','{{ $tab['key'] }}')"
                        class="py-3 px-4 border-b-2 font-medium text-sm transition-colors flex items-center gap-1.5
                        {{ $activeTab === $tab['key']
                            ? 'border-' . $tab['ac'] . '-500 text-' . $tab['ac'] . '-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        <span>{{ $tab['emoji'] }}</span>
                        {{ $tab['label'] }}
                        <span
                            class="ml-1 px-2 py-0.5 text-xs rounded-full
                        {{ $activeTab === $tab['key'] ? 'bg-' . $tab['ac'] . '-100 text-' . $tab['ac'] . '-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $this->statistics[$tab['key']] }}
                        </span>
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- ══════════════ SEARCH ══════════════ --}}
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ $activeTab === 'tc' ? 'Search by student, TC no...' : 'Search by student, event, cert no...' }}"
                    class="w-full border rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
            </div>
            <select wire:model.live="perPage"
                class="border rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-yellow-400">
                <option value="10">10 per page</option>
                <option value="25">25 per page</option>
                <option value="50">50 per page</option>
            </select>
        </div>

        {{-- ══════════════ ACHIEVEMENT / PARTICIPATION CARDS ══════════════ --}}
        @if (in_array($activeTab, ['achievement', 'participation']))
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse ($certificates as $cert)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div
                            class="h-1.5 {{ $cert->type === 'achievement' ? 'bg-gradient-to-r from-yellow-400 to-amber-500' : 'bg-gradient-to-r from-blue-400 to-indigo-500' }}">
                        </div>
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-3">
                                <span
                                    class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $cert->type === 'achievement' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $cert->type === 'achievement' ? '🏆 Achievement' : '🎖 Participation' }}
                                </span>
                                <span class="text-xs font-mono text-gray-400">{{ $cert->certificate_no }}</span>
                            </div>
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-9 h-9 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-sm text-white {{ $cert->type === 'achievement' ? 'bg-gradient-to-br from-yellow-400 to-amber-500' : 'bg-gradient-to-br from-blue-400 to-indigo-500' }}">
                                    {{ strtoupper(substr($cert->student->full_name ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">
                                        {{ $cert->student->full_name ?? '—' }}</p>
                                    @if ($cert->student?->admission_no)
                                        <p class="text-xs text-gray-400">Adm: {{ $cert->student->admission_no }}</p>
                                    @endif
                                </div>
                            </div>
                            <p class="text-sm font-medium text-gray-800 mb-1">{{ $cert->event_name }}</p>
                            @if ($cert->description)
                                <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $cert->description }}</p>
                            @endif
                            <div
                                class="flex items-center justify-between text-xs text-gray-400 pt-2 border-t border-gray-100 mt-2">
                                <span>📅 {{ $cert->issued_date->format('d M Y') }}</span>
                                <span>By: {{ $cert->issued_by }}</span>
                            </div>
                        </div>
                        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-2">

                            {{-- ✅ DIRECT PDF DOWNLOAD --}}
                            <a href="{{ route('admin.cert.download', ['organization' => $organization->id, 'id' => $cert->id]) }}"
                                target="_blank"
                                class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-lg transition flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download PDF
                            </a>

                            <button wire:click="editCert({{ $cert->id }})"
                                class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button wire:click="deleteCert({{ $cert->id }})"
                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 bg-white rounded-xl shadow-sm p-12 text-center">
                        <p class="text-5xl mb-3">{{ $activeTab === 'achievement' ? '🏆' : '🎖' }}</p>
                        <h3 class="text-sm font-medium text-gray-900">No {{ $activeTab }} certificates yet</h3>
                        <button wire:click="createCert"
                            class="mt-4 px-5 py-2 bg-yellow-500 text-white text-sm rounded-lg hover:bg-yellow-600 transition">Issue
                            Certificate</button>
                    </div>
                @endforelse
            </div>
            @if ($certificates->hasPages())
                <div class="mt-6">{{ $certificates->links() }}</div>
            @endif
        @endif

        {{-- ══════════════ TC LIST ══════════════ --}}
        @if ($activeTab === 'tc')
            <div class="space-y-3">
                @forelse ($tcList as $tc)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="h-1.5 bg-gradient-to-r from-red-400 to-rose-500"></div>
                        <div class="p-5">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-red-400 to-rose-500 flex items-center justify-center text-white font-bold flex-shrink-0">
                                        {{ strtoupper(substr($tc->student->full_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-900">{{ $tc->student->full_name ?? '—' }}
                                        </p>
                                        <div class="flex items-center gap-2 flex-wrap mt-0.5">
                                            @if ($tc->student?->admission_no)
                                                <span class="text-xs text-gray-400">Adm:
                                                    {{ $tc->student->admission_no }}</span>
                                            @endif
                                            <span
                                                class="text-xs font-mono text-rose-600 font-semibold">{{ $tc->tc_no }}</span>
                                            @if ($tc->book_no)
                                                <span class="text-xs text-gray-400">Book: {{ $tc->book_no }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">

                                    {{-- ✅ DIRECT TC PDF DOWNLOAD --}}
                                    <a href="{{ route('admin.tc.download', ['organization' => $organization->id, 'id' => $tc->id]) }}"
                                        target="_blank"
                                        class="px-3 py-1.5 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-lg transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Download PDF
                                    </a>

                                    <button wire:click="editTc({{ $tc->id }})"
                                        class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="deleteTc({{ $tc->id }})"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                                <div>
                                    <p class="text-gray-400">Last Class</p>
                                    <p class="font-medium text-gray-800">{{ $tc->last_class_studied ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Conduct</p>
                                    <p class="font-medium text-gray-800">{{ $tc->general_conduct }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Issue Date</p>
                                    <p class="font-medium text-gray-800">{{ $tc->issue_date->format('d M Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400">Reason</p>
                                    <p class="font-medium text-gray-800 truncate">{{ $tc->reason_for_leaving ?: '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <p class="text-5xl mb-3">📄</p>
                        <h3 class="text-sm font-medium text-gray-900">No Transfer Certificates issued yet</h3>
                        <button wire:click="createTc"
                            class="mt-4 px-5 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition">Issue
                            TC</button>
                    </div>
                @endforelse
            </div>
            @if ($tcList->hasPages())
                <div class="mt-6">{{ $tcList->links() }}</div>
            @endif
        @endif

    </div>

    <x-modal-form show="{{ $certModal }}" title="{{ $editCertId ? 'Edit Certificate' : 'Issue Certificate' }}"
        submitAction="saveCert" submitButton="{{ $editCertId ? 'Update' : 'Issue' }}" closeAction="closeCertModal">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type <span
                        class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="type" value="achievement" class="sr-only peer">
                        <div
                            class="flex items-center gap-2 px-4 py-3 rounded-lg border-2 transition peer-checked:border-yellow-400 peer-checked:bg-yellow-50 border-gray-200 hover:border-gray-300">
                            <span class="text-xl">🏆</span>
                            <div>
                                <p class="text-sm font-semibold">Achievement</p>
                                <p class="text-xs text-gray-500">Award for excellence</p>
                            </div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" wire:model.live="type" value="participation" class="sr-only peer">
                        <div
                            class="flex items-center gap-2 px-4 py-3 rounded-lg border-2 transition peer-checked:border-blue-400 peer-checked:bg-blue-50 border-gray-200 hover:border-gray-300">
                            <span class="text-xl">🎖</span>
                            <div>
                                <p class="text-sm font-semibold">Participation</p>
                                <p class="text-xs text-gray-500">For taking part</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Student <span
                        class="text-red-500">*</span></label>
                <select wire:model.defer="student_detail_id"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                    <option value="">— Select Student —</option>
                    @foreach ($students as $s)
                        <option value="{{ $s['id'] }}">
                            {{ $s['full_name'] }}{{ $s['admission_no'] ? ' (' . $s['admission_no'] . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('student_detail_id')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>

            <div class="sm:col-span-2">
                <x-input wire:model.defer="event_name" label="Event / Activity Name *" required
                    placeholder="{{ $type === 'achievement' ? 'e.g. Annual Science Olympiad 2024' : 'e.g. Annual Sports Day 2024' }}" />
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea wire:model.defer="description" rows="2"
                    placeholder="{{ $type === 'achievement' ? 'For securing First Position in...' : 'For actively participating in...' }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm resize-none focus:ring-2 focus:ring-yellow-400"></textarea>
            </div>

            <x-input wire:model.defer="issued_by" label="Issued By *" required placeholder="e.g. Rajesh Kumar" />
            <x-input wire:model.defer="issued_by_designation" label="Designation" placeholder="e.g. Principal" />
            <div class="sm:col-span-2">
                <x-input wire:model.defer="issued_date" label="Issued Date *" type="date" required />
            </div>
        </div>
    </x-modal-form>

    @if ($tcModal)
        <div class="fixed inset-0 z-[999] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" wire:click="closeTcModal"></div>
            <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl flex flex-col max-h-[92vh]">

                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-gray-100 bg-rose-50 rounded-t-2xl">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">📄 {{ $editTcId ? 'Edit' : 'Issue' }} Transfer
                            Certificate</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Fill all details as per student records</p>
                    </div>
                    <button wire:click="closeTcModal"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-rose-100 text-gray-500 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pb-1 border-b">Student
                            Information</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Student <span
                                        class="text-red-500">*</span></label>
                                <select wire:model.defer="tc_student_id"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                                    <option value="">— Select Student —</option>
                                    @foreach ($students as $s)
                                        <option value="{{ $s['id'] }}">
                                            {{ $s['full_name'] }}{{ $s['admission_no'] ? ' (' . $s['admission_no'] . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tc_student_id')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                                <input type="text" wire:model.defer="nationality"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Book No.</label>
                                <input type="text" wire:model.defer="book_no" placeholder="e.g. 096"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div class="sm:col-span-2 flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                                <input type="checkbox" wire:model.defer="is_sc_st" id="tc_scst"
                                    class="h-4 w-4 text-rose-500 rounded border-gray-300">
                                <label for="tc_scst" class="text-sm text-gray-700">Belongs to Scheduled Caste /
                                    Scheduled Tribe</label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pb-1 border-b">Academic
                            Details</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class Last Studied</label>
                                <input type="text" wire:model.defer="last_class_studied" placeholder="e.g. 12th"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Exam Last Taken with
                                    Result</label>
                                <input type="text" wire:model.defer="exam_last_taken"
                                    placeholder="e.g. 12th Passed"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Whether Failed</label>
                                <select wire:model.defer="whether_failed"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                                    @foreach ($failedOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Qualified for
                                    Promotion</label>
                                <select wire:model.defer="qualified_for_promotion"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subjects Studied</label>
                                <input type="text" wire:model.defer="subjects_studied"
                                    placeholder="e.g. Hindi, English, Mathematics, Science"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pb-1 border-b">
                            Attendance & Fees</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Total Working Days</label>
                                <input type="number" wire:model.defer="total_working_days" min="0"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Days Present</label>
                                <input type="number" wire:model.defer="days_present" min="0"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fees Paid Upto</label>
                                <input type="text" wire:model.defer="fees_paid_upto" placeholder="e.g. March 2026"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Concession (if
                                    any)</label>
                                <input type="text" wire:model.defer="fee_concession" placeholder="e.g. None"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pb-1 border-b">
                            Activities & Conduct</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">NCC / Scout / Guide</label>
                                <select wire:model.defer="is_ncc_scout"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                                    @foreach ($nccOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">General Conduct</label>
                                <select wire:model.defer="general_conduct"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                                    @foreach ($conductOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Games / Extra-Curricular
                                    Activities</label>
                                <input type="text" wire:model.defer="extra_activities"
                                    placeholder="e.g. Cricket, Debate"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 pb-1 border-b">Issue
                            Details</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Application <span
                                        class="text-red-500">*</span></label>
                                <input type="date" wire:model.defer="application_date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                                @error('application_date')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Issue <span
                                        class="text-red-500">*</span></label>
                                <input type="date" wire:model.defer="tc_issue_date"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                                @error('tc_issue_date')
                                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Leaving</label>
                                <input type="text" wire:model.defer="reason_for_leaving"
                                    placeholder="e.g. No Further Classes"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Any Other Remark</label>
                                <textarea wire:model.defer="tc_remarks" rows="2" placeholder="e.g. No"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-rose-400 text-sm resize-none"></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                    <button wire:click="closeTcModal"
                        class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="saveTc"
                        class="px-6 py-2 text-sm font-medium text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-sm transition">
                        {{ $editTcId ? 'Update TC' : 'Issue TC' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
