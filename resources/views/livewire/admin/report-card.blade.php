<div class="p-4">
    {{-- Notification component --}}
    <x-notifications />
    <x-dialog />

    @if ($viewMode === 'list')
        {{-- ==================== LIST VIEW ==================== --}}

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-sm border mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Report Card Management</h2>
                <p class="text-gray-600 mt-1">Issue, manage and download academic report cards</p>
            </div>
            <button wire:click="openIssueScreen"
                class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <x-icon name="document-plus" class="h-5 w-5" />
                Issue Report Card
            </button>
        </div>

        {{-- Analytics Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Students --}}
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <x-icon name="users" class="h-6 w-6 text-indigo-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->analytics['total_students'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Active Students --}}
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <x-icon name="user-group" class="h-6 w-6 text-blue-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Active Students</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $this->analytics['active_students'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Issued --}}
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <x-icon name="check-circle" class="h-6 w-6 text-green-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Issued</p>
                        <p class="text-2xl font-bold text-green-600">{{ $this->analytics['issued'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Pending --}}
            <div class="bg-white rounded-xl shadow-sm border p-5">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <x-icon name="clock" class="h-6 w-6 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-amber-600">{{ $this->analytics['pending'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Search by student name or admission number..."
                            class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm">
                    </div>
                </div>

                {{-- Class Filter --}}
                <div>
                    <x-native-select wire:model.live="filterStandard" label="Class">
                        <option value="">All Classes</option>
                        @foreach ($this->standards as $standard)
                            <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>

                {{-- Section Filter --}}
                <div>
                    <x-native-select wire:model.live="filterSection" label="Section" :disabled="!$filterStandard">
                        <option value="">All Sections</option>
                        @foreach ($this->filterSections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <x-native-select wire:model.live="filterStatus" label="Status">
                        <option value="">All Status</option>
                        <option value="issued">Issued</option>
                        <option value="revoked">Revoked</option>
                    </x-native-select>
                </div>
            </div>

            <div class="flex justify-between items-center mt-4">
                <div class="flex gap-3 items-end">
                    <x-native-select wire:model.live="perPage" label="Show" class="w-32">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </x-native-select>

                    @if ($search || $filterStandard || $filterSection || $filterStatus)
                        <x-button wire:click="resetFilters" flat sm label="Clear Filters" icon="x-mark" />
                    @endif
                </div>

                <div class="text-sm text-gray-500">
                    Total: {{ $reportCards->total() }} report cards
                </div>
            </div>
        </div>

        {{-- Report Cards Table --}}
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">S.No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Class & Section</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Admission No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Academic Year</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Issued On</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($reportCards as $index => $card)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $reportCards->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-indigo-700 font-semibold text-xs">
                                                {{ strtoupper(substr($card->studentDetail->full_name ?? 'N', 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $card->studentDetail->full_name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $card->studentDetail->standard->name ?? '' }} - {{ $card->studentDetail->section->name ?? '' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600 font-mono">
                                    {{ $card->studentDetail->admission_no ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $card->academic_year ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($card->status === 'issued')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                            Issued
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                                            Revoked
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $card->issued_at ? $card->issued_at->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($card->status === 'issued')
                                            {{-- Download --}}
                                            <a href="{{ route('admin.report-card.download', ['organization' => auth()->user()->organization_id, 'id' => $card->id]) }}"
                                                target="_blank"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors"
                                                title="Download PDF">
                                                <x-icon name="arrow-down-tray" class="h-4 w-4" />
                                            </a>

                                            {{-- Print --}}
                                            <a href="{{ route('admin.report-card.print', ['organization' => auth()->user()->organization_id, 'id' => $card->id]) }}"
                                                target="_blank"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-100 transition-colors"
                                                title="Print">
                                                <x-icon name="printer" class="h-4 w-4" />
                                            </a>

                                            {{-- Revoke --}}
                                            <button wire:click="revokeReportCard({{ $card->id }})"
                                                wire:confirm="Are you sure you want to revoke this report card?"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 transition-colors"
                                                title="Revoke">
                                                <x-icon name="x-circle" class="h-4 w-4" />
                                            </button>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Revoked</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <x-icon name="document-text" class="h-12 w-12 text-gray-300" />
                                        <p class="text-gray-500 font-medium">No report cards found</p>
                                        <p class="text-gray-400 text-sm">Issue report cards to see them here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($reportCards->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $reportCards->links() }}
                </div>
            @endif
        </div>

    @else
        {{-- ==================== ISSUE REPORT CARD SCREEN ==================== --}}

        {{-- Header with back button --}}
        <div class="flex items-center gap-4 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-sm border mb-6">
            <button wire:click="backToList"
                class="flex items-center justify-center w-10 h-10 rounded-lg bg-white shadow-sm border hover:bg-gray-50 transition-colors">
                <x-icon name="arrow-left" class="h-5 w-5 text-gray-600" />
            </button>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Issue Report Cards</h2>
                <p class="text-gray-600 mt-1">Select class and section, then choose students to issue report cards</p>
            </div>
        </div>

        {{-- Step 1: Select Class and Section --}}
        <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                Select Class & Section
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <x-native-select wire:model.live="issueStandard" label="Class *">
                        <option value="">-- Select Class --</option>
                        @foreach ($this->standards as $standard)
                            <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>

                <div>
                    <x-native-select wire:model.live="issueSection" label="Section *" :disabled="!$issueStandard">
                        <option value="">-- Select Section --</option>
                        @foreach ($this->issueSections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>

                <div>
                    <x-button
                        wire:click="loadStudents"
                        primary
                        label="Load Students"
                        icon="magnifying-glass"
                        :disabled="!$issueStandard || !$issueSection"
                        class="!bg-indigo-600 hover:!bg-indigo-700"
                    />
                </div>
            </div>
        </div>

        {{-- Step 2: Student List --}}
        @if ($issueStudentsLoaded)
            <div class="bg-white rounded-xl shadow-sm border mb-6">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-7 h-7 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            Select Students
                        </h3>

                        @php
                            $eligibleCount = $this->issueStudents->filter(fn($s) => $s['marks_complete'] && !$s['already_issued'])->count();
                        @endphp

                        @if ($eligibleCount > 0)
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-gray-500">{{ count($selectedStudents) }} of {{ $eligibleCount }} eligible selected</span>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox"
                                        wire:click="toggleAllEligible($event.target.checked)"
                                        {{ count($selectedStudents) === $eligibleCount && $eligibleCount > 0 ? 'checked' : '' }}
                                        class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm font-medium text-gray-700">Select All Eligible</span>
                                </label>
                            </div>
                        @endif
                    </div>

                    {{-- Legend --}}
                    <div class="flex flex-wrap gap-4 mt-3 text-xs">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-green-400"></span>
                            <span class="text-gray-600">Eligible (marks complete)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-gray-300"></span>
                            <span class="text-gray-600">Marks incomplete</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-blue-400"></span>
                            <span class="text-gray-600">Already issued</span>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12"></th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">S.No</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Admission No.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Roll No.</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Marks Status</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Report Card</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($this->issueStudents as $index => $student)
                                @php
                                    $isEligible = $student['marks_complete'] && !$student['already_issued'];
                                    $rowClass = !$student['marks_complete'] ? 'opacity-50 bg-gray-50' : ($student['already_issued'] ? 'bg-blue-50/30' : '');
                                @endphp
                                <tr class="{{ $rowClass }} hover:bg-gray-50/50 transition-colors">
                                    {{-- Checkbox --}}
                                    <td class="px-4 py-3">
                                        @if ($isEligible)
                                            <input type="checkbox"
                                                wire:model.live="selectedStudents"
                                                value="{{ $student['id'] }}"
                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        @else
                                            <input type="checkbox" disabled
                                                class="w-4 h-4 rounded border-gray-200 text-gray-300 cursor-not-allowed">
                                        @endif
                                    </td>

                                    {{-- Serial --}}
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>

                                    {{-- Name --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                {{ $student['marks_complete'] ? 'bg-indigo-100' : 'bg-gray-200' }}">
                                                <span class="{{ $student['marks_complete'] ? 'text-indigo-700' : 'text-gray-500' }} font-semibold text-xs">
                                                    {{ strtoupper(substr($student['full_name'], 0, 1)) }}
                                                </span>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $student['full_name'] }}</span>
                                        </div>
                                    </td>

                                    {{-- Admission No --}}
                                    <td class="px-4 py-3 text-sm text-gray-600 font-mono">{{ $student['admission_no'] ?? 'N/A' }}</td>

                                    {{-- Roll No --}}
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $student['roll_no'] }}</td>

                                    {{-- Marks Status --}}
                                    <td class="px-4 py-3">
                                        @if ($student['marks_complete'])
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <x-icon name="check-circle" class="h-3.5 w-3.5" />
                                                Complete
                                            </span>
                                        @else
                                            <div>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                                    <x-icon name="exclamation-triangle" class="h-3.5 w-3.5" />
                                                    Incomplete
                                                </span>
                                                @if ($student['missing_info'])
                                                    <p class="text-[11px] text-gray-400 mt-1">{{ $student['missing_info'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </td>

                                    {{-- Report Card Status --}}
                                    <td class="px-4 py-3">
                                        @if ($student['already_issued'])
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <x-icon name="document-check" class="h-3.5 w-3.5" />
                                                Already Issued
                                            </span>
                                        @elseif ($student['marks_complete'])
                                            <span class="text-xs text-gray-500">Ready to issue</span>
                                        @else
                                            <span class="text-xs text-gray-400">--</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <x-icon name="users" class="h-12 w-12 text-gray-300" />
                                            <p class="text-gray-500 font-medium">No students found</p>
                                            <p class="text-gray-400 text-sm">No students are enrolled in this class and section.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Issue Button --}}
                @if ($this->issueStudents->isNotEmpty())
                    <div class="p-6 border-t border-gray-200 bg-gray-50 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <span class="font-medium">{{ count($selectedStudents) }}</span> student(s) selected
                        </div>
                        <x-button
                            wire:click="issueReportCards"
                            primary
                            label="Issue Report Cards"
                            icon="document-check"
                            :disabled="empty($selectedStudents)"
                            class="!bg-gradient-to-r !from-indigo-600 !to-purple-600 hover:!from-indigo-700 hover:!to-purple-700"
                            wire:loading.attr="disabled"
                        >
                            <span wire:loading wire:target="issueReportCards" class="mr-2">
                                <svg class="animate-spin h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                            </span>
                        </x-button>
                    </div>
                @endif
            </div>
        @endif

    @endif
</div>
