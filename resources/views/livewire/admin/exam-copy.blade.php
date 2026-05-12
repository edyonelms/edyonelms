<div class="p-4">
    <!-- Tabs -->
    <div class="flex justify-center items-center mb-6">
        <div class="flex gap-2 bg-white p-1 rounded-lg shadow-md">
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'list' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('list')">All Exam Copies</button>
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'view' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('view')">View Student Copies</button>
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'upload' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('upload')">Upload PDFs</button>
        </div>
    </div>

    <!-- List Tab Content -->
    @if ($activeTab === 'list')
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Exam Copies Card -->
            <x-card class="bg-white border-l-4 border-l-blue-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Exam Copies</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalExamCopies }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <!-- Total Students Card -->
            <x-card class="bg-white border-l-4 border-l-green-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Students</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalStudents }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <!-- Uploaded Copies Card -->
            <x-card class="bg-white border-l-4 border-l-purple-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Uploaded PDFs</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $uploadedCopies }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                    </div>
                </div>
            </x-card>

            <!-- Pending Uploads Card -->
            <x-card class="bg-white border-l-4 border-l-orange-500 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Uploads</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $pendingUploads }}</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Filter Exam Copy Records</h3>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Standard</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Section</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PDF Status</th>
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
                                    <div class="text-xs text-gray-500">
                                        Roll: {{ $examCopy->studentDetail->roll_no ?? 'N/A' }}
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
                                    @if ($examCopy->pdf_path)
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <x-icon name="document-check" class="w-3 h-3 mr-1" />
                                            Uploaded
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <x-icon name="clock" class="w-3 h-3 mr-1" />
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button wire:click="onView({{ $examCopy->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="View Details">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </button>
                                        @if ($examCopy->pdf_path)
                                            <button wire:click="onDownloadPdf({{ $examCopy->id }})"
                                                class="text-green-600 hover:text-green-900 transition-colors"
                                                title="Download PDF">
                                                <x-icon name="folder-arrow-down" class="w-4 h-4" />
                                            </button>
                                        @endif
                                        <button wire:click="onDelete({{ $examCopy->id }})"
                                            class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-icon name="magnifying-glass" class="w-12 h-12 text-gray-400 mb-2" />
                                        <p class="text-lg font-medium text-gray-600">No exam copy records found</p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            @if ($search || $filterExam || $filterStandard || $filterSection || $filterSubject)
                                                No results found for current filters
                                            @else
                                                No exam copy records available
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
            <h3 class="text-lg font-semibold mb-4">Search Student Exam Copies</h3>
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
                            <option value="{{ $student->id }}">{{ $student->user->name }} (Roll:
                                {{ $student->roll_no }})</option>
                        @endforeach
                    </select>
                    @if (!$selectedSection)
                        <p class="text-xs text-gray-500 mt-1">Please select a section first</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Results -->
        @if (count($studentPerformance) > 0)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    @php
                        $student = $students->firstWhere('id', $selectedStudent);
                    @endphp
                    <h3 class="text-lg font-semibold">Exam Copy Results</h3>
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
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PDF Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded At
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($studentPerformance as $performance)
                                <tr>
                                    <td class="px-6 py-4">{{ $performance['subject']['name'] ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">
                                        @if ($performance['pdf_path'])
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <x-icon name="document-check" class="w-3 h-3 mr-1" />
                                                Uploaded
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <x-icon name="clock" class="w-3 h-3 mr-1" />
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $performance['updated_at'] ? \Carbon\Carbon::parse($performance['updated_at'])->format('d M Y, h:i A') : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($performance['pdf_path'])
                                            <button wire:click="onDownloadPdf({{ $performance['id'] }})"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="Download PDF">
                                                <x-icon name="folder-arrow-down" class="w-4 h-4" />
                                            </button>
                                        @else
                                            <span class="text-gray-400">No PDF</span>
                                        @endif
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
                    <p class="text-yellow-700">No exam copy records found for the selected criteria.</p>
                </div>
            </div>
        @endif
    @endif

    <!-- Upload PDFs Tab Content -->
    @if ($activeTab === 'upload')
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Upload Exam Copy PDFs</h3>

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
                <!-- PDF Upload Table -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold mb-4">Upload PDFs for Students</h4>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Roll No</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Student Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Current Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Upload PDF</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Remarks</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($studentPdfs as $studentId => $pdf)
                                    <tr>
                                        <td class="px-4 py-3">
                                            {{ $pdf['roll_no'] }}
                                        </td>
                                        <td class="px-4 py-3">
                                            {{ $pdf['student_name'] }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($pdf['pdf_path'])
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Uploaded
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="file" wire:model="uploadedFiles.{{ $studentId }}"
                                                accept=".pdf"
                                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                                            @error("uploadedFiles.$studentId")
                                                <span class="text-xs text-red-500">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text"
                                                wire:model.live="studentPdfs.{{ $studentId }}.remarks"
                                                class="w-full px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Optional remarks">
                                        </td>
                                        <td class="px-4 py-3">
                                            @if ($pdf['pdf_path'])
                                                <button wire:click="deletePdf({{ $studentId }})"
                                                    class="text-red-600 hover:text-red-900 transition-colors"
                                                    title="Delete PDF">
                                                    <x-icon name="trash" class="w-4 h-4" />
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button wire:click="uploadPdfs" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-gradient-3 text-white rounded-md hover:bg-gradient-3-hover transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="uploadPdfs">Upload All PDFs</span>
                        <span wire:loading wire:target="uploadPdfs">Uploading...</span>
                    </button>
                </div>
            @else
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 text-center">
                    <div class="flex items-center justify-center">
                        <x-icon name="information-circle" class="w-5 h-5 text-blue-500 mr-2" />
                        <p class="text-blue-700">Please select Exam, Standard, Section, and Subject to upload PDFs.</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

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
                                        <p class="text-xs text-gray-600 mt-1">
                                            Roll No: {{ $sliderData['exam_copy']->studentDetail->roll_no ?? 'N/A' }}
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

                                <!-- PDF Status -->
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h3 class="text-sm font-medium text-gray-500 mb-1">PDF Status</h3>
                                    @if ($sliderData['exam_copy']->pdf_path)
                                        <div class="flex items-center mt-2">
                                            <span
                                                class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                                <x-icon name="document-check" class="w-4 h-4 mr-1" />
                                                PDF Uploaded
                                            </span>
                                            <span class="ml-3 text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($sliderData['exam_copy']->updated_at)->format('d M Y, h:i A') }}
                                            </span>
                                        </div>
                                    @else
                                        <span
                                            class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <x-icon name="clock" class="w-4 h-4 mr-1" />
                                            Pending Upload
                                        </span>
                                    @endif
                                </div>

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
                        @if (isset($sliderData['exam_copy']) && $sliderData['exam_copy']->pdf_path)
                            <button wire:click="onDownloadPdf({{ $sliderData['exam_copy']->id }})"
                                class="w-full sm:w-auto px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors order-1 sm:order-2">
                                <x-icon name="folder-arrow-down" class="w-4 h-4 inline mr-1" />
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
