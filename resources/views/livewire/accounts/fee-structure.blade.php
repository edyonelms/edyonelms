<div>
    {{-- ═══════════════════════════════════════════════════════════════════════════
         STICKY HEADER
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-6 pt-5 pb-3">
            {{-- Title row --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h1 class="text-xl font-bold text-gray-800">Fee Structure</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Manage academic and transport fee structures for your organisation</p>
                </div>
                <button wire:click="openStructureModal"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl shadow transition whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Fee Structure
                </button>
            </div>

            {{-- Analytics strip --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                <div class="bg-emerald-50 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-emerald-600 font-medium">Total Structures</p>
                        <p class="text-xl font-bold text-emerald-800">{{ $totalStructures }}</p>
                    </div>
                </div>
                <div class="bg-blue-50 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-blue-600 font-medium">Academic</p>
                        <p class="text-xl font-bold text-blue-800">{{ $academicCount }}</p>
                    </div>
                </div>
                <div class="bg-amber-50 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-amber-600 font-medium">Transport</p>
                        <p class="text-xl font-bold text-amber-800">{{ $transportCount }}</p>
                    </div>
                </div>
                <div class="bg-violet-50 rounded-xl px-4 py-3 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-violet-600 font-medium">Total Academic Amt</p>
                        <p class="text-xl font-bold text-violet-800">₹{{ number_format($totalAcademicAmt) }}</p>
                    </div>
                </div>
            </div>

            {{-- Tab bar --}}
            <div class="flex gap-1">
                <button wire:click="setTab('academic')"
                    class="px-5 py-2 rounded-t-lg text-sm font-semibold transition border-b-2
                        {{ $activeTab === 'academic'
                            ? 'border-emerald-600 text-emerald-700 bg-emerald-50'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    Academic
                </button>
                <button wire:click="setTab('transport')"
                    class="px-5 py-2 rounded-t-lg text-sm font-semibold transition border-b-2
                        {{ $activeTab === 'transport'
                            ? 'border-emerald-600 text-emerald-700 bg-emerald-50'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    Transport
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════════
         CONTENT AREA
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <div class="p-6 space-y-5">

        {{-- Filter bar --}}
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Class</label>
                    <select wire:model.live="filterStructureStandard"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Classes</option>
                        @foreach($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($activeTab === 'academic')
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Section</label>
                    <select wire:model.live="filterStructureSection"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                        {{ empty($sections) ? 'disabled' : '' }}>
                        <option value="">All Sections</option>
                        @foreach($sections as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Search</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search fee name..."
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 pl-9" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @else
                <div class="sm:col-span-3"></div>
                @endif
            </div>
        </div>

        {{-- ── ACADEMIC TAB TABLE ── --}}
        @if($activeTab === 'academic')
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-emerald-50 border-b border-emerald-100">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Class</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Section</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Fee Name</th>
                            <th class="px-4 py-3 text-right font-semibold text-emerald-700">Amount</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Academic Year</th>
                            <th class="px-4 py-3 text-center font-semibold text-emerald-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($structures as $structure)
                            <tr class="hover:bg-emerald-50/40 transition">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $structure->standard->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $structure->section->name ?? 'All Sections' }}
                                </td>
                                <td class="px-4 py-3 text-gray-700">{{ $structure->fee_name }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                    ₹{{ number_format($structure->amount, 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $structure->academic_year }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="viewStructure({{ $structure->id }})"
                                            class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="printStructure({{ $structure->id }})"
                                            class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 transition" title="Print">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="editStructure({{ $structure->id }})"
                                            class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="deleteStructure({{ $structure->id }})"
                                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    No academic fee structures found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($structures->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $structures->links() }}
            </div>
            @endif
        </div>
        @endif

        {{-- ── TRANSPORT TAB TABLE ── --}}
        @if($activeTab === 'transport')
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-amber-50 border-b border-amber-100">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-amber-700">Class</th>
                            <th class="px-4 py-3 text-left font-semibold text-amber-700">Section</th>
                            <th class="px-4 py-3 text-right font-semibold text-amber-700">Monthly Fee</th>
                            <th class="px-4 py-3 text-right font-semibold text-amber-700">Total Annual (11 mo.)</th>
                            <th class="px-4 py-3 text-left font-semibold text-amber-700">Academic Year</th>
                            <th class="px-4 py-3 text-center font-semibold text-amber-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transportMonthlyData as $tData)
                            <tr class="hover:bg-amber-50/40 transition">
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $tData['class'] }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $tData['section'] }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-amber-700">
                                    ₹{{ number_format($tData['monthly_fee'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                    ₹{{ number_format($tData['total'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $tData['academic_year'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button wire:click="viewStructure({{ $tData['id'] }})"
                                            class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 transition" title="View">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="editStructure({{ $tData['id'] }})"
                                            class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50 transition" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="deleteStructure({{ $tData['id'] }})"
                                            class="p-1.5 rounded-lg text-red-500 hover:bg-red-50 transition" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    No transport fee structures found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($structures->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $structures->links() }}
            </div>
            @endif
        </div>
        @endif

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════════
         ADD / EDIT MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($structureModalOpen)
    <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
            {{-- Modal header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $editStructureId ? 'Edit Fee Structure' : 'Add Fee Structure' }}
                </h2>
                <button wire:click="$set('structureModalOpen', false)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Modal body --}}
            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                {{-- Fee Type --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fee Type <span class="text-red-500">*</span></label>
                    <div class="flex gap-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="structureFeeType" value="academic"
                                class="text-emerald-600 border-gray-300 focus:ring-emerald-500" />
                            <span class="text-sm text-gray-700">Academic</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" wire:model.live="structureFeeType" value="transport"
                                class="text-emerald-600 border-gray-300 focus:ring-emerald-500" />
                            <span class="text-sm text-gray-700">Transport</span>
                        </label>
                    </div>
                    @error('structureFeeType') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Class --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                    <select wire:model.live="structureStandardId"
                        class="w-full rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Select Class</option>
                        @foreach($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                    @error('structureStandardId') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Sections (multi-select checkboxes) --}}
                @if($structureStandardId && count($formSections) > 0)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Sections</label>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2 p-3 bg-gray-50 rounded-xl border border-gray-200">
                        @foreach($formSections as $sec)
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox"
                                wire:model="structureSectionIds"
                                value="{{ $sec->id }}"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-gray-700">{{ $sec->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Leave unchecked to apply to all sections.</p>
                </div>
                @endif

                {{-- Academic Year --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="academicYear" placeholder="e.g. 2026-27"
                        class="w-full rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @error('academicYear') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Academic fee rows --}}
                @if($structureFeeType === 'academic')
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-sm font-semibold text-gray-700">Fee Details <span class="text-red-500">*</span></label>
                        <button wire:click="addFeeRow" type="button"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 hover:text-emerald-800 px-2 py-1 rounded-lg hover:bg-emerald-50 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Row
                        </button>
                    </div>
                    <div class="space-y-2">
                        @foreach($feeRows as $i => $row)
                        <div class="flex items-center gap-2">
                            <input type="text" wire:model="feeRows.{{ $i }}.name" placeholder="Fee Name (e.g. Tuition Fee)"
                                class="flex-1 rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            <input type="number" wire:model="feeRows.{{ $i }}.amount" placeholder="Amount (₹)" min="0" step="0.01"
                                class="w-36 rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            @if(count($feeRows) > 1)
                            <button wire:click="removeFeeRow({{ $i }})" type="button"
                                class="p-1.5 rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                        @if($errors->has("feeRows.$i.name") || $errors->has("feeRows.$i.amount"))
                        <p class="text-xs text-red-500">
                            {{ $errors->first("feeRows.$i.name") }} {{ $errors->first("feeRows.$i.amount") }}
                        </p>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Transport monthly fee --}}
                @if($structureFeeType === 'transport')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Monthly Fee (₹) <span class="text-red-500">*</span></label>
                    <input type="number" wire:model="transportMonthlyFee" placeholder="Monthly fee amount" min="0" step="0.01"
                        class="w-full rounded-xl border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    @if($transportMonthlyFee)
                    <p class="text-xs text-amber-600 mt-1">
                        Annual total (11 months, June excluded): ₹{{ number_format($transportMonthlyFee * 11, 2) }}
                    </p>
                    @endif
                    @error('transportMonthlyFee') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

            </div>

            {{-- Modal footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button wire:click="$set('structureModalOpen', false)"
                    class="px-5 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="saveStructure" wire:loading.attr="disabled"
                    class="px-5 py-2 text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveStructure">{{ $editStructureId ? 'Update' : 'Save' }}</span>
                    <span wire:loading wire:target="saveStructure">Saving...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
         VIEW MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($viewModalOpen)
    <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Fee Structure Details</h2>
                <button wire:click="$set('viewModalOpen', false)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Class</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewStructureData['class'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Section</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewStructureData['section'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Fee Type</p>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-xs font-semibold
                            {{ ($viewStructureData['fee_type'] ?? '') === 'academic' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ ucfirst($viewStructureData['fee_type'] ?? '') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Academic Year</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewStructureData['academic_year'] ?? '-' }}</p>
                    </div>
                    @if(($viewStructureData['fee_type'] ?? '') === 'academic')
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Fee Name</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewStructureData['fee_name'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Amount</p>
                        <p class="text-sm font-bold text-emerald-700 mt-0.5">₹{{ number_format($viewStructureData['amount'] ?? 0, 2) }}</p>
                    </div>
                    @else
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Monthly Fee</p>
                        <p class="text-sm font-bold text-amber-700 mt-0.5">₹{{ number_format($viewStructureData['monthly_fee'] ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Annual Total (11 mo.)</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">₹{{ number_format($viewStructureData['amount'] ?? 0, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>
            <div class="flex justify-end px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button wire:click="$set('viewModalOpen', false)"
                    class="px-5 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Close
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
         PRINT MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($printModalOpen)
    <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-800">Print Fee Structure</h2>
                <button wire:click="$set('printModalOpen', false)"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-6 py-5 space-y-4" id="printable-fee-structure">
                {{-- Print header --}}
                <div class="text-center border-b border-gray-200 pb-4">
                    <h3 class="text-lg font-bold text-gray-800">Fee Structure</h3>
                    <p class="text-sm text-gray-600">
                        Class: <strong>{{ $printData['class'] ?? '-' }}</strong>
                        &nbsp;|&nbsp;
                        Section: <strong>{{ $printData['section'] ?? '-' }}</strong>
                        &nbsp;|&nbsp;
                        Year: <strong>{{ $printData['academic_year'] ?? '-' }}</strong>
                    </p>
                </div>

                {{-- Academic fees table --}}
                @if(!empty($printData['academic_fees']))
                <div>
                    <h4 class="text-sm font-bold text-emerald-700 mb-2">Academic Fees</h4>
                    <table class="w-full text-sm border border-gray-200 rounded-xl overflow-hidden">
                        <thead class="bg-emerald-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-semibold text-emerald-700">Fee Name</th>
                                <th class="px-3 py-2 text-right font-semibold text-emerald-700">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($printData['academic_fees'] as $fee)
                            <tr>
                                <td class="px-3 py-2 text-gray-700">{{ $fee['fee_name'] }}</td>
                                <td class="px-3 py-2 text-right font-medium text-gray-800">₹{{ number_format($fee['amount'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-emerald-50 border-t border-emerald-200">
                            <tr>
                                <td class="px-3 py-2 font-bold text-emerald-800">Total Academic</td>
                                <td class="px-3 py-2 text-right font-bold text-emerald-800">₹{{ number_format($printData['academic_total'] ?? 0, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif

                {{-- Transport fee --}}
                @if(isset($printData['transport_fee']) && $printData['transport_fee'] > 0)
                <div>
                    <h4 class="text-sm font-bold text-amber-700 mb-2">Transport Fee</h4>
                    <div class="bg-amber-50 rounded-xl border border-amber-200 p-4 grid grid-cols-2 gap-3">
                        <div>
                            <p class="text-xs text-amber-600 font-semibold">Monthly Fee</p>
                            <p class="text-lg font-bold text-amber-800">₹{{ number_format($printData['transport_monthly'] ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-amber-600 font-semibold">Annual Total (11 mo.)</p>
                            <p class="text-lg font-bold text-amber-800">₹{{ number_format($printData['transport_fee'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button wire:click="$set('printModalOpen', false)"
                    class="px-5 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Close
                </button>
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-gray-700 hover:bg-gray-800 rounded-xl shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
         DELETE CONFIRM MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($pendingDeleteStructureId)
    <div class="fixed inset-0 z-[1000] flex items-center justify-center px-4 py-6"
         style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm">
            <div class="px-6 py-6 text-center">
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Delete Fee Structure?</h3>
                <p class="text-sm text-gray-500">This action cannot be undone. The fee structure will be permanently removed.</p>
            </div>
            <div class="flex items-center gap-3 px-6 pb-6">
                <button wire:click="cancelDeleteStructure"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="doDeleteStructure" wire:loading.attr="disabled"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-red-600 hover:bg-red-700 rounded-xl shadow transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="doDeleteStructure">Yes, Delete</span>
                    <span wire:loading wire:target="doDeleteStructure">Deleting...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
