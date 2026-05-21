<div class="p-4">
    <x-notifications />
    <x-dialog />

    {{-- ══════════════════════════ BULK GENERATE FULL SCREEN ══════════════════════════ --}}
    @if($showBulkScreen)
    <div class="fixed inset-0 z-[9998] bg-white overflow-y-auto">
        <div class="sticky top-0 z-10 bg-white border-b border-gray-200 shadow-sm">
            <div class="px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Bulk Generate Admit Cards</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Auto-generate based on attendance or fee criteria</p>
                </div>
                <button wire:click="closeBulkScreen"
                    class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-600 hover:bg-gray-50 transition">
                    <x-icon name="x-mark" class="h-5 w-5" /> Close
                </button>
            </div>
        </div>

        <div class="max-w-3xl mx-auto px-6 py-8 space-y-6">

            {{-- Exam + Class + Section --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Step 1 — Select Exam & Class</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Exam <span class="text-red-500">*</span></label>
                        <select wire:model.live="bulkExam" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Exam</option>
                            @foreach($this->exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }} ({{ $exam->academic_year }})</option>
                            @endforeach
                        </select>
                        @error('bulkExam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Class <span class="text-red-500">*</span></label>
                        <select wire:model.live="bulkStandard" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Class</option>
                            @foreach($this->standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                        @error('bulkStandard') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Section <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select wire:model.live="bulkSection" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            {{ $this->bulkSections->isEmpty() ? 'disabled' : '' }}>
                            <option value="">All Sections</option>
                            @foreach($this->bulkSections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Criteria --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Step 2 — Eligibility Criteria</h3>
                <div class="flex gap-6">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="radio" wire:model.live="bulkGenerateType" value="attendance"
                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <div>
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700">By Attendance</span>
                            <p class="text-xs text-gray-400">Generate if attendance % meets threshold</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="radio" wire:model.live="bulkGenerateType" value="fee"
                            class="text-indigo-600 focus:ring-indigo-500 border-gray-300">
                        <div>
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-indigo-700">By Fee Clearance</span>
                            <p class="text-xs text-gray-400">Generate if student has paid fees</p>
                        </div>
                    </label>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-56">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            {{ $bulkGenerateType === 'attendance' ? 'Minimum Attendance %' : 'Fee Coverage %' }}
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" wire:model="bulkPercentage" min="1" max="100"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 pr-8">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                        </div>
                        @error('bulkPercentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="pt-5">
                        <div class="inline-flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 text-xs px-3 py-2 rounded-lg">
                            <x-icon name="information-circle" class="h-4 w-4 flex-shrink-0" />
                            Students meeting the {{ $bulkPercentage }}% threshold will get admit cards automatically
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subjects --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Step 3 — Exam Schedule</h3>
                    <button type="button" wire:click="addBulkSubject"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700 transition">
                        <x-icon name="plus" class="h-3.5 w-3.5" /> Add Subject
                    </button>
                </div>
                <div class="space-y-3">
                    @foreach($bulkSubjects as $i => $subj)
                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-gray-500">Subject {{ $i + 1 }}</span>
                            @if(count($bulkSubjects) > 1)
                            <button type="button" wire:click="removeBulkSubject({{ $i }})"
                                class="text-red-400 hover:text-red-600">
                                <x-icon name="x-mark" class="h-4 w-4" />
                            </button>
                            @endif
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                            <div class="col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Subject *</label>
                                <select wire:model="bulkSubjects.{{ $i }}.subject_id"
                                    wire:change="syncBulkSubjectName({{ $i }})"
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Choose Subject</option>
                                    @foreach($this->allSubjects as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}{{ $sub->code ? ' ('.$sub->code.')' : '' }}</option>
                                    @endforeach
                                </select>
                                @error("bulkSubjects.{$i}.subject_id") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Date *</label>
                                <input type="date" wire:model="bulkSubjects.{{ $i }}.exam_date"
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @error("bulkSubjects.{$i}.exam_date") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Time *</label>
                                <input type="time" wire:model="bulkSubjects.{{ $i }}.exam_time"
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @error("bulkSubjects.{$i}.exam_time") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Duration *</label>
                                <input type="text" wire:model="bulkSubjects.{{ $i }}.exam_duration" placeholder="3 Hrs"
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                @error("bulkSubjects.{$i}.exam_duration") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @error('bulkSubjects') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Instructions + Reporting Time --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Step 4 — Additional Info</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reporting Time</label>
                        <input type="time" wire:model="bulkReportingTime"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Instructions for Students <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea wire:model="bulkInstructions" rows="3" placeholder="Enter exam instructions…"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pb-8">
                <button wire:click="closeBulkScreen"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="bulkGenerateAdmitCards"
                    class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-semibold shadow-md transition flex items-center gap-2">
                    <x-icon name="document-duplicate" class="h-5 w-5" />
                    Generate Admit Cards
                </button>
            </div>

        </div>
    </div>
    @endif
    {{-- ══════════════════════════ END BULK SCREEN ══════════════════════════ --}}


    {{-- ══════════════════════════ HEADER ══════════════════════════ --}}
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-sm border mb-5 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Admit Card Management</h2>
                <p class="text-gray-500 mt-1 text-sm">Issue and manage examination admit cards</p>
            </div>
            <div class="flex gap-3">
                <button wire:click="openBulkScreen"
                    class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <x-icon name="document-duplicate" class="h-5 w-5" />
                    Bulk Generate
                </button>
                <button wire:click="openIssueModal"
                    class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition flex items-center gap-2">
                    <x-icon name="ticket" class="h-5 w-5" />
                    Issue Admit Card
                </button>
            </div>
        </div>
    </div>

    {{-- Analytics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <x-icon name="users" class="h-6 w-6 text-indigo-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Students</p>
                <p class="text-2xl font-bold text-gray-900">{{ $this->analytics['total'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <x-icon name="ticket" class="h-6 w-6 text-green-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Issued</p>
                <p class="text-2xl font-bold text-green-700">{{ $this->analytics['issued'] }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border p-5 flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                <x-icon name="clock" class="h-6 w-6 text-amber-600" />
            </div>
            <div>
                <p class="text-sm text-gray-500">Remaining</p>
                <p class="text-2xl font-bold text-amber-600">{{ $this->analytics['remaining'] }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border mb-5 p-4">
        <div class="flex flex-wrap gap-3 items-end">
            {{-- Search --}}
            <div class="flex-1 min-w-44">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Search</label>
                <div class="relative">
                    <x-icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Name, card number, roll no…"
                        class="w-full pl-9 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                </div>
            </div>
            {{-- Exam --}}
            <div class="min-w-44">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Exam</label>
                <select wire:model.live="examFilter"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Exams</option>
                    @foreach($this->exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Class --}}
            <div class="min-w-36">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Class</label>
                <select wire:model.live="standardFilter"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Classes</option>
                    @foreach($this->standards as $std)
                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Section (appears when class selected) --}}
            @if($this->filterSections->isNotEmpty())
            <div class="min-w-32">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Section</label>
                <select wire:model.live="sectionFilter"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    @foreach($this->filterSections as $sec)
                        <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            {{-- Status --}}
            <div class="min-w-32">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Status</label>
                <select wire:model.live="statusFilter"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="used">Used</option>
                </select>
            </div>
            {{-- Per page --}}
            <div class="min-w-28">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Show</label>
                <select wire:model.live="perPage"
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            @if($search || $examFilter || $standardFilter || $sectionFilter || $statusFilter)
            <div class="self-end">
                <button wire:click="resetFilters"
                    class="px-3 py-2 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Clear Filters
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        {{-- Print All button --}}
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <span class="text-sm text-gray-500">{{ $admitCards->total() }} card(s) found</span>
            <a href="{{ $this->getPrintAllUrl() }}" target="_blank"
                class="flex items-center gap-1.5 px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold rounded-lg transition shadow-sm">
                <x-icon name="printer" class="h-4 w-4" />
                Print All
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-indigo-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700">Class / Section</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700">Exam</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-indigo-700">Card No. / Roll No.</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-indigo-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($admitCards as $i => $card)
                    <tr class="hover:bg-indigo-50/20 transition">
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $admitCards->firstItem() + $i }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($card->studentDetail?->image)
                                    <img src="{{ Storage::url($card->studentDetail->image) }}"
                                        class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center flex-shrink-0">
                                        <span class="text-indigo-600 font-bold text-sm">{{ substr($card->student_name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $card->student_name }}</p>
                                    <p class="text-xs text-gray-400">{{ $card->studentDetail?->admission_no ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            {{ $card->studentDetail?->standard?->name ?? '—' }}
                            @if($card->studentDetail?->section?->name)
                                / {{ $card->studentDetail->section->name }}
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="text-sm text-gray-700 font-medium">{{ $card->exam_name }}</p>
                            <p class="text-xs text-gray-400">{{ $card->academic_year }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-mono text-xs text-gray-800 font-medium">{{ $card->admit_card_number }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">Roll: {{ $card->roll_number }}</p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($card->status === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">Active</span>
                            @elseif($card->status === 'used')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Used</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-1">
                                {{-- View --}}
                                <a href="{{ route('admin.admit-card.view', [$org->serial_number ?? $org->id, $card->id]) }}"
                                    target="_blank"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View">
                                    <x-icon name="eye" class="h-4 w-4" />
                                </a>
                                {{-- Download --}}
                                <a href="{{ route('admin.admit-card.download', [$org->serial_number ?? $org->id, $card->id]) }}"
                                    class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition" title="Download PDF">
                                    <x-icon name="arrow-down-tray" class="h-4 w-4" />
                                </a>
                                {{-- Edit --}}
                                <button wire:click="openEditModal({{ $card->id }})"
                                    class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Edit">
                                    <x-icon name="pencil-square" class="h-4 w-4" />
                                </button>
                                {{-- Delete --}}
                                <button wire:click="confirmDelete({{ $card->id }})"
                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                    <x-icon name="trash" class="h-4 w-4" />
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-16 text-center">
                            <x-icon name="ticket" class="w-14 h-14 text-gray-200 mx-auto mb-3" />
                            <p class="text-gray-400 text-sm font-medium">No admit cards found</p>
                            @if($search || $examFilter || $standardFilter || $statusFilter)
                                <button wire:click="resetFilters" class="mt-3 text-xs text-indigo-600 hover:underline">Clear filters</button>
                            @else
                                <button wire:click="openIssueModal" class="mt-3 text-xs text-indigo-600 hover:underline">Issue first admit card</button>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($admitCards->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">{{ $admitCards->links() }}</div>
        @endif
    </div>


    {{-- ══════════════════════════ ISSUE ADMIT CARD MODAL ══════════════════════════ --}}
    @if($showIssueModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center px-4 py-6"
         style="background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col max-h-[92vh]" wire:click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Issue Admit Cards</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Select exam, class and students to issue admit cards</p>
                </div>
                <button wire:click="closeIssueModal" class="text-gray-400 hover:text-gray-600">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                {{-- Exam + Class + Section --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Exam <span class="text-red-500">*</span></label>
                        <select wire:model.live="issueExam" class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Exam</option>
                            @foreach($this->exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }} ({{ $exam->academic_year }})</option>
                            @endforeach
                        </select>
                        @error('issueExam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Class <span class="text-red-500">*</span></label>
                        <select wire:model.live="issueStandard" class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                            <option value="">Select Class</option>
                            @foreach($this->standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                        @error('issueStandard') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Section <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select wire:model.live="issueSection" class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500"
                            {{ $this->issueSections->isEmpty() ? 'disabled' : '' }}>
                            <option value="">All Sections</option>
                            @foreach($this->issueSections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Student List --}}
                @if($issueExam && $issueStandard)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                        <p class="text-xs font-bold text-gray-700">Students ({{ $this->issueAvailableStudents->count() }} available)</p>
                        <div class="flex gap-3">
                            <button type="button" wire:click="selectAllIssueStudents" class="text-xs text-purple-600 hover:underline font-semibold">Select All</button>
                            <button type="button" wire:click="deselectAllIssueStudents" class="text-xs text-gray-400 hover:underline">Deselect All</button>
                        </div>
                    </div>
                    @if($this->issueAvailableStudents->isEmpty())
                        <div class="px-4 py-6 text-center text-sm text-gray-400">All students already have admit cards for this exam.</div>
                    @else
                        <div class="max-h-48 overflow-y-auto divide-y divide-gray-100">
                            @foreach($this->issueAvailableStudents as $student)
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-purple-50/30 cursor-pointer">
                                <input type="checkbox"
                                    wire:click="toggleIssueStudent({{ $student->id }})"
                                    @checked(in_array($student->id, $issueStudents))
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <div class="flex items-center gap-2 flex-1">
                                    @if($student->image)
                                        <img src="{{ Storage::url($student->image) }}" class="w-7 h-7 rounded-full object-cover border">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-xs">
                                            {{ substr($student->full_name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <p class="text-sm font-medium text-gray-800">{{ $student->full_name }}</p>
                                        <p class="text-xs text-gray-400">{{ $student->admission_no }} · {{ $student->standard?->name }}@if($student->section) / {{ $student->section->name }} @endif</p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    @endif
                </div>
                @error('issueStudents') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                @endif

                {{-- Subjects --}}
                @if($issueStandard)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-purple-50 border-b border-purple-100">
                        <p class="text-xs font-bold text-purple-800">Exam Schedule (all subjects)</p>
                        <button type="button" wire:click="addIssueSubject"
                            class="flex items-center gap-1 text-xs text-purple-600 hover:underline font-semibold">
                            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Row
                        </button>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                        @foreach($issueSubjects as $i => $subj)
                        <div class="px-4 py-3 bg-white">
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 items-end">
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Subject *</label>
                                    <select wire:model="issueSubjects.{{ $i }}.subject_id"
                                        wire:change="syncIssueSubjectName({{ $i }})"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                        <option value="">Choose</option>
                                        @foreach($this->allSubjects as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->name }}{{ $sub->code ? ' ('.$sub->code.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                    @error("issueSubjects.{$i}.subject_id") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Date *</label>
                                    <input type="date" wire:model="issueSubjects.{{ $i }}.exam_date"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                    @error("issueSubjects.{$i}.exam_date") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Time *</label>
                                    <input type="time" wire:model="issueSubjects.{{ $i }}.exam_time"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                    @error("issueSubjects.{$i}.exam_time") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Duration *</label>
                                    <input type="text" wire:model="issueSubjects.{{ $i }}.exam_duration" placeholder="3 Hrs"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-purple-500 focus:ring-purple-500">
                                    @error("issueSubjects.{$i}.exam_duration") <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div class="flex items-end justify-end">
                                    @if(count($issueSubjects) > 1)
                                    <button type="button" wire:click="removeIssueSubject({{ $i }})"
                                        class="p-1.5 text-red-400 hover:text-red-600 rounded">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @error('issueSubjects') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
                @endif

                {{-- Instructions + Reporting Time --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reporting Time</label>
                        <input type="time" wire:model="issueReportingTime"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Instructions <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea wire:model="issueInstructions" rows="3" placeholder="Enter exam instructions…"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-purple-500 focus:ring-purple-500 resize-none"></textarea>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-b-2xl">
                <span class="text-xs text-gray-500">{{ count($issueStudents) }} student(s) selected</span>
                <div class="flex gap-2">
                    <button type="button" wire:click="closeIssueModal"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button type="button" wire:click="issueAdmitCards"
                        class="px-4 py-2 text-sm bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-lg hover:from-purple-700 hover:to-pink-700 font-semibold shadow transition flex items-center gap-2">
                        <x-icon name="ticket" class="h-4 w-4" />
                        Issue Cards
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- ══════════════════════════ EDIT MODAL ══════════════════════════ --}}
    @if($showEditModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center px-4 py-6"
         style="background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[92vh]" wire:click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-base font-bold text-gray-800">Edit Admit Card</h3>
                <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Card Number *</label>
                        <input type="text" wire:model="editAdmitCardNumber"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500 font-mono">
                        @error('editAdmitCardNumber') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Roll Number *</label>
                        <input type="text" wire:model="editRollNumber"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                        @error('editRollNumber') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Exam Roll No.</label>
                        <input type="text" wire:model="editExamRollNumber"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reporting Time</label>
                        <input type="time" wire:model="editReportingTime"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Room No. (R)</label>
                        <input type="text" wire:model="editRoomNumber" placeholder="e.g. 23"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Seat No. (S)</label>
                        <input type="text" wire:model="editSeatNumber" placeholder="e.g. 18"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Exam Center</label>
                        <input type="text" wire:model="editExamCenter"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Status *</label>
                        <select wire:model="editStatus"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="used">Used</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Exam Center Address</label>
                        <input type="text" wire:model="editExamCenterAddress"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500">
                    </div>
                </div>

                {{-- Subjects --}}
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-yellow-50 border-b border-yellow-100">
                        <p class="text-xs font-bold text-yellow-800">Exam Schedule</p>
                        <button type="button" wire:click="addEditSubject"
                            class="flex items-center gap-1 text-xs text-yellow-700 hover:underline font-semibold">
                            <x-icon name="plus" class="h-3.5 w-3.5" /> Add Row
                        </button>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-52 overflow-y-auto">
                        @foreach($editSubjects as $i => $subj)
                        <div class="px-4 py-3 bg-white">
                            <div class="grid grid-cols-2 md:grid-cols-6 gap-2 items-end">
                                <div class="col-span-2">
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Subject *</label>
                                    <select wire:model="editSubjects.{{ $i }}.subject_id"
                                        wire:change="syncEditSubjectName({{ $i }})"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-yellow-500 focus:ring-yellow-500">
                                        <option value="">Choose</option>
                                        @foreach($this->allSubjects as $sub)
                                            <option value="{{ $sub->id }}">{{ $sub->name }}{{ $sub->code ? ' ('.$sub->code.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Date *</label>
                                    <input type="date" wire:model="editSubjects.{{ $i }}.exam_date"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-yellow-500 focus:ring-yellow-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Time *</label>
                                    <input type="time" wire:model="editSubjects.{{ $i }}.exam_time"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-yellow-500 focus:ring-yellow-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Duration *</label>
                                    <input type="text" wire:model="editSubjects.{{ $i }}.exam_duration" placeholder="3 Hrs"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-yellow-500 focus:ring-yellow-500">
                                </div>
                                <div class="flex items-end gap-1 justify-end">
                                    <select wire:model="editSubjects.{{ $i }}.status"
                                        class="rounded border-gray-300 text-xs focus:border-yellow-500 focus:ring-yellow-500">
                                        <option value="eligible">Eligible</option>
                                        <option value="not_eligible">Not Eligible</option>
                                    </select>
                                    @if(count($editSubjects) > 1)
                                    <button type="button" wire:click="removeEditSubject({{ $i }})"
                                        class="p-1 text-red-400 hover:text-red-600 rounded">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Instructions</label>
                    <textarea wire:model="editInstructions" rows="3"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-yellow-500 focus:ring-yellow-500 resize-none"></textarea>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50 rounded-b-2xl">
                <button wire:click="closeEditModal"
                    class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button wire:click="saveEditCard"
                    class="px-4 py-2 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-semibold shadow transition">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- ══════════════════════════ DELETE CONFIRM ══════════════════════════ --}}
    @if($showDeleteModal)
    <div class="fixed inset-0 z-[10000] flex items-center justify-center px-4"
         style="background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" wire:click.stop>
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="trash" class="w-7 h-7 text-red-500" />
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1">Delete Admit Card?</h3>
            <p class="text-sm text-gray-500 mb-5">This action cannot be undone.</p>
            <div class="flex justify-center gap-3">
                <button wire:click="cancelDelete"
                    class="px-4 py-2 text-sm border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button wire:click="deleteAdmitCard"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow transition">Delete</button>
            </div>
        </div>
    </div>
    @endif

</div>
