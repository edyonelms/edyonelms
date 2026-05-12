<div class="p-4">
    <!-- Tabs -->
    <div class="flex justify-center items-center mb-6">
        <div class="flex gap-2 bg-white p-1 rounded-lg shadow-md">
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'list' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('list')">All Performers</button>
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'view' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('view')">View Specific Performance</button>
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'upload' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('upload')">Upload Marks</button>
        </div>
    </div>

    <div class="p-4">
        <!-- List Tab Content -->
        @if ($activeTab === 'list')
            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Filter Exam Records</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <x-input wire:model.live.debounce.300ms="search" placeholder="Search by student or exam..."
                            icon="magnifying-glass" />
                    </div>

                    <!-- Exam Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                        <select wire:model.live="filterExam"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Exams</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Standard Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Standard</label>
                        <select wire:model.live="filterStandard"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Standards</option>
                            @foreach ($standards as $standard)
                                <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select wire:model.live="filterSection"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Sections</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <select wire:model.live="filterSubject"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <x-native-select wire:model.live="perPage" class="text-sm">
                            <option value="10">10 per page</option>
                            <option value="25">25 per page</option>
                            <option value="50">50 per page</option>
                        </x-native-select>
                    </div>
                    <div class="text-sm text-gray-500">
                        @if ($examCopies->count() > 0)
                            Showing {{ $examCopies->firstItem() }} - {{ $examCopies->lastItem() }} of
                            {{ $examCopies->total() }} records
                        @else
                            No records found
                        @endif
                    </div>
                </div>
            </div>

            <!-- Exam Copies Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Standard
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marks</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($examCopies as $examCopy)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $examCopy->studentDetail->user->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $examCopy->exam->exam_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $examCopy->standard->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $examCopy->section->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $examCopy->subject->name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $examCopy->marks_obtained }}/{{ $examCopy->max_marks }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $examCopy->grade === 'A' || $examCopy->grade === 'A+'
                                                ? 'bg-green-100 text-green-800'
                                                : ($examCopy->grade === 'B' || $examCopy->grade === 'B+'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : ($examCopy->grade === 'C' || $examCopy->grade === 'C+'
                                                        ? 'bg-yellow-100 text-yellow-800'
                                                        : ($examCopy->grade === 'D'
                                                            ? 'bg-orange-100 text-orange-800'
                                                            : 'bg-red-100 text-red-800'))) }}">
                                            {{ $examCopy->grade }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <button wire:click="onView({{ $examCopy->id }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="View Details">
                                                <x-icon name="eye" class="w-4 h-4" />
                                            </button>
                                            <button wire:click="onDownloadPdf({{ $examCopy->id }}, 'single')"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Download PDF">
                                                <x-icon name="folder-arrow-down" class="w-4 h-4" />
                                            </button>
                                            <button wire:click="onDelete({{ $examCopy->id }})"
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Delete">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <x-icon name="magnifying-glass" class="w-12 h-12 text-gray-400 mb-2" />
                                            <p class="text-lg font-medium text-gray-600">No exam records found</p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                @if ($search || $filterExam || $filterStandard || $filterSection || $filterSubject)
                                                    No results found for current filters
                                                @else
                                                    No exam records available
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($examCopies->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $examCopies->links() }}
                    </div>
                @endif
            </div>
        @endif

        <!-- View Tab Content -->
        @if ($activeTab === 'view')
            <!-- Search Filters -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Search Student Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <!-- Exam Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                        <select wire:model.live="selectedExam"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Exam</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Standard Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Standard</label>
                        <select wire:model.live="selectedStandard"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Standard</option>
                            @foreach ($standards as $standard)
                                <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select wire:model.live="selectedSection"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            {{ !$selectedStandard ? 'disabled' : '' }}>
                            <option value="">Select Section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                        @if (!$selectedStandard)
                            <p class="text-xs text-gray-500 mt-1">Please select a standard first</p>
                        @endif
                    </div>

                    <!-- Student Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Student</label>
                        <select wire:model.live="selectedStudent"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            {{ !$selectedSection ? 'disabled' : '' }}>
                            <option value="">Select Student</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                            @endforeach
                        </select>
                        @if (!$selectedSection)
                            <p class="text-xs text-gray-500 mt-1">Please select a section first</p>
                        @endif
                    </div>
                </div>

                <div class="flex justify-end gap-3">
                    <button wire:click="searchPerformance"
                        class="px-6 py-2 bg-gradient-3 text-white rounded-md hover:bg-gradient-3-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                        {{ !$selectedExam || !$selectedStandard || !$selectedSection || !$selectedStudent }}>
                        <span wire:loading.remove>Search</span>
                        <span wire:loading>Searching...</span>
                    </button>
                    @if ($selectedExam && $selectedStudent && count($studentPerformance) > 0)
                        <button wire:click="onDownloadPdf(null, 'multiple')"
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                            Download All PDF
                        </button>
                    @endif
                </div>
            </div>

            <!-- Results -->
            @if (count($studentPerformance) > 0)
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        @php
                            $student = $students->firstWhere('id', $selectedStudent);
                        @endphp
                        <h3 class="text-lg font-semibold">Performance Results</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Showing results for: {{ $student->user->name ?? 'N/A' }}
                            (Roll No: {{ $student->roll_no ?? 'N/A' }})
                        </p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Marks
                                        Obtained</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Max
                                        Marks</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Percentage</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($studentPerformance as $performance)
                                    <tr>
                                        <td class="px-6 py-4">{{ $performance['subject']['name'] ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $performance['marks_obtained'] ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $performance['max_marks'] ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ $performance['percentage'] ?? 'N/A' }}%</td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $performance['grade'] === 'A' || $performance['grade'] === 'A+'
                                            ? 'bg-green-100 text-green-800'
                                            : ($performance['grade'] === 'B' || $performance['grade'] === 'B+'
                                                ? 'bg-blue-100 text-blue-800'
                                                : ($performance['grade'] === 'C' || $performance['grade'] === 'C+'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : ($performance['grade'] === 'D'
                                                        ? 'bg-orange-100 text-orange-800'
                                                        : 'bg-red-100 text-red-800'))) }}">
                                                {{ $performance['grade'] ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <button wire:click="onDownloadPdf({{ $performance['id'] }}, 'single')"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Download PDF">
                                                <x-icon name="folder-arrow-down" class="w-4 h-4" />
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @elseif($selectedExam && $selectedStudent)
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 text-center">
                    <div class="flex items-center justify-center">
                        <x-icon name="question-mark-circle" class="w-5 h-5 text-yellow-500 mr-2" />
                        <p class="text-yellow-700">No performance records found for the selected criteria.</p>
                    </div>
                </div>
            @endif
        @endif

        <!-- Upload Marks Tab Content -->
        @if ($activeTab === 'upload')
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Upload Marks</h3>

                <!-- Selection Form -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Exam Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                        <select wire:model.live="uploadExam"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Exam</option>
                            @foreach ($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Standard Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Standard</label>
                        <select wire:model.live="uploadStandard"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Standard</option>
                            @foreach ($standards as $standard)
                                <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select wire:model.live="uploadSection"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            {{ !$uploadStandard ? 'disabled' : '' }}>
                            <option value="">Select Section</option>
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>
                        @if (!$uploadStandard)
                            <p class="text-xs text-gray-500 mt-1">Please select a standard first</p>
                        @endif
                    </div>

                    <!-- Subject Dropdown -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <select wire:model.live="uploadSubject"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            {{ !$uploadSection ? 'disabled' : '' }}>
                            <option value="">Select Subject</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @if (!$uploadSection)
                            <p class="text-xs text-gray-500 mt-1">Please select a section first</p>
                        @endif
                    </div>
                </div>

                @if ($uploadExam && $uploadStandard && $uploadSection && $uploadSubject)
                    <!-- Marks Input Table -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold mb-4">Enter Marks for Students</h4>

                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Roll No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Student Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Marks Obtained</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Max
                                            Marks</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($studentMarks as $studentId => $marks)
                                        <tr>
                                            <td class="px-4 py-3">
                                                {{ $marks['roll_no'] }}
                                            </td>
                                            <td class="px-4 py-3">
                                                {{ $marks['student_name'] }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number"
                                                    wire:model.live="studentMarks.{{ $studentId }}.marks_obtained"
                                                    class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    min="0" step="0.01" placeholder="Enter marks">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number"
                                                    wire:model.live="studentMarks.{{ $studentId }}.max_marks"
                                                    class="w-24 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    min="1" step="1" value="100">
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text"
                                                    wire:model.live="studentMarks.{{ $studentId }}.remarks"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                    placeholder="Remarks">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button wire:click="uploadMarks" wire:loading.attr="disabled"
                            class="px-6 py-2 bg-gradient-3 text-white rounded-md hover:bg-gradient-3-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove>Save All Marks</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                @else
                    <div class="bg-blue-50 border border-blue-200 rounded-md p-4 text-center">
                        <div class="flex items-center justify-center">
                            <x-icon name="information-circle" class="w-5 h-5 text-blue-500 mr-2" />
                            <p class="text-blue-700">Please select Exam, Standard, Section, and Subject to enter marks.
                            </p>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal Slider Component -->
    @if ($showSlider)
        <div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="relative w-full max-w-3xl max-h-[calc(100vh-8rem)] mx-4 sm:mx-6 md:mx-8 my-4">
                <div title="{{ $sliderTitle }}"
                    class="relative z-10 bg-white/90 backdrop-blur-sm rounded-lg shadow-xl h-full flex flex-col min-h-[300px] max-h-full">
                    <!-- Header -->
                    <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 truncate pr-4">{{ $sliderTitle }}
                        </h3>
                        <button type="button" wire:click="closeSlider"
                            class="text-gray-400 hover:text-gray-600 transition flex-shrink-0 p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 sm:h-6 sm:w-6" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 sm:space-y-6">
                        @if (isset($sliderData['exam_copy']))
                            <div class="space-y-6">
                                <!-- Student Info -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 mb-1">Student</h3>
                                        <p class="text-base font-semibold text-gray-800">
                                            {{ $sliderData['exam_copy']->studentDetail->user->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 mb-1">Exam</h3>
                                        <p class="text-base font-semibold text-gray-800">
                                            {{ $sliderData['exam_copy']->exam->exam_name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Class Info -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 mb-1">Standard</h3>
                                        <p class="text-base font-semibold text-gray-800">
                                            {{ $sliderData['exam_copy']->standard->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 mb-1">Section</h3>
                                        <p class="text-base font-semibold text-gray-800">
                                            {{ $sliderData['exam_copy']->section->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 mb-1">Subject</h3>
                                        <p class="text-base font-semibold text-gray-800">
                                            {{ $sliderData['exam_copy']->subject->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Marks Info -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                                        <h3 class="text-sm font-medium text-blue-600 mb-1">Marks Obtained</h3>
                                        <p class="text-2xl font-bold text-blue-700">
                                            {{ $sliderData['exam_copy']->marks_obtained }}</p>
                                    </div>
                                    <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                                        <h3 class="text-sm font-medium text-green-600 mb-1">Max Marks</h3>
                                        <p class="text-2xl font-bold text-green-700">
                                            {{ $sliderData['exam_copy']->max_marks }}</p>
                                    </div>
                                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                                        <h3 class="text-sm font-medium text-purple-600 mb-1">Percentage</h3>
                                        <p class="text-2xl font-bold text-purple-700">
                                            {{ $sliderData['exam_copy']->percentage }}%</p>
                                    </div>
                                    <div class="bg-orange-50 p-4 rounded-lg border border-orange-100">
                                        <h3 class="text-sm font-medium text-orange-600 mb-1">Grade</h3>
                                        <p class="text-2xl font-bold text-orange-700">
                                            {{ $sliderData['exam_copy']->grade }}</p>
                                    </div>
                                </div>

                                <!-- Subject-wise Marks -->
                                @if (isset($sliderData['subject_marks']) && count($sliderData['subject_marks']) > 0)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-lg font-semibold text-gray-700 mb-4">Subject-wise Marks</h3>
                                        <div class="overflow-x-auto">
                                            <table class="w-full">
                                                <thead class="bg-gray-100">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-2 text-left text-sm font-medium text-gray-600">
                                                            Subject</th>
                                                        <th
                                                            class="px-4 py-2 text-left text-sm font-medium text-gray-600">
                                                            Marks</th>
                                                        <th
                                                            class="px-4 py-2 text-left text-sm font-medium text-gray-600">
                                                            Max Marks</th>
                                                        <th
                                                            class="px-4 py-2 text-left text-sm font-medium text-gray-600">
                                                            Percentage</th>
                                                        <th
                                                            class="px-4 py-2 text-left text-sm font-medium text-gray-600">
                                                            Grade</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach ($sliderData['subject_marks'] as $subjectMark)
                                                        <tr>
                                                            <td class="px-4 py-2">
                                                                {{ $subjectMark->subject->name ?? 'N/A' }}</td>
                                                            <td class="px-4 py-2">{{ $subjectMark->marks_obtained }}
                                                            </td>
                                                            <td class="px-4 py-2">{{ $subjectMark->max_marks }}</td>
                                                            <td class="px-4 py-2">{{ $subjectMark->percentage }}%</td>
                                                            <td class="px-4 py-2">{{ $subjectMark->grade }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif

                                <!-- Remarks -->
                                @if ($sliderData['exam_copy']->remarks)
                                    <div class="bg-gray-50 p-4 rounded-lg">
                                        <h3 class="text-sm font-medium text-gray-500 mb-1">Remarks</h3>
                                        <p class="text-base text-gray-800 whitespace-pre-line">
                                            {{ $sliderData['exam_copy']->remarks }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div
                        class="border-t border-gray-200 flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3 p-4 sm:px-6 sm:py-4 flex-shrink-0 bg-gray-50/50">
                        @if (isset($sliderData['exam_copy']))
                            <button wire:click="onDownloadPdf({{ $sliderData['exam_copy']->id }}, 'single')"
                                class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors order-1 sm:order-2">
                                Download PDF
                            </button>
                        @endif
                        <button wire:click="closeSlider"
                            class="w-full sm:w-auto px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors order-2 sm:order-1">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
