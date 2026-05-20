{{-- Transport Fee Summary tab — shared by Admin & Accounts Transport.
     Requires HandlesTransportFees trait on the host component. --}}
@php $summary = $this->feeSummary(); @endphp

@if (!$feeStudentId)
    {{-- Student picker --}}
    <div class="max-w-xl mx-auto bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-base font-semibold text-gray-900 mb-1">Student Transport Fee</h3>
        <p class="text-sm text-gray-500 mb-4">Search a student (using transport) to view their fee summary, receipts &amp; record payments.</p>
        <div class="relative">
            <input wire:model.live.debounce.300ms="feeStudentSearch" type="text" placeholder="Search by name or admission number…"
                class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" autocomplete="off">
            @if (strlen($feeStudentSearch) >= 2)
                @php $results = $this->feeStudentResults(); @endphp
                <div class="mt-2 border border-gray-200 rounded-lg shadow-sm max-h-72 overflow-y-auto divide-y divide-gray-100">
                    @forelse ($results as $r)
                        <div wire:click="selectFeeStudent({{ $r->id }})" class="px-4 py-3 hover:bg-blue-50 cursor-pointer">
                            <p class="text-sm font-medium text-gray-800">{{ $r->full_name }}</p>
                            <p class="text-xs text-gray-400">{{ $r->admission_no }} · {{ $r->standard->name ?? '' }}{{ $r->section ? '-' . $r->section->name : '' }}</p>
                        </div>
                    @empty
                        <div class="px-4 py-3 text-sm text-gray-400">No transport students match.</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
@elseif ($summary)
    <div class="space-y-5">
        {{-- Student header --}}
        <div class="bg-white rounded-xl border border-gray-200 p-5 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 min-w-0">
                @if ($summary['student']->user?->image)
                    <img src="{{ $summary['student']->user->image }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow flex-shrink-0">
                @else
                    <div class="w-14 h-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 text-lg font-bold flex-shrink-0">{{ strtoupper(substr($summary['student']->full_name ?? 'S', 0, 1)) }}</div>
                @endif
                <div class="min-w-0">
                    <h3 class="text-lg font-bold text-gray-900 truncate">{{ $summary['student']->full_name }}</h3>
                    <p class="text-sm text-gray-500 truncate">
                        {{ $summary['student']->admission_no }} · {{ $summary['student']->standard->name ?? '' }}{{ $summary['student']->section ? '-' . $summary['student']->section->name : '' }}
                        · Route: {{ $summary['route']->route_name ?? '—' }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button wire:click="openPaymentPanel"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Add Payment
                </button>
                <button wire:click="clearFeeStudent" class="px-3 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">Change</button>
            </div>
        </div>

        {{-- Fee figures --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Monthly</p>
                <p class="text-xl font-bold text-gray-800 mt-1">₹{{ number_format($summary['monthly'], 0) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Annual (×11)</p>
                <p class="text-xl font-bold text-blue-600 mt-1">₹{{ number_format($summary['annual'], 0) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Paid</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">₹{{ number_format($summary['paid'], 0) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-400 uppercase tracking-wide">Remaining</p>
                <p class="text-xl font-bold {{ $summary['remaining'] > 0 ? 'text-red-600' : 'text-gray-400' }} mt-1">₹{{ number_format($summary['remaining'], 0) }}</p>
            </div>
        </div>

        {{-- Transactions --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <h4 class="text-sm font-semibold text-gray-700">Transactions</h4>
                <span class="text-xs text-gray-400">{{ $summary['payments']->count() }} payment(s)</span>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Receipt</th>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Mode</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-center w-32">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($summary['payments'] as $p)
                        <tr wire:key="pay-{{ $p->id }}" class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $p->receipt_number }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->payment_date?->format('d M Y') }}</td>
                            <td class="px-4 py-3 capitalize text-gray-600">{{ $p->payment_mode }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">₹{{ number_format($p->amount, 0) }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route(request()->routeIs('accounts.*') ? 'accounts.transport.receipt' : 'admin.transport.receipt', ['organization' => auth()->user()->organization_id, 'id' => $p->id]) }}" target="_blank"
                                        class="p-1.5 rounded-md border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600" title="Receipt">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    </a>
                                    <button wire:click="confirmDeletePayment({{ $p->id }})" class="p-1.5 rounded-md border border-red-200 text-red-500 hover:bg-red-50" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-10 text-center text-gray-400">No payments recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endif
