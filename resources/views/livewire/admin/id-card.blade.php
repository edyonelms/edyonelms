<div class="p-4">

    {{-- ══════════ HEADER (admit-card style) ══════════ --}}
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-sm border mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">ID Card Management</h2>
            <p class="text-gray-600 mt-1">Generate &amp; manage {{ $cardType }} identity cards</p>
        </div>
        <div class="flex gap-3">
            <button wire:click="openBulkGenerate"
                class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <x-icon name="document-duplicate" class="h-5 w-5" />
                Bulk Generate
            </button>
            <button wire:click="addCard"
                class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                <x-icon name="plus-circle" class="h-5 w-5" />
                Add ID Card
            </button>
        </div>
    </div>

    {{-- ══════════ ANALYTICS (per tab) ══════════ --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center shadow-sm">
            <p class="text-3xl font-bold text-gray-800">{{ $this->analytics['total'] }}</p>
            <p class="text-xs uppercase tracking-wide text-gray-400 mt-1">Total {{ ucfirst($cardType) }}s</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center shadow-sm">
            <p class="text-3xl font-bold text-emerald-600">{{ $this->analytics['issued'] }}</p>
            <p class="text-xs uppercase tracking-wide text-emerald-400 mt-1">Issued</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-5 text-center shadow-sm">
            <p class="text-3xl font-bold text-amber-600">{{ $this->analytics['remaining'] }}</p>
            <p class="text-xs uppercase tracking-wide text-amber-400 mt-1">Remaining</p>
        </div>
    </div>

    {{-- ══════════ TABS ══════════ --}}
    <div class="bg-white rounded-xl shadow-sm border mb-6">
        <div class="flex border-b">
            <button wire:click="switchCardType('student')"
                class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors {{ $cardType === 'student' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Students
            </button>
            <button wire:click="switchCardType('teacher')"
                class="px-6 py-3 text-sm font-semibold border-b-2 transition-colors {{ $cardType === 'teacher' ? 'border-purple-600 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Teachers
            </button>
        </div>

        {{-- Filters --}}
        <div class="p-4 flex flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[220px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search name, card no{{ $cardType === 'student' ? ', admission' : ', employee id' }}…"
                    class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            @if ($cardType === 'student')
                <select wire:model.live="standardFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white">
                    <option value="">All Classes</option>
                    @foreach ($standards as $std)<option value="{{ $std->id }}">{{ $std->name }}</option>@endforeach
                </select>
                <select wire:model.live="sectionFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white">
                    <option value="">All Sections</option>
                    @foreach ($sections as $sec)<option value="{{ $sec->id }}">{{ $sec->name }}</option>@endforeach
                </select>
            @endif

            <select wire:model.live="statusFilter" class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>

            <select wire:model.live="perPage" class="text-sm border border-gray-300 rounded-lg px-3 py-2 bg-white">
                <option value="50">50 / page</option>
                <option value="100">100 / page</option>
                <option value="200">200 / page</option>
            </select>

            @if ($search || $standardFilter || $sectionFilter || $statusFilter)
                <button wire:click="resetFilters" class="text-sm text-red-600 border border-red-200 bg-red-50 hover:bg-red-100 rounded-lg px-3 py-2">Clear</button>
            @endif

            <span class="ml-auto text-sm text-gray-500">Total: {{ $cards->total() }}</span>
        </div>
    </div>

    {{-- ══════════ TABLE ══════════ --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left w-12">#</th>
                        <th class="px-4 py-3 text-left">{{ ucfirst($cardType) }}</th>
                        <th class="px-4 py-3 text-left">Card No.</th>
                        <th class="px-4 py-3 text-center w-28">Expiry</th>
                        <th class="px-4 py-3 text-center w-24">Status</th>
                        <th class="px-4 py-3 text-center w-44">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($cards as $i => $card)
                        @php
                            if ($cardType === 'student') {
                                $person = $card->studentDetail;
                                $name   = $person?->full_name ?? ($person?->user?->name ?? '—');
                                $img    = $person?->user?->image;
                                $ident  = $person?->admission_no;
                            } else {
                                $person = $card->teacherDetail;
                                $name   = $person?->user?->name ?? '—';
                                $img    = $person?->user?->image;
                                $ident  = $person?->employee_id;
                            }
                        @endphp
                        <tr wire:key="card-{{ $cardType }}-{{ $card->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-400">{{ $cards->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if ($img)
                                        <img src="{{ $img }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xs font-bold">{{ strtoupper(substr($name, 0, 1)) }}</div>
                                    @endif
                                    <div class="min-w-0">
                                        <p class="font-medium text-gray-800 truncate">{{ $name }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $ident ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-mono text-gray-600">{{ $card->card_number }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $card->expiry_date?->format('d M Y') ?? '—' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $card->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $card->status }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="showCard({{ $card->id }})" class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600" title="View">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                    </button>
                                    <a href="{{ route('admin.id-card.print', ['organization' => auth()->user()->organization_id, 'type' => $cardType, 'id' => $card->id]) }}" target="_blank"
                                        class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600" title="Download / Print">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                    </a>
                                    <button wire:click="editCard({{ $card->id }})" class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-amber-50 hover:text-amber-600" title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <button wire:click="confirmDelete({{ $card->id }})" class="p-1.5 rounded-md border border-red-200 text-red-500 hover:bg-red-50" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-12 text-center text-gray-400">No ID cards found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t bg-gray-50">{{ $cards->links() }}</div>
    </div>

    {{-- ══════════ VIEW CARD MODAL ══════════ --}}
    @if ($showViewModal && $viewCard)
        <div class="fixed inset-0 flex justify-center bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl my-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ ucfirst($cardType) }} ID Card</h3>
                        <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="h-6 w-6" /></button>
                    </div>
                    @php
                        if ($cardType === 'student') {
                            $person = $viewCard->studentDetail;
                            $personName = $person?->full_name ?? ($person?->user?->name ?? 'N/A');
                            $personImage = $person?->user?->image;
                            $identifier = $person?->admission_no;
                        } else {
                            $person = $viewCard->teacherDetail;
                            $personName = $person?->user?->name ?? 'N/A';
                            $personImage = $person?->user?->image;
                            $identifier = $person?->employee_id;
                        }
                    @endphp
                    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg shadow-lg p-6 border-2 border-purple-200">
                        <div class="text-center border-b-2 border-purple-300 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-purple-900">{{ $viewCard->organization->name }}</h2>
                            <p class="text-sm text-gray-700 mt-1 font-medium">{{ ucfirst($cardType) }} Identification Card</p>
                        </div>
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-1 flex flex-col items-center">
                                @if ($personImage)
                                    <img src="{{ $personImage }}" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                                @else
                                    <div class="w-32 h-32 rounded-lg bg-purple-200 flex items-center justify-center border-4 border-white shadow-lg">
                                        <span class="text-purple-700 font-bold text-4xl">{{ substr($personName, 0, 1) }}</span>
                                    </div>
                                @endif
                                @if ($viewCard->qr_code)
                                    <div class="mt-4 bg-white p-2 rounded-lg shadow-md">
                                        <img src="data:image/png;base64,{{ $viewCard->qr_code }}" class="w-24 h-24">
                                        <p class="text-xs text-center text-gray-600 mt-1">Scan to verify</p>
                                    </div>
                                @endif
                            </div>
                            <div class="col-span-2 space-y-3">
                                <div class="bg-white rounded-lg p-3 shadow-sm">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Full Name</label>
                                    <p class="text-lg font-bold text-gray-900">{{ $personName }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Card Number</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $viewCard->card_number }}</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">{{ $cardType === 'student' ? 'Admission No' : 'Employee ID' }}</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $identifier ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                @if ($cardType === 'student')
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-white rounded-lg p-3 shadow-sm">
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Class</label>
                                            <p class="text-sm font-medium text-gray-900">{{ $person?->standard?->name ?? 'N/A' }} {{ $person?->section?->name ? '- ' . $person->section->name : '' }}</p>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 shadow-sm">
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Roll No</label>
                                            <p class="text-sm font-medium text-gray-900">{{ $person?->roll_no ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Issue Date</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $viewCard->issue_date?->format('d M Y') ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Expiry Date</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $viewCard->expiry_date?->format('d M Y') ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('admin.id-card.print', ['organization' => auth()->user()->organization_id, 'type' => $cardType, 'id' => $viewCard->id]) }}" target="_blank"
                            class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Download / Print</a>
                        <button wire:click="closeViewModal" class="px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ ADD / EDIT MODAL ══════════ --}}
    @if ($showEditModal)
        <div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">{{ $cardId ? 'Edit ID Card' : 'Add New ID Card' }} ({{ ucfirst($cardType) }})</h3>
                        <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="h-6 w-6" /></button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ ucfirst($cardType) }} *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" /></div>
                                <input type="text" wire:model.live.debounce.300ms="personSearch"
                                    placeholder="Search by name or {{ $cardType === 'student' ? 'admission' : 'employee' }} number…"
                                    class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" autocomplete="off">
                            </div>
                            @if (strlen($personSearch) >= 2 && count($this->availablePersons) > 0)
                                <div class="mt-2 border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto bg-white z-50">
                                    @foreach ($this->availablePersons as $p)
                                        <div wire:click="selectPerson({{ $p['id'] }})" class="px-4 py-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                            <div class="font-medium text-gray-900">{{ $p['text'] }}</div>
                                            <div class="text-sm text-gray-600 mt-1">{{ $p['info'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            @if ($personId && strlen($personSearch) < 2)
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="font-medium text-green-800">Selected:</div>
                                    <div class="text-sm text-green-700">{{ $personSearch }}</div>
                                </div>
                            @endif
                            @error('personId')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number *</label>
                            <input type="text" wire:model="cardNumber" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Enter card number">
                            @error('cardNumber')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                            <input type="date" wire:model="expiryDate" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @error('expiryDate')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select wire:model="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                            @error('status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                        <button wire:click="closeEditModal" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                        <button wire:click="saveCard" class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">{{ $cardId ? 'Update' : 'Create' }}</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ BULK GENERATE MODAL ══════════ --}}
    @if ($showBulkGenerateModal)
        <div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Bulk Generate {{ ucfirst($cardType) }} ID Cards</h3>
                        <button wire:click="closeBulkModal" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="h-6 w-6" /></button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                            <input type="date" wire:model="bulkExpiryDate" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @error('bulkExpiryDate')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="information-circle" class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Note</h4>
                                    <p class="text-sm text-blue-700 mt-1">Generates ID cards for all {{ $cardType }}s who don't have an active card. Each gets a unique number, QR code and the expiry date above.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                        <button wire:click="closeBulkModal" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                        <button wire:click="bulkGenerateCards" wire:loading.attr="disabled" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-60">
                            <span wire:loading.remove wire:target="bulkGenerateCards">Generate Cards</span>
                            <span wire:loading wire:target="bulkGenerateCards">Generating…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ DELETE MODAL ══════════ --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 flex justify-center bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-auto my-auto">
                <div class="p-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <x-icon name="exclamation-triangle" class="h-6 w-6 text-red-600" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete ID Card</h3>
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this ID card? This action cannot be undone.</p>
                    </div>
                    <div class="flex justify-center gap-3">
                        <button wire:click="closeDeleteModal" class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Cancel</button>
                        <button wire:click="deleteCard" class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
