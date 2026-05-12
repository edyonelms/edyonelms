<div class="p-4">

    {{-- Main Tabs --}}
    <div class="flex flex-wrap justify-center items-center mb-6 gap-2">
        <div class="flex flex-wrap gap-2 bg-white p-1 rounded-lg shadow-md">
            @foreach ([
                'fee_structure'  => 'Fee Structure',
                'fee_submission' => 'Fee Submission',
                'view_fee'       => 'View Fee',
                'analytics'      => 'Analytics',
                'payments'       => 'Payments',
                'penalties'      => 'Penalties & Cycle',
            ] as $tabKey => $tabLabel)
                <button
                    class="px-5 py-2 rounded-md text-sm font-medium transition-all {{ $activeTab === $tabKey ? 'bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white shadow-md' : 'text-gray-600 hover:bg-pink-100' }}"
                    wire:click="showTab('{{ $tabKey }}')">
                    {{ $tabLabel }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: FEE STRUCTURE                                            --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'fee_structure')
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-semibold text-gray-800">Fee Structure</h3>
                <button wire:click="openStructureModal()"
                    class="px-4 py-2 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white rounded-lg hover:opacity-90 transition-all text-sm">
                    <i class="fas fa-plus mr-1"></i> Add Fee Structure
                </button>
            </div>

            {{-- Filters --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                <select wire:model.live="filterStructureStandard"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">All Classes</option>
                    @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="filterStructureSection"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">All Sections</option>
                    @foreach ($sections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                    @endforeach
                </select>
                <input type="text" wire:model.live="filterStructureYear" placeholder="Academic Year (e.g. 2025-2026)"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search fee name..."
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Academic Year</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($structures as $s)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-medium">{{ $s->standard->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $s->section->name ?? 'All' }}</td>
                                <td class="px-4 py-3">{{ $s->fee_name }}</td>
                                <td class="px-4 py-3 font-semibold">₹{{ number_format($s->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $s->fee_type === 'academic' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                        {{ ucfirst($s->fee_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">{{ $s->academic_year }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $s->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $s->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <button wire:click="openStructureModal({{ $s->id }})"
                                            class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-300 rounded hover:bg-blue-50 transition-all">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </button>
                                        <button wire:click="deleteStructure({{ $s->id }})"
                                            class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-300 rounded hover:bg-red-50 transition-all">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-400">No fee structures found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $structures->links() }}</div>
        </div>

        {{-- Add/Edit Fee Structure Modal --}}
        @if ($structureModalOpen)
            <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg mx-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-lg font-semibold">{{ $editStructureId ? 'Edit' : 'Add' }} Fee Structure</h4>
                        <button wire:click="$set('structureModalOpen', false)" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                                <select wire:model.live="structureStandardId"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                    <option value="">Select Class</option>
                                    @foreach ($standards as $std)
                                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                                    @endforeach
                                </select>
                                @error('structureStandardId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Section (optional)</label>
                                <select wire:model="structureSectionId"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                    <option value="">All Sections</option>
                                    @foreach ($sections as $sec)
                                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fee Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="feeName" placeholder="e.g. Tuition Fee, Lab Fee"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                            @error('feeName') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₹) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="feeAmount" placeholder="0.00" step="0.01" min="0"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                @error('feeAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type <span class="text-red-500">*</span></label>
                                <select wire:model="structureFeeType"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                    <option value="academic">Academic</option>
                                    <option value="transport">Transport</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="academicYear" placeholder="e.g. 2025-2026"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                            @error('academicYear') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button wire:click="$set('structureModalOpen', false)"
                            class="px-4 py-2 border border-gray-300 text-gray-600 rounded-lg text-sm hover:bg-gray-50">Cancel</button>
                        <button wire:click="saveStructure"
                            class="px-4 py-2 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white rounded-lg text-sm hover:opacity-90">
                            {{ $editStructureId ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: FEE SUBMISSION                                           --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'fee_submission')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Student Selection --}}
            <div class="bg-white rounded-lg shadow-md p-5">
                <h3 class="text-base font-semibold text-gray-800 mb-4">Select Student</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Class</label>
                        <select wire:model.live="submissionStandardId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Select Class</option>
                            @foreach ($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Section (optional)</label>
                        <select wire:model.live="submissionSectionId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">All Sections</option>
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Student</label>
                        <select wire:model.live="selectedStudentId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Select Student</option>
                            @foreach ($students as $stu)
                                <option value="{{ $stu->id }}">
                                    {{ $stu->user->name ?? 'Unknown' }}
                                    @if ($stu->father_name) ({{ $stu->father_name }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Fee Structure for selected class --}}
                @if ($selectedStudentId && count($classStructures))
                    <div class="mt-5">
                        <h4 class="text-xs font-semibold text-gray-700 uppercase mb-2">Class Fee Structure</h4>
                        <div class="space-y-1">
                            @foreach ($classStructures as $cs)
                                <div class="flex justify-between items-center py-1 border-b border-gray-100 text-sm">
                                    <span class="text-gray-700">{{ $cs['fee_name'] }}</span>
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-xs {{ $cs['fee_type'] === 'academic' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                            {{ ucfirst($cs['fee_type']) }}
                                        </span>
                                        <span class="font-semibold text-gray-800">₹{{ number_format($cs['amount'], 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right: Transaction History + Submit Form --}}
            <div class="lg:col-span-2 space-y-5">
                {{-- Submit Payment Form --}}
                @if ($selectedStudentId)
                    <div class="bg-white rounded-lg shadow-md p-5">
                        <h3 class="text-base font-semibold text-gray-800 mb-4">Submit Fee Payment</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Amount (₹) <span class="text-red-500">*</span></label>
                                <input type="number" wire:model="submitAmount" placeholder="Enter amount" step="0.01" min="1"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                @error('submitAmount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Fee Mode <span class="text-red-500">*</span></label>
                                <select wire:model="submitFeeType"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                    <option value="academic">Academic</option>
                                    <option value="transport">Transport</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Payment Mode <span class="text-red-500">*</span></label>
                                <select wire:model="submitPaymentMode"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                    <option value="cash">Cash</option>
                                    <option value="online">Online</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model="submitDate"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                @error('submitDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Submitted By <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="submittedBy" placeholder="Staff name"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                                @error('submittedBy') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Remark</label>
                                <input type="text" wire:model="submitRemark" placeholder="Optional remark"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button wire:click="submitFeePayment"
                                class="px-6 py-2 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white rounded-lg text-sm hover:opacity-90">
                                <i class="fas fa-check mr-1"></i> Submit Payment
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Transaction History --}}
                <div class="bg-white rounded-lg shadow-md p-5">
                    <h3 class="text-base font-semibold text-gray-800 mb-4">Transaction History</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">#</th>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Receipt</th>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Amount</th>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Mode</th>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Fee Type</th>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Date</th>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">By</th>
                                    <th class="px-3 py-2 text-left text-xs text-gray-500 uppercase">Print</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($studentTransactions as $i => $txn)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">{{ $i + 1 }}</td>
                                        <td class="px-3 py-2 font-mono text-xs text-purple-700">{{ $txn['receipt_number'] }}</td>
                                        <td class="px-3 py-2 font-semibold">₹{{ number_format($txn['amount'], 2) }}</td>
                                        <td class="px-3 py-2 capitalize">{{ str_replace('_', ' ', $txn['payment_mode']) }}</td>
                                        <td class="px-3 py-2">
                                            <span class="px-2 py-0.5 rounded text-xs {{ $txn['fee_type'] === 'academic' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                                {{ ucfirst($txn['fee_type']) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2">{{ \Carbon\Carbon::parse($txn['payment_date'])->format('d M Y') }}</td>
                                        <td class="px-3 py-2 text-xs text-gray-600">{{ $txn['submitted_by'] }}</td>
                                        <td class="px-3 py-2">
                                            <a href="{{ route('admin.fee.receipt', ['organization' => auth()->user()->organization_id, 'id' => $txn['id']]) }}" target="_blank"
                                                class="text-xs px-2 py-1 border border-gray-300 rounded hover:bg-gray-100 text-gray-600 inline-block">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-3 py-6 text-center text-gray-400 text-sm">
                                            @if ($selectedStudentId) No transactions found for this student. @else Select a student to view transactions. @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 3: VIEW FEE                                                 --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'view_fee')
        {{-- Sub tabs --}}
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-6">
                @foreach (['by_student' => 'By Student', 'by_class' => 'By Class'] as $key => $label)
                    <button wire:click="setViewSubTab('{{ $key }}')"
                        class="relative whitespace-nowrap py-3 px-1 font-medium text-sm {{ $viewSubTab === $key ? 'text-purple-600' : 'text-gray-500 hover:text-gray-700' }}">
                        {{ $label }}
                        @if ($viewSubTab === $key)
                            <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff]"></div>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- By Student --}}
        @if ($viewSubTab === 'by_student')
            <div class="bg-white rounded-lg shadow-md p-5 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Class</label>
                        <select wire:model.live="viewStudentStandardId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Select Class</option>
                            @foreach ($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Section (optional)</label>
                        <select wire:model.live="viewStudentSectionId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">All Sections</option>
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Student</label>
                        <select wire:model.live="viewStudentId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Select Student</option>
                            @foreach ($students as $stu)
                                <option value="{{ $stu->id }}">{{ $stu->user->name ?? 'Unknown' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if ($viewStudentId)
                    <div class="mt-4 flex justify-end">
                        <button wire:click="loadStudentFeeView"
                            class="px-5 py-2 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white rounded-lg text-sm hover:opacity-90">
                            <i class="fas fa-search mr-1"></i> View Fee Details
                        </button>
                    </div>
                @endif
            </div>

            @if (!empty($studentFeeView))
                @php $sv = $studentFeeView; @endphp
                {{-- Analytics Cards --}}
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                        <p class="text-xs text-blue-600 font-medium uppercase">Total Fee</p>
                        <p class="text-2xl font-bold text-blue-800 mt-1">₹{{ number_format($sv['totalFee'], 2) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg p-4 border border-emerald-200">
                        <p class="text-xs text-emerald-600 font-medium uppercase">Paid</p>
                        <p class="text-2xl font-bold text-emerald-800 mt-1">₹{{ number_format($sv['totalPaid'], 2) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                        <p class="text-xs text-red-600 font-medium uppercase">Remaining</p>
                        <p class="text-2xl font-bold text-red-800 mt-1">₹{{ number_format($sv['remaining'], 2) }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                        <p class="text-xs text-purple-600 font-medium uppercase">Academic Fee</p>
                        <p class="text-2xl font-bold text-purple-800 mt-1">₹{{ number_format($sv['academicTotal'], 2) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Fee Structure Breakdown --}}
                    <div class="bg-white rounded-lg shadow-md p-5">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Fee Structure</h4>
                        <div class="space-y-2">
                            @foreach ($sv['structures'] as $fs)
                                @if ($fs->fee_type === 'transport' && !$sv['hasTransport']) @continue @endif
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                                    <div>
                                        <span class="font-medium text-gray-800">{{ $fs->fee_name }}</span>
                                        <span class="ml-2 px-2 py-0.5 rounded text-xs {{ $fs->fee_type === 'academic' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                            {{ ucfirst($fs->fee_type) }}
                                        </span>
                                    </div>
                                    <span class="font-semibold">₹{{ number_format($fs->amount, 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                        @if ($sv['hasTransport'])
                            <div class="mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-green-700 font-medium">Transport Fee Total</span>
                                    <span class="font-bold text-green-800">₹{{ number_format($sv['transportTotal'], 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-green-600">Transport Paid</span>
                                    <span class="font-medium text-green-700">₹{{ number_format($sv['transportPaid'], 2) }}</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Payment History --}}
                    <div class="bg-white rounded-lg shadow-md p-5">
                        <h4 class="text-sm font-semibold text-gray-800 mb-3">Payment History</h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto">
                            @forelse ($sv['payments'] as $p)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 text-sm">
                                    <div>
                                        <p class="font-medium text-gray-800">₹{{ number_format($p->amount, 2) }}
                                            <span class="text-xs px-1.5 py-0.5 rounded {{ $p->fee_type === 'academic' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                                {{ ucfirst($p->fee_type) }}
                                            </span>
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $p->receipt_number }} &bull; {{ ucfirst(str_replace('_', ' ', $p->payment_mode)) }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($p->payment_date)->format('d M Y') }}</span>
                                </div>
                            @empty
                                <p class="text-center text-gray-400 text-sm py-4">No payments recorded.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        @endif

        {{-- By Class --}}
        @if ($viewSubTab === 'by_class')
            <div class="bg-white rounded-lg shadow-md p-5 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Class <span class="text-red-500">*</span></label>
                        <select wire:model.live="viewClassStandardId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">Select Class</option>
                            @foreach ($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Section (optional)</label>
                        <select wire:model.live="viewClassSectionId"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                            <option value="">All Sections</option>
                            @foreach ($sections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button wire:click="loadClassFeeView"
                            class="w-full px-4 py-2 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white rounded-lg text-sm hover:opacity-90">
                            <i class="fas fa-search mr-1"></i> Load Students
                        </button>
                    </div>
                </div>
            </div>

            @if (!empty($classFeeList))
                <div class="bg-white rounded-lg shadow-md p-5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Adm No.</th>
                                    <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Class</th>
                                    <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Section</th>
                                    <th class="px-4 py-3 text-right text-xs text-gray-500 uppercase">Academic</th>
                                    <th class="px-4 py-3 text-right text-xs text-gray-500 uppercase">Transport</th>
                                    <th class="px-4 py-3 text-right text-xs text-gray-500 uppercase">Total Fee</th>
                                    <th class="px-4 py-3 text-right text-xs text-gray-500 uppercase">Collected</th>
                                    <th class="px-4 py-3 text-center text-xs text-gray-500 uppercase">View</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($classFeeList as $i => $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">{{ $i + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row['name'] }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $row['admission_no'] ?? '-' }}</td>
                                        <td class="px-4 py-3">{{ $row['class'] }}</td>
                                        <td class="px-4 py-3">{{ $row['section'] }}</td>
                                        <td class="px-4 py-3 text-right">₹{{ number_format($row['academicFee'], 2) }}</td>
                                        <td class="px-4 py-3 text-right">₹{{ number_format($row['transportFee'], 2) }}</td>
                                        <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($row['totalFee'], 2) }}</td>
                                        <td class="px-4 py-3 text-right text-emerald-700 font-semibold">₹{{ number_format($row['collected'], 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <button wire:click="$set('viewSubTab', 'by_student'); $set('viewStudentId', '{{ $row['id'] }}')"
                                                class="text-xs px-3 py-1 border border-purple-300 text-purple-600 rounded hover:bg-purple-50 transition-all">
                                                View
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @endif
    @endif

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 4: ANALYTICS                                                --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'analytics')
        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Filter by Class</label>
                    <select wire:model.live="analyticsStandardId"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">All Classes</option>
                        @foreach ($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Filter by Section</label>
                    <select wire:model.live="analyticsSectionId"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                        <option value="">All Sections</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button wire:click="loadAnalytics"
                        class="w-full px-4 py-2 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white rounded-lg text-sm hover:opacity-90">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh Analytics
                    </button>
                </div>
            </div>
        </div>

        {{-- Analytics Cards --}}
        @if (!empty($analyticsData))
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-5 border border-indigo-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-rupee-sign text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-indigo-600 font-medium uppercase">Total Fee</p>
                            <p class="text-xl font-bold text-indigo-800">₹{{ number_format($analyticsData['totalFee'], 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl p-5 border border-emerald-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-emerald-600 font-medium uppercase">Collected</p>
                            <p class="text-xl font-bold text-emerald-800">₹{{ number_format($analyticsData['collected'], 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5 border border-red-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-circle text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-red-600 font-medium uppercase">Remaining</p>
                            <p class="text-xl font-bold text-red-800">₹{{ number_format($analyticsData['remaining'], 0) }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5 border border-green-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-bus text-white text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-green-600 font-medium uppercase">Transport Total</p>
                            <p class="text-xl font-bold text-green-800">₹{{ number_format($analyticsData['transportTotal'], 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Student List --}}
        @if (!empty($analyticsStudentList))
            <div class="bg-white rounded-lg shadow-md p-5">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Student Fee Overview</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">#</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Name</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Adm No.</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Class</th>
                                <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Section</th>
                                <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Fee Come / Total</th>
                                <th class="px-4 py-2 text-center text-xs text-gray-500 uppercase">View</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($analyticsStudentList as $i => $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-medium">{{ $row['name'] }}</td>
                                    <td class="px-4 py-3 text-gray-500">{{ $row['admission_no'] ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $row['class'] }}</td>
                                    <td class="px-4 py-3">{{ $row['section'] }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-emerald-700 font-semibold">₹{{ number_format($row['collected'], 0) }}</span>
                                        <span class="text-gray-400 mx-1">/</span>
                                        <span class="text-gray-700">₹{{ number_format($row['totalFee'], 0) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button wire:click="showTab('view_fee'); $set('viewStudentId', '{{ $row['id'] }}')"
                                            class="text-xs px-3 py-1 border border-purple-300 text-purple-600 rounded hover:bg-purple-50">View</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    @endif

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 5: PAYMENTS                                                 --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'payments')
        {{-- Period Analytics --}}
        @if (!empty($paymentPeriodStats))
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                @foreach ([
                    'today'      => ['Today',      'from-blue-50 to-blue-100',     'border-blue-200',   'text-blue-600',   'text-blue-800'],
                    'yesterday'  => ['Yesterday',  'from-gray-50 to-gray-100',     'border-gray-200',   'text-gray-600',   'text-gray-800'],
                    'this_week'  => ['This Week',  'from-purple-50 to-purple-100', 'border-purple-200', 'text-purple-600', 'text-purple-800'],
                    'this_month' => ['This Month', 'from-emerald-50 to-emerald-100', 'border-emerald-200', 'text-emerald-600', 'text-emerald-800'],
                    'last_month' => ['Last Month', 'from-orange-50 to-orange-100', 'border-orange-200', 'text-orange-600', 'text-orange-800'],
                ] as $key => [$label, $bg, $border, $textColor, $textBold])
                    <div class="bg-gradient-to-br {{ $bg }} rounded-xl p-4 border {{ $border }}">
                        <p class="text-xs {{ $textColor }} font-medium uppercase">{{ $label }}</p>
                        <p class="text-xl font-bold {{ $textBold }} mt-1">₹{{ number_format($paymentPeriodStats[$key] ?? 0, 0) }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-lg shadow-md p-4 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <select wire:model.live="paymentStandardId"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">All Classes</option>
                    @foreach ($standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="paymentSectionId"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">All Sections</option>
                    @foreach ($sections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="paymentModeFilter"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
                    <option value="">All Payment Modes</option>
                    <option value="cash">Cash</option>
                    <option value="online">Online</option>
                    <option value="cheque">Cheque</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search student..."
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>
        </div>

        {{-- Transactions Table --}}
        <div class="bg-white rounded-lg shadow-md p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">#</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Receipt</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Class/Sec</th>
                            <th class="px-4 py-3 text-right text-xs text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Fee Type</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Mode</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs text-gray-500 uppercase">Submitted By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($payments as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-purple-700">{{ $p->receipt_number }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800">{{ $p->studentDetail->user->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">{{ $p->studentDetail->admission_no ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs">
                                    {{ $p->standard->name ?? '-' }} {{ $p->section ? '/ ' . $p->section->name : '' }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold">₹{{ number_format($p->amount, 2) }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $p->fee_type === 'academic' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600' }}">
                                        {{ ucfirst($p->fee_type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 capitalize text-xs">{{ str_replace('_', ' ', $p->payment_mode) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $p->payment_date->format('d M Y') }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $p->submitted_by }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-400">No payments found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $payments->links() }}</div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 6: PENALTIES & FEE CYCLE                                    --}}
    {{-- ════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'penalties')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Settings Form --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-5">Fee Cycle & Penalty Settings</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Penalty per Day (₹)</label>
                        <input type="number" wire:model="penaltyPerDay" placeholder="0" step="0.01" min="0"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                        <p class="text-xs text-gray-400 mt-1">Amount charged per overdue day per student.</p>
                        @error('penaltyPerDay') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fee Cycle</label>
                        <select wire:model="cycleType"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                            <option value="monthly">Monthly</option>
                            <option value="quarterly">Quarterly</option>
                        </select>
                        @error('cycleType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Day of Month</label>
                        <input type="number" wire:model="dueDayOfMonth" placeholder="10" min="1" max="31"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-purple-300">
                        <p class="text-xs text-gray-400 mt-1">E.g. 10 means fee is due on the 10th of each month.</p>
                        @error('dueDayOfMonth') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <button wire:click="saveSettings"
                    class="mt-6 w-full px-4 py-2 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff] text-white rounded-lg text-sm hover:opacity-90">
                    <i class="fas fa-save mr-1"></i> Save Settings
                </button>
            </div>

            {{-- Penalty Analytics --}}
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-base font-semibold text-gray-800 mb-5">Penalty Overview</h3>
                @if (!empty($penaltyAnalytics))
                    <div class="space-y-4">
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-red-600 uppercase font-medium">Total Estimated Penalty</p>
                                <p class="text-2xl font-bold text-red-700">₹{{ number_format($penaltyAnalytics['total'], 2) }}</p>
                            </div>
                            <i class="fas fa-exclamation-triangle text-red-300 text-3xl"></i>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                                <p class="text-xs text-orange-600 uppercase font-medium">Overdue Students</p>
                                <p class="text-xl font-bold text-orange-700">{{ $penaltyAnalytics['students'] ?? 0 }}</p>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-xs text-yellow-600 uppercase font-medium">Days Overdue</p>
                                <p class="text-xl font-bold text-yellow-700">{{ $penaltyAnalytics['days_overdue'] ?? 0 }}</p>
                            </div>
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-3">
                                <p class="text-xs text-purple-600 uppercase font-medium">Penalty Per Day</p>
                                <p class="text-xl font-bold text-purple-700">₹{{ $penaltyAnalytics['penalty_per_day'] ?? 0 }}</p>
                            </div>
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <p class="text-xs text-blue-600 uppercase font-medium">Cycle Type</p>
                                <p class="text-xl font-bold text-blue-700 capitalize">{{ $cycleType }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400">
                            Penalty is estimated based on students who have not made a payment this month and are past the due date (day {{ $dueDayOfMonth }}).
                        </p>
                    </div>
                @else
                    <p class="text-gray-400 text-sm">Save settings to view penalty analytics.</p>
                @endif
            </div>
        </div>
    @endif

</div>
