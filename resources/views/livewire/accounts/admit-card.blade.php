<div class="min-h-screen bg-gray-50/50">

    {{-- ═══ STICKY HEADER ═══ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="px-6 pt-4 pb-3 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-700 leading-tight">Admit Cards</h1>
                <p class="text-xs text-gray-400 mt-0.5">Generate and manage student admit cards</p>
            </div>
            <x-button emerald label="Bulk Generate" icon="plus"
                wire:click="openBulkModal" />
        </div>

        {{-- Analytics Strip --}}
        <div class="px-6 pb-3 grid grid-cols-3 gap-3">
            <div class="flex items-center gap-3 bg-emerald-50 rounded-xl px-4 py-3 border border-emerald-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="users" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Students</p>
                    <p class="text-lg font-bold text-emerald-700">{{ $totalStudents }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="ticket" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Total Issued</p>
                    <p class="text-lg font-bold text-blue-700">{{ $totalIssued }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-green-50 rounded-xl px-4 py-3 border border-green-100">
                <div class="w-8 h-8 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="check-circle" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <p class="text-xs text-gray-500 font-medium">Active Cards</p>
                    <p class="text-lg font-bold text-green-700">{{ $totalActive }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-5">

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-green-700 text-sm flex items-center gap-2">
                <x-icon name="check-circle" class="w-4 h-4 text-green-500" />
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
                @if($search || $examFilter || $standardFilter || $statusFilter)
                    <div class="self-end">
                        <x-button flat xs label="Clear" wire:click="resetFilters" />
                    </div>
                @endif
            </div>
        </div>

        {{-- Admit Cards Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-emerald-50">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">#</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Card No.</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Student</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Class / Section</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Exam</th>
                            <th class="px-4 py-3 text-left font-semibold text-emerald-700">Issued On</th>
                            <th class="px-4 py-3 text-center font-semibold text-emerald-700">Status</th>
                            <th class="px-4 py-3 text-center font-semibold text-emerald-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($admitCards as $i => $card)
                            <tr class="hover:bg-emerald-50/20 transition">
                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $admitCards->firstItem() + $i }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $card->admit_card_number }}</td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $card->student_name }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ $card->studentDetail->standard->name ?? '-' }}
                                    / {{ $card->studentDetail->section->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $card->exam_name }}</td>
                                <td class="px-4 py-3 text-gray-500 text-xs">
                                    {{ $card->issue_date?->format('d M Y') ?? '-' }}
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
                                        <x-button flat xs icon="eye" emerald
                                            wire:click="viewCard({{ $card->id }})" title="View" />
                                        <x-button flat xs icon="trash" red
                                            wire:click="deleteCard({{ $card->id }})" title="Delete" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-14 text-center">
                                    <x-icon name="ticket" class="w-12 h-12 text-gray-200 mx-auto mb-3" />
                                    <p class="text-gray-400 text-sm font-medium">No admit cards found.</p>
                                    <p class="text-gray-300 text-xs mt-1">Click "Bulk Generate" to create admit cards.</p>
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


    {{-- ═══ BULK GENERATE MODAL ═══ --}}
    @if($showBulkModal)
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]" wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800">Bulk Generate Admit Cards</h3>
                    <button type="button" wire:click="closeBulkModal" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">

                    {{-- Exam Selection --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Exam <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="selectedExam"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select Exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }} ({{ $exam->academic_year }})</option>
                            @endforeach
                        </select>
                        @error('selectedExam') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Class + Section --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Class <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedStandard"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Select Class</option>
                                @foreach($standards as $std)
                                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedStandard') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Section</label>
                            <select wire:model.live="selectedSection"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                                {{ $bulkSections->isEmpty() ? 'disabled' : '' }}>
                                <option value="">All Sections</option>
                                @foreach($bulkSections as $sec)
                                    <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Options --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Reporting Time</label>
                            <input type="time" wire:model="bulkReportingTime"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>
                    </div>

                    {{-- Checkboxes --}}
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="checkAttendance"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-gray-700">Check Attendance</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="checkFeeClearance"
                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                            <span class="text-sm text-gray-700">Check Fee Clearance</span>
                        </label>
                    </div>

                    {{-- Instructions --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Instructions (optional)</label>
                        <textarea wire:model="bulkInstructions" rows="2" placeholder="Instructions for students…"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                    </div>

                    {{-- Student List --}}
                    @if($selectedExam && $selectedStandard)
                        <div class="border border-gray-200 rounded-xl overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-50 border-b border-gray-100">
                                <p class="text-xs font-bold text-gray-700">
                                    Available Students ({{ $availableStudents->count() }})
                                </p>
                                <div class="flex gap-2">
                                    <button type="button" wire:click="selectAllStudents"
                                        class="text-xs text-emerald-600 hover:underline font-semibold">Select All</button>
                                    <button type="button" wire:click="deselectAllStudents"
                                        class="text-xs text-gray-400 hover:underline">Deselect All</button>
                                </div>
                            </div>
                            @if($availableStudents->isEmpty())
                                <div class="px-4 py-6 text-center text-sm text-gray-400">
                                    All students already have admit cards for this exam.
                                </div>
                            @else
                                <div class="max-h-48 overflow-y-auto divide-y divide-gray-100">
                                    @foreach($availableStudents as $student)
                                        <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-emerald-50/30 cursor-pointer">
                                            <input type="checkbox"
                                                wire:click="toggleStudent({{ $student['id'] }})"
                                                @checked(in_array($student['id'], $selectedStudents))
                                                class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-800">{{ $student['name'] }}</p>
                                                <p class="text-xs text-gray-400">
                                                    {{ $student['admission_no'] }}
                                                    @if($student['section'])
                                                        · {{ $student['class'] }} / {{ $student['section'] }}
                                                    @else
                                                        · {{ $student['class'] }}
                                                    @endif
                                                </p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @error('selectedStudents')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    @endif

                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center bg-gray-50/50 rounded-b-2xl">
                    <span class="text-xs text-gray-500">{{ count($selectedStudents) }} student(s) selected</span>
                    <div class="flex gap-2">
                        <x-button flat label="Cancel" wire:click="closeBulkModal" />
                        <x-button emerald label="Generate Admit Cards" icon="ticket"
                            wire:click="bulkGenerateAdmitCards"
                            :disabled="empty($selectedStudents)" />
                    </div>
                </div>
            </div>
        </div>
    @endif


    {{-- ═══ VIEW MODAL ═══ --}}
    @if($showViewModal && $viewAdmitCard)
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]" wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800">Admit Card Details</h3>
                    <button type="button" wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-4">
                    {{-- Card Number + Status --}}
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Card Number</p>
                            <p class="text-sm font-mono font-bold text-gray-800">{{ $viewAdmitCard->admit_card_number }}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            {{ $viewAdmitCard->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ ucfirst($viewAdmitCard->status) }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Student Name</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewAdmitCard->student_name }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Roll Number</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewAdmitCard->roll_number ?? '-' }}</p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Class / Section</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">
                                {{ $viewAdmitCard->studentDetail->standard->name ?? '-' }}
                                / {{ $viewAdmitCard->studentDetail->section->name ?? '-' }}
                            </p>
                        </div>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Reporting Time</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewAdmitCard->reporting_time ?? '-' }}</p>
                        </div>
                        <div class="col-span-2 p-3 bg-emerald-50 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Exam</p>
                            <p class="text-sm font-semibold text-emerald-800 mt-0.5">{{ $viewAdmitCard->exam_name }}</p>
                            <p class="text-xs text-emerald-600">{{ $viewAdmitCard->academic_year }}</p>
                        </div>
                        @if($viewAdmitCard->instructions)
                            <div class="col-span-2 p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-400 uppercase tracking-wide">Instructions</p>
                                <p class="text-sm text-gray-700 mt-0.5">{{ $viewAdmitCard->instructions }}</p>
                            </div>
                        @endif
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Issued On</p>
                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewAdmitCard->issue_date?->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50/50 rounded-b-2xl">
                    <x-button flat label="Close" wire:click="closeViewModal" />
                </div>
            </div>
        </div>
    @endif


    {{-- ═══ DELETE CONFIRM ═══ --}}
    @if($pendingDeleteId)
        <div class="fixed inset-0 z-[1000] flex items-center justify-center px-4"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" wire:click.stop>
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-7 h-7 text-red-500" />
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Delete Admit Card?</h3>
                <p class="text-sm text-gray-500 mb-5">This cannot be undone.</p>
                <div class="flex justify-center gap-3">
                    <x-button flat label="Cancel" wire:click="cancelDelete" />
                    <x-button red label="Delete" wire:click="doDelete" />
                </div>
            </div>
        </div>
    @endif

</div>
