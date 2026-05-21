<div class="min-h-screen bg-gray-50/50">

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
                        <select wire:model.live="bulkExam" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }} ({{ $exam->academic_year }})</option>
                            @endforeach
                        </select>
                        @error('bulkExam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Class <span class="text-red-500">*</span></label>
                        <select wire:model.live="bulkStandard" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select Class</option>
                            @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                        @error('bulkStandard') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Section <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select wire:model.live="bulkSection" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            {{ $bulkSections->isEmpty() ? 'disabled' : '' }}>
                            <option value="">All Sections</option>
                            @foreach($bulkSections as $sec)
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
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" wire:model.live="bulkGenerateType" value="attendance"
                            class="text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        <div>
                            <span class="text-sm font-semibold text-gray-800">By Attendance</span>
                            <p class="text-xs text-gray-400">Generate if attendance % meets threshold</p>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="radio" wire:model.live="bulkGenerateType" value="fee"
                            class="text-emerald-600 focus:ring-emerald-500 border-gray-300">
                        <div>
                            <span class="text-sm font-semibold text-gray-800">By Fee Clearance</span>
                            <p class="text-xs text-gray-400">Generate if student has paid fees</p>
                        </div>
                    </label>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-56">
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Minimum Percentage <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="number" wire:model="bulkPercentage" min="1" max="100"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 pr-8">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                        </div>
                        @error('bulkPercentage') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Subjects --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Step 3 — Exam Schedule</h3>
                    <button type="button" wire:click="addBulkSubject"
                        class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded-lg hover:bg-emerald-700 transition">
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
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
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
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Time *</label>
                                <input type="time" wire:model="bulkSubjects.{{ $i }}.exam_time"
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Duration *</label>
                                <input type="text" wire:model="bulkSubjects.{{ $i }}.exam_duration" placeholder="3 Hrs"
                                    class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Additional --}}
            <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-4">
                <h3 class="text-sm font-bold text-gray-700 uppercase tracking-wide">Step 4 — Additional Info</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reporting Time</label>
                        <input type="time" wire:model="bulkReportingTime"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Instructions <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea wire:model="bulkInstructions" rows="3" placeholder="Enter exam instructions…"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 pb-8">
                <button wire:click="closeBulkScreen"
                    class="px-5 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button wire:click="bulkGenerateAdmitCards"
                    class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-lg font-semibold shadow-md transition flex items-center gap-2">
                    <x-icon name="document-duplicate" class="h-5 w-5" />
                    Generate Admit Cards
                </button>
            </div>
        </div>
    </div>
    @endif
    {{-- ══════════════════════════ END BULK SCREEN ══════════════════════════ --}}


    {{-- ══════════════════════════ STICKY HEADER ══════════════════════════ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-6 pt-4 pb-3 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-700 leading-tight">Admit Cards</h1>
                <p class="text-xs text-gray-400 mt-0.5">Generate and manage student admit cards</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="openBulkScreen"
                    class="flex items-center gap-1.5 px-3 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    <x-icon name="document-duplicate" class="w-4 h-4" />
                    Bulk Generate
                </button>
                <button wire:click="openIssueModal"
                    class="flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition shadow-sm">
                    <x-icon name="ticket" class="w-4 h-4" />
                    Issue Admit Card
                </button>
            </div>
        </div>

        {{-- Analytics Strip --}}
        <div class="px-6 pb-3 grid grid-cols-3 gap-3">
            <div class="flex items-center gap-3 bg-emerald-50 rounded-xl px-4 py-3 border border-emerald-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="users" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Students</p>
                    <p class="text-lg font-bold text-emerald-700">{{ $this->analytics['total'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="ticket" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Issued</p>
                    <p class="text-lg font-bold text-blue-700">{{ $this->analytics['issued'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-amber-50 rounded-xl px-4 py-3 border border-amber-100">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="clock" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Remaining</p>
                    <p class="text-lg font-bold text-amber-700">{{ $this->analytics['remaining'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-5">

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-sm flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 text-green-500 flex-shrink-0" />
            {{ session('success') }}
        </div>
        @endif

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-40">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Search</label>
                    <div class="relative">
                        <x-icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Name, card number…"
                            class="w-full pl-9 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    </div>
                </div>
                <div class="min-w-40">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Exam</label>
                    <select wire:model.live="examFilter"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Exams</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-36">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Class</label>
                    <select wire:model.live="standardFilter"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Classes</option>
                        @foreach($standards as $std)
                            <option value="{{ $std->id }}">{{ $std->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($filterSections->isNotEmpty())
                <div class="min-w-32">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Section</label>
                    <select wire:model.live="sectionFilter"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All</option>
                        @foreach($filterSections as $sec)
                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="min-w-32">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Status</label>
                    <select wire:model.live="statusFilter"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="used">Used</option>
                    </select>
                </div>
                @if($search || $examFilter || $standardFilter || $sectionFilter || $statusFilter)
                <div class="self-end">
                    <button wire:click="resetFilters"
                        class="px-3 py-2 text-xs text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Clear
                    </button>
                </div>
                @endif
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Print All --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <span class="text-sm text-gray-500">{{ $admitCards->total() }} card(s)</span>
                <a href="{{ $this->getPrintAllUrl() }}"
                    target="_blank"
                    class="flex items-center gap-1.5 px-4 py-1.5 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold rounded-lg transition">
                    <x-icon name="printer" class="h-4 w-4" />
                    Print All
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700">#</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700">Class / Section</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700">Exam</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-emerald-700">Card No. / Roll No.</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-700">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-emerald-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($admitCards as $i => $card)
                        <tr class="hover:bg-emerald-50/20 transition">
                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $admitCards->firstItem() + $i }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($card->studentDetail?->image)
                                        <img src="{{ Storage::url($card->studentDetail->image) }}"
                                            class="w-9 h-9 rounded-full object-cover border border-gray-200 flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                            <span class="text-emerald-600 font-bold text-sm">{{ substr($card->student_name, 0, 1) }}</span>
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
                                    <a href="{{ route('accounts.admit-card.view', [$org->serial_number ?? $org->id, $card->id]) }}"
                                        target="_blank"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="View">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    {{-- Download --}}
                                    <a href="{{ route('accounts.admit-card.download', [$org->serial_number ?? $org->id, $card->id]) }}"
                                        class="p-1.5 text-green-600 hover:bg-green-50 rounded-lg transition" title="Download PDF">
                                        <x-icon name="arrow-down-tray" class="h-4 w-4" />
                                    </a>
                                    {{-- Delete --}}
                                    <button wire:click="deleteCard({{ $card->id }})"
                                        class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <x-icon name="trash" class="h-4 w-4" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-14 text-center">
                                <x-icon name="ticket" class="w-12 h-12 text-gray-200 mx-auto mb-3" />
                                <p class="text-gray-400 text-sm font-medium">No admit cards found.</p>
                                <button wire:click="openIssueModal" class="mt-2 text-xs text-emerald-600 hover:underline">Issue first admit card</button>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($admitCards->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $admitCards->links() }}</div>
            @endif
        </div>

    </div>


    {{-- ══════════════════════════ ISSUE ADMIT CARD MODAL ══════════════════════════ --}}
    @if($showIssueModal)
    <div class="fixed inset-0 z-[9999] flex items-center justify-center px-4 py-6"
         style="background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl flex flex-col max-h-[92vh]" wire:click.stop>

            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Issue Admit Cards</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Select exam, class and students</p>
                </div>
                <button wire:click="closeIssueModal" class="text-gray-400 hover:text-gray-600">
                    <x-icon name="x-mark" class="w-5 h-5" />
                </button>
            </div>

            <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Exam <span class="text-red-500">*</span></label>
                        <select wire:model.live="issueExam" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }} ({{ $exam->academic_year }})</option>
                            @endforeach
                        </select>
                        @error('issueExam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Class <span class="text-red-500">*</span></label>
                        <select wire:model.live="issueStandard" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select Class</option>
                            @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                        @error('issueStandard') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Section <span class="text-gray-400 font-normal">(optional)</span></label>
                        <select wire:model.live="issueSection" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                            {{ $issueSections->isEmpty() ? 'disabled' : '' }}>
                            <option value="">All Sections</option>
                            @foreach($issueSections as $sec)
                                <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if($issueExam && $issueStandard)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                        <p class="text-xs font-bold text-gray-700">Students ({{ $this->issueAvailableStudents->count() }} available)</p>
                        <div class="flex gap-3">
                            <button type="button" wire:click="selectAllIssueStudents" class="text-xs text-emerald-600 hover:underline font-semibold">Select All</button>
                            <button type="button" wire:click="deselectAllIssueStudents" class="text-xs text-gray-400 hover:underline">Deselect All</button>
                        </div>
                    </div>
                    @if($this->issueAvailableStudents->isEmpty())
                        <div class="px-4 py-6 text-center text-sm text-gray-400">All students already have admit cards for this exam.</div>
                    @else
                        <div class="max-h-48 overflow-y-auto divide-y divide-gray-100">
                            @foreach($this->issueAvailableStudents as $student)
                            <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50/30 cursor-pointer">
                                <input type="checkbox"
                                    wire:click="toggleIssueStudent({{ $student->id }})"
                                    @checked(in_array($student->id, $issueStudents))
                                    class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                                <div class="flex items-center gap-2 flex-1">
                                    @if($student->image)
                                        <img src="{{ Storage::url($student->image) }}" class="w-7 h-7 rounded-full object-cover border">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-xs">
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

                @if($issueStandard)
                <div class="border border-gray-200 rounded-xl overflow-hidden">
                    <div class="flex items-center justify-between px-4 py-2.5 bg-emerald-50 border-b border-emerald-100">
                        <p class="text-xs font-bold text-emerald-800">Exam Schedule</p>
                        <button type="button" wire:click="addIssueSubject"
                            class="flex items-center gap-1 text-xs text-emerald-600 hover:underline font-semibold">
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
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
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
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Time *</label>
                                    <input type="time" wire:model="issueSubjects.{{ $i }}.exam_time"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-500 mb-1">Duration *</label>
                                    <input type="text" wire:model="issueSubjects.{{ $i }}.exam_duration" placeholder="3 Hrs"
                                        class="w-full rounded-lg border-gray-300 text-xs focus:border-emerald-500 focus:ring-emerald-500">
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
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reporting Time</label>
                        <input type="time" wire:model="issueReportingTime"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Instructions <span class="text-gray-400 font-normal">(optional)</span></label>
                    <textarea wire:model="issueInstructions" rows="3" placeholder="Enter exam instructions…"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 resize-none"></textarea>
                </div>

            </div>

            <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-b-2xl">
                <span class="text-xs text-gray-500">{{ count($issueStudents) }} student(s) selected</span>
                <div class="flex gap-2">
                    <button wire:click="closeIssueModal"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                    <button wire:click="issueAdmitCards"
                        class="px-4 py-2 text-sm bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold shadow transition flex items-center gap-2">
                        <x-icon name="ticket" class="h-4 w-4" />
                        Issue Cards
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- ══════════════════════════ DELETE CONFIRM ══════════════════════════ --}}
    @if($pendingDeleteId)
    <div class="fixed inset-0 z-[10000] flex items-center justify-center px-4"
         style="background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" wire:click.stop>
            <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <x-icon name="trash" class="w-7 h-7 text-red-500" />
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1">Delete Admit Card?</h3>
            <p class="text-sm text-gray-500 mb-5">This cannot be undone.</p>
            <div class="flex justify-center gap-3">
                <button wire:click="cancelDelete"
                    class="px-4 py-2 text-sm border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition">Cancel</button>
                <button wire:click="doDelete"
                    class="px-4 py-2 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 font-semibold shadow transition">Delete</button>
            </div>
        </div>
    </div>
    @endif

</div>
