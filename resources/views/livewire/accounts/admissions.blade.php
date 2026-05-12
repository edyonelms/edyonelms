<div class="min-h-screen bg-gray-50/50">

    {{-- ═══════════════════════════════════════════════════════════════════════════
         STICKY HEADER
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">

        {{-- Top bar: title + action button --}}
        <div class="px-6 pt-4 pb-3 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-700 leading-tight">Admissions</h1>
                <p class="text-xs text-gray-400 mt-0.5">Manage student enquiries &amp; exam papers</p>
            </div>
            <div>
                @if($activeTab === 'admissions')
                    <x-button emerald label="Add Student" icon="plus"
                        wire:click="openEnquiryModal" />
                @else
                    <x-button emerald label="Upload Paper" icon="arrow-up-tray"
                        wire:click="openPaperModal" />
                @endif
            </div>
        </div>

        {{-- Analytics Strip --}}
        <div class="px-6 pb-3 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="flex items-center gap-3 bg-emerald-50 rounded-xl px-4 py-3 border border-emerald-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="users" class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium truncate">Total Students</p>
                    <p class="text-lg font-bold text-emerald-700 leading-tight">{{ $totalStudents }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="arrow-path" class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium truncate">Updated</p>
                    <p class="text-lg font-bold text-blue-700 leading-tight">{{ $updatedStudents }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-amber-50 rounded-xl px-4 py-3 border border-amber-100">
                <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="calendar" class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium truncate">This Month</p>
                    <p class="text-lg font-bold text-amber-700 leading-tight">{{ $thisMonthAdded }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-purple-50 rounded-xl px-4 py-3 border border-purple-100">
                <div class="w-8 h-8 rounded-lg bg-purple-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="clock" class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium truncate">Last Month</p>
                    <p class="text-lg font-bold text-purple-700 leading-tight">{{ $lastMonthAdded }}</p>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="px-6 flex gap-6 border-t border-gray-100">
            <button wire:click="setTab('admissions')"
                class="pb-3 pt-2 px-1 text-sm font-semibold border-b-2 transition
                    {{ $activeTab === 'admissions'
                        ? 'border-emerald-600 text-emerald-700'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <x-icon name="user-plus" class="w-4 h-4 inline mr-1" />
                Admissions
            </button>
            <button wire:click="setTab('exam-papers')"
                class="pb-3 pt-2 px-1 text-sm font-semibold border-b-2 transition
                    {{ $activeTab === 'exam-papers'
                        ? 'border-emerald-600 text-emerald-700'
                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <x-icon name="document-text" class="w-4 h-4 inline mr-1" />
                Exam Papers
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════════
         PAGE CONTENT
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <div class="p-6 space-y-5">

        {{-- ── TAB 1: ADMISSIONS ─────────────────────────────────────────────── --}}
        @if($activeTab === 'admissions')

            {{-- Filter Bar --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    {{-- Month Filter (first) --}}
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Month
                        </label>
                        <select wire:model.live="filterMonth"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">All Months</option>
                            @foreach($monthOptions as $mo)
                                <option value="{{ $mo['value'] }}">{{ $mo['label'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Class Filter (second) --}}
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide">
                            Class
                        </label>
                        <select wire:model.live="filterStandard"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">All Classes</option>
                            @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Search --}}
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Search
                        </label>
                        <div class="relative">
                            <x-icon name="magnifying-glass" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Name, mobile…"
                                class="w-full pl-9 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>
                    </div>

                    {{-- Clear --}}
                    @if($search || $filterStandard || $filterMonth)
                        <div class="sm:self-end">
                            <x-button flat xs label="Clear" icon="x-mark"
                                wire:click="$set('search', ''); $set('filterStandard', ''); $set('filterMonth', '')" />
                        </div>
                    @endif
                </div>
            </div>

            {{-- Enquiries Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-emerald-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-emerald-700">#</th>
                                <th class="px-4 py-3 text-left font-semibold text-emerald-700">Student Name</th>
                                <th class="px-4 py-3 text-left font-semibold text-emerald-700">Mobile</th>
                                <th class="px-4 py-3 text-left font-semibold text-emerald-700">Guardian</th>
                                <th class="px-4 py-3 text-left font-semibold text-emerald-700">Class</th>
                                <th class="px-4 py-3 text-right font-semibold text-emerald-700">Admission Fee</th>
                                <th class="px-4 py-3 text-center font-semibold text-emerald-700">Status</th>
                                <th class="px-4 py-3 text-center font-semibold text-emerald-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($enquiries as $index => $enquiry)
                                <tr class="hover:bg-emerald-50/30 transition">
                                    <td class="px-4 py-3 text-gray-400 text-xs">
                                        {{ $enquiries->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">
                                        {{ $enquiry->student_name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $enquiry->mobile }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $enquiry->guardian_name }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-600">
                                        {{ $enquiry->standard->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-semibold text-gray-800">
                                        ₹{{ number_format($enquiry->admission_fee, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($enquiry->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                                Pending
                                            </span>
                                        @elseif($enquiry->status === 'updated')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                Updated
                                            </span>
                                        @elseif($enquiry->status === 'admitted')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                                Admitted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <x-button flat xs icon="eye" emerald
                                                wire:click="viewEnquiry({{ $enquiry->id }})"
                                                title="View" />
                                            <x-button flat xs icon="pencil" emerald
                                                wire:click="editEnquiry({{ $enquiry->id }})"
                                                title="Edit" />
                                            <x-button flat xs icon="arrow-up-tray" sky
                                                wire:click="openUpdateModal({{ $enquiry->id }})"
                                                title="Update Result" />
                                            <x-button flat xs icon="trash" red
                                                wire:click="deleteEnquiry({{ $enquiry->id }})"
                                                title="Delete" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-14 text-center">
                                        <x-icon name="user-plus" class="w-12 h-12 text-gray-200 mx-auto mb-3" />
                                        <p class="text-gray-400 text-sm font-medium">No admission enquiries found.</p>
                                        <p class="text-gray-300 text-xs mt-1">Click "Add Student" to create one.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($enquiries->hasPages())
                    <div class="px-4 py-3 border-t border-gray-100">
                        {{ $enquiries->links() }}
                    </div>
                @endif
            </div>

        @endif

        {{-- ── TAB 2: EXAM PAPERS ────────────────────────────────────────────── --}}
        @if($activeTab === 'exam-papers')

            {{-- Filter --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-1 max-w-xs">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                            Filter by Class
                        </label>
                        <select wire:model.live="filterPaperStandard"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">All Classes</option>
                            @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Papers Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-emerald-800 flex items-center gap-2">
                        <x-icon name="document-text" class="w-4 h-4 text-emerald-600" />
                        Uploaded Exam Papers
                    </h2>
                    <span class="text-xs text-emerald-600 bg-white px-2.5 py-1 rounded-full border border-emerald-200 font-semibold">
                        {{ $examPapers->count() }} Papers
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">#</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Class</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Title</th>
                                <th class="px-5 py-3 text-left font-semibold text-gray-600">Uploaded On</th>
                                <th class="px-5 py-3 text-center font-semibold text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($examPapers as $index => $paper)
                                <tr class="hover:bg-emerald-50/20 transition">
                                    <td class="px-5 py-3 text-gray-400 text-xs">{{ $index + 1 }}</td>
                                    <td class="px-5 py-3 font-medium text-gray-800">
                                        {{ $paper->standard->name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-600">
                                        {{ $paper->title ?? 'Exam Paper' }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-500 text-xs">
                                        {{ $paper->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-center gap-1">
                                            <x-button flat xs icon="pencil" emerald
                                                wire:click="openEditPaperModal({{ $paper->id }})"
                                                title="Edit" />
                                            <x-button flat xs icon="arrow-down-tray" sky
                                                wire:click="downloadExamPaper({{ $paper->id }})"
                                                title="Download" />
                                            <x-button flat xs icon="trash" red
                                                wire:click="deleteExamPaper({{ $paper->id }})"
                                                title="Delete" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-14 text-center">
                                        <x-icon name="document-text" class="w-12 h-12 text-gray-200 mx-auto mb-3" />
                                        <p class="text-gray-400 text-sm font-medium">No exam papers uploaded yet.</p>
                                        <p class="text-gray-300 text-xs mt-1">Click "Upload Paper" to add one.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        @endif

    </div>
    {{-- end page content --}}


    {{-- ═══════════════════════════════════════════════════════════════════════════
         ADD / EDIT ENQUIRY MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($enquiryModalOpen)
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]"
                 wire:click.stop>
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800">
                        {{ $editEnquiryId ? 'Edit Student Enquiry' : 'Add Student Enquiry' }}
                    </h3>
                    <button type="button" wire:click="$set('enquiryModalOpen', false)"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Student Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="studentName" placeholder="Full name"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('studentName')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Email</label>
                            <input type="email" wire:model="email" placeholder="student@example.com"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('email')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Mobile <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="mobile" placeholder="Mobile number"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('mobile')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Guardian Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="guardianName" placeholder="Parent / Guardian name"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('guardianName')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Address</label>
                        <textarea wire:model="address" rows="2" placeholder="Full address"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        @error('address')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">Class</label>
                            <select wire:model.live="standardId"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Select Class</option>
                                @foreach($standards as $std)
                                    <option value="{{ $std->id }}">{{ $std->name }}</option>
                                @endforeach
                            </select>
                            @error('standardId')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Stream <span class="text-xs font-normal text-gray-400">(optional)</span>
                            </label>
                            <input type="text" wire:model="stream" placeholder="e.g. Science, Arts"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Admission Fee <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 text-sm">₹</span>
                                <input type="number" wire:model="admissionFee" placeholder="0.00" step="0.01"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 pl-7" />
                            </div>
                            @error('admissionFee')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50 rounded-b-2xl">
                    <x-button flat label="Cancel" wire:click="$set('enquiryModalOpen', false)" />
                    <x-button emerald label="{{ $editEnquiryId ? 'Update' : 'Save' }}" icon="check"
                        wire:click="saveEnquiry" />
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
         UPDATE RESULT MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($updateModalOpen)
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]"
                 wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800">Update Student Result</h3>
                    <button type="button" wire:click="$set('updateModalOpen', false)"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Total Marks <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="totalMarks" placeholder="e.g. 500" step="0.01"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('totalMarks')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                                Obtained Marks <span class="text-red-500">*</span>
                            </label>
                            <input type="number" wire:model="obtainedMarks" placeholder="e.g. 420" step="0.01"
                                class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            @error('obtainedMarks')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Remarks</label>
                        <textarea wire:model="remarks" rows="3" placeholder="Any remarks about the student…"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                        @error('remarks')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Result PDF
                            <span class="text-xs font-normal text-gray-400">(optional, max 10 MB)</span>
                        </label>
                        <input type="file" wire:model="resultPdf" accept=".pdf"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700
                                   hover:file:bg-emerald-100 cursor-pointer" />
                        @error('resultPdf')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="resultPdf" class="mt-2 flex items-center gap-2 text-xs text-emerald-600">
                            <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Uploading…
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50 rounded-b-2xl">
                    <x-button flat label="Cancel" wire:click="$set('updateModalOpen', false)" />
                    <x-button emerald label="Save Result" icon="check" wire:click="saveUpdate" />
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
         VIEW ENQUIRY MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($viewModalOpen)
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]"
                 wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800">Student Enquiry Details</h3>
                    <button type="button" wire:click="$set('viewModalOpen', false)"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 px-6 py-5">
                    @if(!empty($viewEnquiryData))
                        <div class="space-y-5">
                            {{-- Status badge --}}
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-400">Added on {{ $viewEnquiryData['created_at'] }}</span>
                                @if($viewEnquiryData['status'] === 'pending')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Pending</span>
                                @elseif($viewEnquiryData['status'] === 'updated')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Updated</span>
                                @elseif($viewEnquiryData['status'] === 'admitted')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Admitted</span>
                                @endif
                            </div>

                            {{-- Info grid --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Student Name</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['student_name'] }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Guardian</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['guardian_name'] }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Email</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['email'] ?: '—' }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Mobile</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['mobile'] }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Class</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['class'] ?: '—' }}</p>
                                </div>
                                <div class="p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Stream</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['stream'] ?: '—' }}</p>
                                </div>
                                <div class="col-span-2 p-3 bg-gray-50 rounded-lg">
                                    <p class="text-xs text-gray-400 uppercase tracking-wide">Address</p>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['address'] ?: '—' }}</p>
                                </div>
                            </div>

                            {{-- Fee --}}
                            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-between">
                                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Admission Fee</p>
                                <p class="text-xl font-bold text-emerald-700">
                                    ₹{{ number_format($viewEnquiryData['admission_fee'], 2) }}
                                </p>
                            </div>

                            {{-- Result (if updated/admitted) --}}
                            @if($viewEnquiryData['status'] === 'updated' || $viewEnquiryData['total_marks'])
                                <div class="border-t border-gray-200 pt-4">
                                    <h3 class="text-xs font-bold text-blue-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>
                                        Exam Result
                                    </h3>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-3 bg-blue-50 rounded-lg">
                                            <p class="text-xs text-gray-400 uppercase tracking-wide">Total Marks</p>
                                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['total_marks'] ?? '—' }}</p>
                                        </div>
                                        <div class="p-3 bg-blue-50 rounded-lg">
                                            <p class="text-xs text-gray-400 uppercase tracking-wide">Obtained Marks</p>
                                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['obtained_marks'] ?? '—' }}</p>
                                        </div>
                                        @if($viewEnquiryData['total_marks'] && $viewEnquiryData['obtained_marks'])
                                            <div class="p-3 bg-blue-50 rounded-lg">
                                                <p class="text-xs text-gray-400 uppercase tracking-wide">Percentage</p>
                                                <p class="text-sm font-semibold text-gray-800 mt-0.5">
                                                    {{ number_format(($viewEnquiryData['obtained_marks'] / $viewEnquiryData['total_marks']) * 100, 2) }}%
                                                </p>
                                            </div>
                                        @endif
                                        <div class="col-span-2 p-3 bg-blue-50 rounded-lg">
                                            <p class="text-xs text-gray-400 uppercase tracking-wide">Remarks</p>
                                            <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $viewEnquiryData['remarks'] ?: '—' }}</p>
                                        </div>
                                    </div>

                                    @if($viewEnquiryData['result_pdf'])
                                        <div class="mt-3">
                                            <x-button flat xs icon="arrow-down-tray" emerald label="Download Result PDF"
                                                wire:click="downloadResultPdf({{ $viewEnquiryData['id'] }})" />
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end bg-gray-50/50 rounded-b-2xl">
                    <x-button flat label="Close" wire:click="$set('viewModalOpen', false)" />
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
         UPLOAD EXAM PAPER MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($paperModalOpen)
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]"
                 wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800">Upload Exam Paper</h3>
                    <button type="button" wire:click="closePaperModal"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">
                    {{-- Class --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="paperStandardId"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select Class</option>
                            @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                        @error('paperStandardId')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Title <span class="text-xs font-normal text-gray-400">(optional)</span>
                        </label>
                        <input type="text" wire:model="paperTitle" placeholder="e.g. Math Entrance Test"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    </div>

                    {{-- PDF File --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            PDF File <span class="text-red-500">*</span>
                        </label>
                        <input type="file" wire:model="paperFile" accept=".pdf"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700
                                   hover:file:bg-emerald-100 cursor-pointer" />
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                            <x-icon name="information-circle" class="w-3.5 h-3.5 text-gray-400" />
                            Maximum file size: <strong>1 MB</strong> (PDF only)
                        </p>
                        @error('paperFile')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="paperFile" class="mt-2 flex items-center gap-2 text-xs text-emerald-600">
                            <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Uploading…
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50 rounded-b-2xl">
                    <x-button flat label="Cancel" wire:click="closePaperModal" />
                    <x-button emerald label="Upload Paper" icon="arrow-up-tray"
                        wire:click="saveExamPaper"
                        wire:loading.attr="disabled" />
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
         EDIT EXAM PAPER MODAL
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($editPaperModalOpen)
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4 py-6"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg flex flex-col max-h-[90vh]"
                 wire:click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-bold text-gray-800">Edit Exam Paper</h3>
                    <button type="button" wire:click="closeEditPaperModal"
                        class="text-gray-400 hover:text-gray-600 transition">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                <div class="overflow-y-auto flex-1 px-6 py-5 space-y-5">
                    {{-- Class --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="editPaperStandardId"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select Class</option>
                            @foreach($standards as $std)
                                <option value="{{ $std->id }}">{{ $std->name }}</option>
                            @endforeach
                        </select>
                        @error('editPaperStandardId')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Title <span class="text-xs font-normal text-gray-400">(optional)</span>
                        </label>
                        <input type="text" wire:model="editPaperTitle" placeholder="e.g. Math Entrance Test"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                    </div>

                    {{-- Replace PDF --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                            Replace PDF
                            <span class="text-xs font-normal text-gray-400">(leave blank to keep existing)</span>
                        </label>
                        <input type="file" wire:model="editPaperFile" accept=".pdf"
                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700
                                   hover:file:bg-emerald-100 cursor-pointer" />
                        <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                            <x-icon name="information-circle" class="w-3.5 h-3.5 text-gray-400" />
                            Maximum file size: <strong>1 MB</strong> (PDF only)
                        </p>
                        @error('editPaperFile')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        <div wire:loading wire:target="editPaperFile" class="mt-2 flex items-center gap-2 text-xs text-emerald-600">
                            <svg class="animate-spin h-3 w-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                            Uploading…
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2 bg-gray-50/50 rounded-b-2xl">
                    <x-button flat label="Cancel" wire:click="closeEditPaperModal" />
                    <x-button emerald label="Save Changes" icon="check"
                        wire:click="saveEditPaper"
                        wire:loading.attr="disabled" />
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
         DELETE ENQUIRY CONFIRM
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($pendingDeleteEnquiryId)
        <div class="fixed inset-0 z-[1000] flex items-center justify-center px-4"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" wire:click.stop>
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="trash" class="w-7 h-7 text-red-500" />
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Delete Enquiry?</h3>
                <p class="text-sm text-gray-500 mb-5">This action cannot be undone.</p>
                <div class="flex justify-center gap-3">
                    <x-button flat label="Cancel" wire:click="cancelDeleteEnquiry" />
                    <x-button red label="Delete" wire:click="doDeleteEnquiry" />
                </div>
            </div>
        </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════════════════
         DELETE EXAM PAPER CONFIRM
    ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($pendingDeletePaperId)
        <div class="fixed inset-0 z-[1000] flex items-center justify-center px-4"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center" wire:click.stop>
                <div class="w-14 h-14 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="document-minus" class="w-7 h-7 text-red-500" />
                </div>
                <h3 class="text-base font-bold text-gray-800 mb-1">Delete Exam Paper?</h3>
                <p class="text-sm text-gray-500 mb-5">The PDF file will also be removed. This cannot be undone.</p>
                <div class="flex justify-center gap-3">
                    <x-button flat label="Cancel" wire:click="cancelDeletePaper" />
                    <x-button red label="Delete" wire:click="doDeleteExamPaper" />
                </div>
            </div>
        </div>
    @endif

</div>
