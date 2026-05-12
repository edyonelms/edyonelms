<div class="min-h-screen bg-gray-50/50">

    {{-- ═══ STICKY HEADER ═══ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-6 pt-4 pb-3 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-700 leading-tight">TC & Certificates</h1>
                <p class="text-xs text-gray-400 mt-0.5">Manage achievement, participation and transfer certificates</p>
            </div>
            @if($activeTab === 'tc')
                <x-button emerald label="Issue TC" icon="plus" wire:click="openTcModal" />
            @else
                <x-button emerald label="Issue Certificate" icon="plus" wire:click="openCertModal" />
            @endif
        </div>

        {{-- Analytics Strip --}}
        <div class="px-6 pb-3 grid grid-cols-3 gap-3">
            <div class="flex items-center gap-3 bg-yellow-50 rounded-xl px-4 py-3 border border-yellow-100">
                <div class="w-8 h-8 rounded-lg bg-yellow-400 flex items-center justify-center flex-shrink-0">
                    <x-icon name="star" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Achievement</p>
                    <p class="text-lg font-bold text-yellow-700">{{ $achievementCount }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="trophy" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Participation</p>
                    <p class="text-lg font-bold text-blue-700">{{ $participationCount }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-red-50 rounded-xl px-4 py-3 border border-red-100">
                <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="document-arrow-down" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Transfer Cert (TC)</p>
                    <p class="text-lg font-bold text-red-700">{{ $tcCount }}</p>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="px-6 flex gap-1 border-t border-gray-100 pt-2 pb-1">
            @foreach(['achievement' => 'Achievement', 'participation' => 'Participation', 'tc' => 'Transfer Certificate'] as $tab => $label)
            <button wire:click="setTab('{{ $tab }}')"
                class="px-4 py-1.5 text-sm font-medium rounded-lg transition-colors
                    {{ $activeTab === $tab ? 'bg-emerald-600 text-white' : 'text-gray-500 hover:bg-gray-100' }}">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    <div class="p-6 space-y-5">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-green-500" />
                {{ session('success') }}
            </div>
        @endif

        {{-- ─── Search Bar ─────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-4">
            <div class="relative max-w-sm">
                <x-icon name="magnifying-glass" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input wire:model.live="search" type="text"
                    placeholder="{{ $activeTab === 'tc' ? 'TC no, name, admission no…' : 'Cert no, event, name…' }}"
                    class="w-full pl-9 pr-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
            </div>
        </div>

        {{-- ─── Table ──────────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-4 py-3 font-semibold text-gray-600">#</th>
                            @if($activeTab === 'tc')
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">TC No</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Student</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Last Class</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Conduct</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Issue Date</th>
                            @else
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Cert No</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Student</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Event / Activity</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Issued By</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-600">Issue Date</th>
                            @endif
                            <th class="text-right px-4 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @if($activeTab === 'tc')
                            @forelse($tcList as $tc)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration + ($tcList->currentPage() - 1) * $tcList->perPage() }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $tc->tc_no ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-800">{{ $tc->student?->full_name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $tc->student?->admission_no ?? '' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $tc->last_class_studied ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                            {{ $tc->general_conduct }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">
                                        {{ $tc->issue_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="viewRecord({{ $tc->id }}, 'tc')"
                                                class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </button>
                                            <button wire:click="openTcModal({{ $tc->id }})"
                                                class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </button>
                                            @if($pendingDeleteId === $tc->id && $pendingDeleteType === 'tc')
                                                <div class="flex items-center gap-1">
                                                    <button wire:click="doDelete"
                                                        class="px-2 py-1 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600">Confirm</button>
                                                    <button wire:click="cancelDelete"
                                                        class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Cancel</button>
                                                </div>
                                            @else
                                                <button wire:click="deleteRecord({{ $tc->id }}, 'tc')"
                                                    class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                        <x-icon name="document-arrow-down" class="w-10 h-10 mx-auto mb-2 text-gray-200" />
                                        <p class="text-sm">No transfer certificates found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        @else
                            @forelse($certificates as $cert)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration + ($certificates->currentPage() - 1) * $certificates->perPage() }}</td>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $cert->certificate_no ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <p class="font-medium text-gray-800">{{ $cert->student?->full_name ?? '—' }}</p>
                                        <p class="text-xs text-gray-400">{{ $cert->student?->admission_no ?? '' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $cert->event_name }}</td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">
                                        {{ $cert->issued_by }}
                                        @if($cert->issued_by_designation)
                                            <span class="text-gray-400">({{ $cert->issued_by_designation }})</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">
                                        {{ $cert->issued_date?->format('d M Y') ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="viewRecord({{ $cert->id }}, 'cert')"
                                                class="p-1.5 text-blue-500 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </button>
                                            <button wire:click="openCertModal({{ $cert->id }})"
                                                class="p-1.5 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit">
                                                <x-icon name="pencil" class="w-4 h-4" />
                                            </button>
                                            @if($pendingDeleteId === $cert->id && $pendingDeleteType === 'cert')
                                                <div class="flex items-center gap-1">
                                                    <button wire:click="doDelete"
                                                        class="px-2 py-1 text-xs bg-red-500 text-white rounded-lg hover:bg-red-600">Confirm</button>
                                                    <button wire:click="cancelDelete"
                                                        class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Cancel</button>
                                                </div>
                                            @else
                                                <button wire:click="deleteRecord({{ $cert->id }}, 'cert')"
                                                    class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                        <x-icon name="document-text" class="w-10 h-10 mx-auto mb-2 text-gray-200" />
                                        <p class="text-sm">No {{ $activeTab }} certificates found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        @endif
                    </tbody>
                </table>
            </div>

            @php $paginatedData = $activeTab === 'tc' ? $tcList : $certificates; @endphp
            @if($paginatedData->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $paginatedData->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- ═══ CERTIFICATE MODAL ═══ --}}
    @if($certModal)
    <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);"
         wire:click="closeCertModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl flex flex-col max-h-[90vh]"
             wire:click.stop>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-800">{{ $editCertId ? 'Edit Certificate' : 'Issue Certificate' }}</h2>
                <button wire:click="closeCertModal" class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <div class="px-6 py-5 overflow-y-auto space-y-4">

                {{-- Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <label class="flex items-center gap-2 px-3 py-2 border rounded-xl cursor-pointer transition-colors
                            {{ $certType === 'achievement' ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200 hover:bg-gray-50' }}">
                            <input type="radio" wire:model.live="certType" value="achievement" class="sr-only" />
                            <x-icon name="star" class="w-4 h-4 {{ $certType === 'achievement' ? 'text-yellow-500' : 'text-gray-400' }}" />
                            <span class="text-sm {{ $certType === 'achievement' ? 'text-yellow-700 font-medium' : 'text-gray-600' }}">Achievement</span>
                        </label>
                        <label class="flex items-center gap-2 px-3 py-2 border rounded-xl cursor-pointer transition-colors
                            {{ $certType === 'participation' ? 'border-blue-400 bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                            <input type="radio" wire:model.live="certType" value="participation" class="sr-only" />
                            <x-icon name="trophy" class="w-4 h-4 {{ $certType === 'participation' ? 'text-blue-500' : 'text-gray-400' }}" />
                            <span class="text-sm {{ $certType === 'participation' ? 'text-blue-700 font-medium' : 'text-gray-600' }}">Participation</span>
                        </label>
                    </div>
                </div>

                {{-- Student --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                    <select wire:model="student_detail_id"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none">
                        <option value="">Select student…</option>
                        @foreach($students as $s)
                            <option value="{{ $s['id'] }}">{{ $s['full_name'] }} ({{ $s['admission_no'] }})</option>
                        @endforeach
                    </select>
                    @error('student_detail_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Event Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event / Activity <span class="text-red-500">*</span></label>
                    <input wire:model="event_name" type="text" maxlength="255"
                        placeholder="e.g. Science Olympiad 2025"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    @error('event_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {{-- Issued By --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issued By <span class="text-red-500">*</span></label>
                        <input wire:model="issued_by" type="text" maxlength="255"
                            placeholder="e.g. Principal"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                        @error('issued_by') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Designation --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Designation</label>
                        <input wire:model="issued_by_designation" type="text" maxlength="100"
                            placeholder="e.g. School Principal"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                </div>

                {{-- Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date <span class="text-red-500">*</span></label>
                    <input wire:model="issued_date" type="date"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    @error('issued_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="3" maxlength="1000"
                        placeholder="Optional description…"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none resize-none"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 flex-shrink-0">
                <button wire:click="closeCertModal"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="saveCert"
                    class="px-5 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors">
                    {{ $editCertId ? 'Update Certificate' : 'Issue Certificate' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ TC MODAL ═══ --}}
    @if($tcModal)
    <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);"
         wire:click="closeTcModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]"
             wire:click.stop>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-800">{{ $editTcId ? 'Edit Transfer Certificate' : 'Issue Transfer Certificate' }}</h2>
                <button wire:click="closeTcModal" class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <div class="px-6 py-5 overflow-y-auto space-y-4">

                {{-- Student --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                    <select wire:model="tc_student_id"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none">
                        <option value="">Select student…</option>
                        @foreach($students as $s)
                            <option value="{{ $s['id'] }}">{{ $s['full_name'] }} ({{ $s['admission_no'] }})</option>
                        @endforeach
                    </select>
                    @error('tc_student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Application Date <span class="text-red-500">*</span></label>
                        <input wire:model="application_date" type="date"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                        @error('application_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Issue Date <span class="text-red-500">*</span></label>
                        <input wire:model="tc_issue_date" type="date"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                        @error('tc_issue_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Book No</label>
                        <input wire:model="book_no" type="text"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nationality</label>
                        <input wire:model="nationality" type="text"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Class Studied</label>
                        <input wire:model="last_class_studied" type="text"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam Last Taken</label>
                        <input wire:model="exam_last_taken" type="text"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Whether Failed</label>
                        <select wire:model="whether_failed"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none">
                            <option>No</option>
                            <option>Once</option>
                            <option>Twice</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qualified for Promotion</label>
                        <select wire:model="qualified_for_promotion"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none">
                            <option>Yes</option>
                            <option>No</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">General Conduct <span class="text-red-500">*</span></label>
                        <select wire:model="general_conduct"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none">
                            <option>Excellent</option>
                            <option>Good</option>
                            <option>Satisfactory</option>
                            <option>Poor</option>
                        </select>
                        @error('general_conduct') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Working Days</label>
                        <input wire:model="total_working_days" type="number" min="0"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Days Present</label>
                        <input wire:model="days_present" type="number" min="0"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fees Paid Upto</label>
                        <input wire:model="fees_paid_upto" type="text"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Concession</label>
                        <input wire:model="fee_concession" type="text"
                            class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Leaving</label>
                    <input wire:model="reason_for_leaving" type="text" maxlength="255"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none" />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea wire:model="tc_remarks" rows="2"
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-400 focus:border-emerald-400 outline-none resize-none"></textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input wire:model="is_sc_st" type="checkbox" id="is_sc_st"
                        class="w-4 h-4 rounded text-emerald-600 border-gray-300 focus:ring-emerald-400" />
                    <label for="is_sc_st" class="text-sm text-gray-700">Belongs to SC/ST category</label>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-3 flex-shrink-0">
                <button wire:click="closeTcModal"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="saveTc"
                    class="px-5 py-2 text-sm font-semibold bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors">
                    {{ $editTcId ? 'Update TC' : 'Issue TC' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══ VIEW MODAL ═══ --}}
    @if($showViewModal && $viewRecord)
    <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);"
         wire:click="closeViewModal">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col max-h-[90vh]"
             wire:click.stop>

            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-shrink-0">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $viewType === 'tc' ? 'Transfer Certificate' : ucfirst($viewRecord->type ?? '') . ' Certificate' }}
                </h2>
                <button wire:click="closeViewModal" class="p-2 text-gray-400 hover:bg-gray-100 rounded-lg transition-colors">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <div class="px-6 py-5 overflow-y-auto space-y-3">
                @if($viewType === 'tc')
                    <div class="bg-red-50 border border-red-100 rounded-xl px-4 py-2 text-center">
                        <p class="text-xs text-gray-500">TC Number</p>
                        <p class="text-lg font-bold font-mono text-red-700">{{ $viewRecord->tc_no ?? '—' }}</p>
                    </div>
                    <dl class="divide-y divide-gray-100 text-sm">
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Student</dt><dd class="font-medium text-gray-800">{{ $viewRecord->student?->full_name ?? '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Admission No</dt><dd class="text-gray-700">{{ $viewRecord->student?->admission_no ?? '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Last Class</dt><dd class="text-gray-700">{{ $viewRecord->last_class_studied ?? '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">General Conduct</dt><dd class="text-gray-700">{{ $viewRecord->general_conduct }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Working Days</dt><dd class="text-gray-700">{{ $viewRecord->total_working_days }} / Present: {{ $viewRecord->days_present }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Application Date</dt><dd class="text-gray-700">{{ $viewRecord->application_date?->format('d M Y') ?? '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Issue Date</dt><dd class="text-gray-700">{{ $viewRecord->issue_date?->format('d M Y') ?? '—' }}</dd></div>
                        @if($viewRecord->reason_for_leaving)
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Reason</dt><dd class="text-gray-700 text-right max-w-[60%]">{{ $viewRecord->reason_for_leaving }}</dd></div>
                        @endif
                    </dl>
                @else
                    <div class="bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-2 text-center">
                        <p class="text-xs text-gray-500">Certificate Number</p>
                        <p class="text-lg font-bold font-mono text-emerald-700">{{ $viewRecord->certificate_no ?? '—' }}</p>
                    </div>
                    <dl class="divide-y divide-gray-100 text-sm">
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Student</dt><dd class="font-medium text-gray-800">{{ $viewRecord->student?->full_name ?? '—' }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Type</dt><dd class="text-gray-700">{{ ucfirst($viewRecord->type) }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Event</dt><dd class="text-gray-700">{{ $viewRecord->event_name }}</dd></div>
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Issued By</dt><dd class="text-gray-700">{{ $viewRecord->issued_by }}</dd></div>
                        @if($viewRecord->issued_by_designation)
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Designation</dt><dd class="text-gray-700">{{ $viewRecord->issued_by_designation }}</dd></div>
                        @endif
                        <div class="flex justify-between py-2"><dt class="text-gray-500">Issue Date</dt><dd class="text-gray-700">{{ $viewRecord->issued_date?->format('d M Y') ?? '—' }}</dd></div>
                        @if($viewRecord->description)
                        <div class="py-2"><dt class="text-gray-500 mb-1">Description</dt><dd class="text-gray-700 text-xs">{{ $viewRecord->description }}</dd></div>
                        @endif
                    </dl>
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end flex-shrink-0">
                <button wire:click="closeViewModal"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
