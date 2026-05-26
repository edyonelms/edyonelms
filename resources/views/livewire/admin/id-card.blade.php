<div class="min-h-screen bg-gray-50">

    {{-- ══════════════ HEADER (white, sticky) ══════════════ --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">ID Card</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Generate &amp; manage {{ $cardType }} identity cards</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                        <span class="pr-4">Total {{ ucfirst($cardType) }}s: <strong class="text-gray-800">{{ $this->analytics['total'] }}</strong></span>
                        <span class="px-4">Issued: <strong class="text-emerald-600">{{ $this->analytics['issued'] }}</strong></span>
                        <span class="pl-4">Remaining: <strong class="text-amber-500">{{ $this->analytics['remaining'] }}</strong></span>
                    </div>
                    <button wire:click="openBulkGenerate" class="inline-flex items-center gap-1.5 px-3 py-2 border border-blue-200 bg-blue-50 text-blue-700 text-sm font-semibold rounded-lg hover:bg-blue-100 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        Bulk Generate
                    </button>
                    <button wire:click="addCard" class="inline-flex items-center gap-1.5 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        <span class="hidden sm:inline">Add ID Card</span><span class="sm:hidden">Add</span>
                    </button>
                </div>
            </div>

            {{-- mobile analytics --}}
            <div class="flex lg:hidden items-center gap-3 sm:gap-4 text-xs text-gray-500 mt-3 flex-wrap">
                <span>Total: <strong class="text-gray-800">{{ $this->analytics['total'] }}</strong></span>
                <span>Issued: <strong class="text-emerald-600">{{ $this->analytics['issued'] }}</strong></span>
                <span>Remaining: <strong class="text-amber-500">{{ $this->analytics['remaining'] }}</strong></span>
            </div>
        </div>

        {{-- Tabs (rules-regulation style) --}}
        <div class="border-t border-gray-200 px-4 sm:px-6">
            <nav class="flex gap-1">
                <button wire:click="switchCardType('student')"
                    class="py-3.5 px-5 text-sm font-semibold border-b-2 transition-colors {{ $cardType === 'student' ? 'border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Students
                </button>
                <button wire:click="switchCardType('teacher')"
                    class="py-3.5 px-5 text-sm font-semibold border-b-2 transition-colors {{ $cardType === 'teacher' ? 'border-blue-500 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Teachers
                </button>
            </nav>
        </div>

        {{-- Filter bar --}}
        <div class="border-t border-gray-200 bg-gray-50 px-4 sm:px-6 py-3">
            <div class="flex flex-wrap items-center gap-3">
                <div class="flex items-center gap-1.5 text-sm font-semibold text-gray-700">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" /></svg>
                    Filter by:
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Name, card no{{ $cardType === 'student' ? ', admission' : ', employee id' }}…"
                    class="text-xs bg-white border border-gray-200 rounded-md px-3 py-1.5 text-gray-700 w-56 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                @if ($cardType === 'student')
                    <select wire:model.live="standardFilter" class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700">
                        <option value="">All Classes</option>
                        @foreach ($standards as $std)<option value="{{ $std->id }}">{{ $std->name }}</option>@endforeach
                    </select>
                    <select wire:model.live="sectionFilter" @disabled($sections->isEmpty()) class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700 disabled:opacity-50">
                        <option value="">All Sections</option>
                        @foreach ($sections as $sec)<option value="{{ $sec->id }}">{{ $sec->name }}</option>@endforeach
                    </select>
                @endif
                <select wire:model.live="statusFilter" class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select wire:model.live="perPage" class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700">
                    <option value="50">50 / page</option>
                    <option value="100">100 / page</option>
                    <option value="200">200 / page</option>
                </select>
                @if ($search || $standardFilter || $sectionFilter || $statusFilter)
                    <button wire:click="resetFilters" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-red-600 bg-white border border-red-200 rounded-md hover:bg-red-50">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        Clear
                    </button>
                @endif
                <span class="ml-auto text-xs text-gray-500">Total: <strong class="text-gray-700">{{ $cards->total() }}</strong> card(s)</span>
            </div>
        </div>
    </div>

    {{-- ══════════════ TABLE ══════════════ --}}
    <div class="p-4 sm:p-6">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">S.No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ ucfirst($cardType) }}</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Card No.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Expiry</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
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
                            <tr wire:key="card-{{ $cardType }}-{{ $card->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-500 tabular-nums">{{ $cards->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($img)
                                            <img src="{{ $img }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                                        @else
                                            <div class="w-9 h-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">{{ strtoupper(substr($name, 0, 1)) }}</div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-semibold text-sm text-gray-900 truncate">{{ $name }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $ident ?? '—' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $card->card_number }}</td>
                                <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $card->expiry_date?->format('d M Y') ?? '—' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-[11px] font-semibold px-2 py-0.5 rounded-full uppercase tracking-wide {{ $card->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $card->status }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="showCard({{ $card->id }})" class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                        <a href="{{ route('admin.id-card.print', ['organization' => auth()->user()->organization_id, 'type' => $cardType, 'id' => $card->id]) }}" target="_blank"
                                            class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-200" title="Download / Print">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>
                                        <button wire:click="editCard({{ $card->id }})" class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-amber-50 hover:text-amber-600 hover:border-amber-200" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <button wire:click="confirmDelete({{ $card->id }})" class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-200" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <div class="w-12 h-12 mx-auto mb-3 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0z" /></svg>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-800">No ID cards found</p>
                                    <button wire:click="addCard" class="mt-2 text-xs text-blue-600 hover:underline">Add an ID card</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($cards->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $cards->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ══════════════ VIEW CARD MODAL ══════════════ --}}
    @if ($showViewModal && $viewCard)
        <div class="fixed inset-0 z-[9999] overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeViewModal"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-2xl bg-white shadow-2xl flex flex-col" wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-base font-bold text-gray-800">{{ ucfirst($cardType) }} ID Card</h3>
                    <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="h-5 w-5" /></button>
                </div>
                <div class="flex-1 overflow-y-auto px-6 py-5">
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
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-lg p-6 border-2 border-blue-200">
                        <div class="text-center border-b-2 border-blue-300 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-blue-900">{{ $viewCard->organization->name }}</h2>
                            <p class="text-sm text-gray-700 mt-1 font-medium">{{ ucfirst($cardType) }} Identification Card</p>
                        </div>
                        <div class="grid grid-cols-3 gap-6">
                            <div class="col-span-1 flex flex-col items-center">
                                @if ($personImage)
                                    <img src="{{ $personImage }}" class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                                @else
                                    <div class="w-32 h-32 rounded-lg bg-blue-200 flex items-center justify-center border-4 border-white shadow-lg">
                                        <span class="text-blue-700 font-bold text-4xl">{{ substr($personName, 0, 1) }}</span>
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
                </div>
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 flex-shrink-0">
                    <a href="{{ route('admin.id-card.print', ['organization' => auth()->user()->organization_id, 'type' => $cardType, 'id' => $viewCard->id]) }}" target="_blank"
                        class="px-5 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">Download / Print</a>
                    <button wire:click="closeViewModal" class="px-5 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Close</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════ ADD / EDIT SLIDE-IN PANEL ══════════════ --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-[9999] overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeEditModal"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-md bg-white shadow-2xl flex flex-col" wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-base font-bold text-gray-800">{{ $cardId ? 'Edit ID Card' : 'Add New ID Card' }} <span class="text-gray-400 font-normal">({{ ucfirst($cardType) }})</span></h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="h-5 w-5" /></button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">{{ ucfirst($cardType) }} <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" /></div>
                            <input type="text" wire:model.live.debounce.300ms="personSearch"
                                placeholder="Search by name or {{ $cardType === 'student' ? 'admission' : 'employee' }} number…"
                                class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off">
                        </div>
                        @if (strlen($personSearch) >= 2 && count($this->availablePersons) > 0)
                            <div class="mt-2 border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto bg-white">
                                @foreach ($this->availablePersons as $p)
                                    <div wire:click="selectPerson({{ $p['id'] }})" class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                        <div class="font-medium text-sm text-gray-900">{{ $p['text'] }}</div>
                                        <div class="text-xs text-gray-600 mt-1">{{ $p['info'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @if ($personId && strlen($personSearch) < 2)
                            <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="font-medium text-sm text-green-800">Selected:</div>
                                <div class="text-sm text-green-700">{{ $personSearch }}</div>
                            </div>
                        @endif
                        @error('personId')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Card Number <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="cardNumber" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter card number">
                        @error('cardNumber')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="expiryDate" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('expiryDate')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span class="text-red-500">*</span></label>
                        <select wire:model="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 flex-shrink-0">
                    <button wire:click="closeEditModal" class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="saveCard" class="px-5 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-sm transition">{{ $cardId ? 'Update' : 'Create' }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════ BULK GENERATE SLIDE-IN PANEL ══════════════ --}}
    @if ($showBulkGenerateModal)
        <div class="fixed inset-0 z-[9999] overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeBulkModal"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-md bg-white shadow-2xl flex flex-col" wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
                    <h3 class="text-base font-bold text-gray-800">Bulk Generate {{ ucfirst($cardType) }} ID Cards</h3>
                    <button wire:click="closeBulkModal" class="text-gray-400 hover:text-gray-600"><x-icon name="x-mark" class="h-5 w-5" /></button>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Expiry Date <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="bulkExpiryDate" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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

                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end gap-2 flex-shrink-0">
                    <button wire:click="closeBulkModal" class="px-4 py-2 text-sm border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="bulkGenerateCards" wire:loading.attr="disabled" class="px-5 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold shadow-sm transition disabled:opacity-60">
                        <span wire:loading.remove wire:target="bulkGenerateCards">Generate Cards</span>
                        <span wire:loading wire:target="bulkGenerateCards">Generating…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════ DELETE MODAL ══════════════ --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-[10000] flex items-center justify-center px-4" style="background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" wire:click.stop>
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-red-100 mb-4">
                    <x-icon name="exclamation-triangle" class="h-7 w-7 text-red-500" />
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Delete ID Card?</h3>
                <p class="text-sm text-gray-500 mb-5">This action cannot be undone.</p>
                <div class="flex justify-center gap-3">
                    <button wire:click="closeDeleteModal" class="px-4 py-2 text-sm border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="deleteCard" class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow transition">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
