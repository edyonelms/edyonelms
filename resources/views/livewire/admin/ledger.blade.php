<div class="min-h-screen bg-gray-50">

    {{-- ─── Header ─────────────────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Ledger</h1>
                <p class="text-sm text-gray-500 mt-0.5">Track every credit and expense — fees in, salaries out.</p>
            </div>

            {{-- Net balance pill --}}
            <div class="flex items-center gap-3">
                <div class="px-5 py-3 rounded-xl border {{ $netBalance >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
                    <p class="text-[11px] uppercase tracking-wide font-semibold {{ $netBalance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Net Balance</p>
                    <p class="text-2xl font-bold {{ $netBalance >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        ₹{{ number_format($netBalance, 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-4 sm:p-6 space-y-5">

        @if (session('ledger_msg'))
            <div class="rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-2.5">
                {{ session('ledger_msg') }}
            </div>
        @endif

        {{-- ─── Summary cards ─────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Credit (period)</p>
                <p class="text-xl font-bold text-gray-900 mt-1">₹{{ number_format($periodCredit, 2) }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Fees collected + manual credits</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">Expense (period)</p>
                <p class="text-xl font-bold text-gray-900 mt-1">₹{{ number_format($periodExpense, 2) }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Salaries + manual expenses</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Closing Balance</p>
                <p class="text-xl font-bold {{ $closingBalance >= 0 ? 'text-gray-900' : 'text-red-600' }} mt-1">₹{{ number_format($closingBalance, 2) }}</p>
                <p class="text-[11px] text-gray-400 mt-0.5">Opening ₹{{ number_format($openingBalance, 2) }}</p>
            </div>
        </div>

        {{-- ─── Toolbar ──────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex flex-col xl:flex-row xl:items-end gap-3 xl:gap-4">

                {{-- Date range --}}
                <div class="flex flex-wrap items-end gap-3">
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 mb-1">From</label>
                        <input type="date" wire:model.live="startDate" max="{{ $endDate }}"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 mb-1">To</label>
                        <input type="date" wire:model.live="endDate" min="{{ $startDate }}"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-500 mb-1">Month</label>
                        <input type="month" wire:change="setMonth($event.target.value)"
                            class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <button wire:click="thisMonth"
                        class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-200 rounded-md hover:bg-gray-200">
                        This Month
                    </button>
                </div>

                <div class="flex-1"></div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2">
                    <button wire:click="openCredit"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-emerald-600 rounded-md hover:bg-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Add Credit
                    </button>
                    <button wire:click="openExpense"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                        Add Expense
                    </button>
                    <a href="{{ route('admin.ledger.statement', ['organization' => auth()->user()->organization_id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                        target="_blank"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>

        {{-- ─── Statement table ──────────────────────────────────────────── --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr class="text-left text-[11px] uppercase tracking-wide text-gray-500">
                            <th class="px-4 py-3 font-semibold">Date</th>
                            <th class="px-4 py-3 font-semibold">Particulars</th>
                            <th class="px-4 py-3 font-semibold">By</th>
                            <th class="px-4 py-3 font-semibold text-right">Credit</th>
                            <th class="px-4 py-3 font-semibold text-right">Expense</th>
                            <th class="px-4 py-3 font-semibold text-right">Balance</th>
                            <th class="px-4 py-3 font-semibold text-center">·</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($entries as $row)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $row['date']->format('d M Y') }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-gray-900">{{ $row['reason'] }}</p>
                                    <span class="inline-block mt-0.5 text-[10px] font-semibold px-1.5 py-0.5 rounded
                                        {{ $row['source'] === 'Salary' ? 'bg-orange-50 text-orange-600' : ($row['source'] === 'Manual' ? 'bg-purple-50 text-purple-600' : 'bg-blue-50 text-blue-600') }}">
                                        {{ $row['source'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $row['party'] }}</td>
                                <td class="px-4 py-3 text-right font-medium text-emerald-600">
                                    {{ $row['type'] === 'credit' ? '₹' . number_format($row['amount'], 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-medium text-red-600">
                                    {{ $row['type'] === 'expense' ? '₹' . number_format($row['amount'], 2) : '—' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold {{ $row['balance'] >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                                    ₹{{ number_format($row['balance'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if (!empty($row['manual_id']))
                                        <button wire:click="confirmDelete({{ $row['manual_id'] }})" title="Delete entry"
                                            class="p-1.5 rounded-md border border-gray-200 text-gray-400 hover:bg-red-50 hover:text-red-600 hover:border-red-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                                    No transactions in this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($entries->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">
                    {{ $entries->links('livewire::tailwind') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ─── Manual entry modal ────────────────────────────────────────────── --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40" wire:key="ledger-modal">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold {{ $modalType === 'expense' ? 'text-red-700' : 'text-emerald-700' }}">
                        Add {{ $modalType === 'expense' ? 'Expense' : 'Credit' }}
                    </h3>
                    <button wire:click="closeModal" class="w-8 h-8 flex items-center justify-center rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date <span class="text-red-500">*</span></label>
                            <input type="date" wire:model.defer="mDate" class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm">
                            @error('mDate')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Amount (₹) <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" min="0" wire:model.defer="mAmount" placeholder="0.00"
                                class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm">
                            @error('mAmount')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">By</label>
                        <input type="text" wire:model.defer="mParty" placeholder="Person / source name"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm">
                        @error('mParty')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Reason <span class="text-red-500">*</span></label>
                        <textarea wire:model.defer="mReason" rows="3" placeholder="What is this for?"
                            class="w-full px-3 py-2.5 border border-gray-300 rounded-md text-sm"></textarea>
                        @error('mReason')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-100">
                    <button wire:click="closeModal" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
                    <button wire:click="saveManual" wire:loading.attr="disabled" wire:target="saveManual"
                        class="px-4 py-2 text-sm font-semibold text-white rounded-md {{ $modalType === 'expense' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                        <span wire:loading.remove wire:target="saveManual">Save {{ $modalType === 'expense' ? 'Expense' : 'Credit' }}</span>
                        <span wire:loading wire:target="saveManual">Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ─── Delete confirm ────────────────────────────────────────────────── --}}
    @if ($showDeleteConfirm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6">
                <h3 class="text-base font-semibold text-gray-900">Delete this entry?</h3>
                <p class="text-sm text-gray-500 mt-1">This removes the manual ledger entry. Automatic fee/salary rows can't be deleted here.</p>
                <div class="flex items-center justify-end gap-2 mt-5">
                    <button wire:click="cancelDelete" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200">Cancel</button>
                    <button wire:click="deleteManual" class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-md hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
