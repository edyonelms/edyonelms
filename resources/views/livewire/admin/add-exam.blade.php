<div class="p-4">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Exam Management</h2>
        <button wire:click="onAddExam"
            class="px-4 py-2 bg-gradient-3 text-white rounded-md hover:bg-gradient-4 flex items-center gap-2">
            <i class="fas fa-plus"></i>
            Add Exam
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Exams Card -->
        <div class="bg-white border-l-4 border-l-blue-500 shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Exams</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $totalExams }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <i class="fas fa-clipboard-list text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Published Exams Card -->
        <div class="bg-white border-l-4 border-l-green-500 shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Published Exams</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $publishedExams }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <i class="fas fa-eye text-green-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Upcoming Exams Card -->
        <div class="bg-white border-l-4 border-l-orange-500 shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Upcoming Exams</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $upcomingExams }}</p>
                </div>
                <div class="p-3 bg-orange-50 rounded-full">
                    <i class="fas fa-clock text-orange-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Exams Card -->
        <div class="bg-white border-l-4 border-l-purple-500 shadow-sm rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Exams</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $activeExams }}</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-full">
                    <i class="fas fa-play-circle text-purple-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4">Filter Exams</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by exam name..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Academic Year Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                <select wire:model.live="filterAcademicYear"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Years</option>
                    @foreach ($academicYearOptions as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Exam Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                <select wire:model.live="filterExamType"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    @foreach ($examTypes as $key => $type)
                        <option value="{{ $key }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="filterStatus"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="active">Active</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Per Page Selector -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Items Per Page</label>
                <select wire:model.live="perPage"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                </select>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500">
                @if ($exams->count() > 0)
                    Showing {{ $exams->firstItem() }} - {{ $exams->lastItem() }} of {{ $exams->total() }} exams
                @else
                    No exams found
                @endif
            </div>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Academic Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($exams as $exam)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $exam->exam_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $exam->uses_grading_system ? 'Grading System' : 'Marks System' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $exam->academic_year }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $examTypes[$exam->exam_type] ?? $exam->exam_type }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($exam->start_date)->format('d M') }} -
                                    {{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $exam->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $exam->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                    @php
                                        $now = now();
                                        $statusColor = '';
                                        $statusText = '';
                                        if ($now < $exam->start_date) {
                                            $statusColor = 'bg-blue-100 text-blue-800';
                                            $statusText = 'Upcoming';
                                        } elseif ($now >= $exam->start_date && $now <= $exam->end_date) {
                                            $statusColor = 'bg-green-100 text-green-800';
                                            $statusText = 'Active';
                                        } else {
                                            $statusColor = 'bg-gray-100 text-gray-800';
                                            $statusText = 'Completed';
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button wire:click="onViewExam({{ $exam->id }})"
                                        class="text-blue-600 hover:text-blue-900 transition-colors"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button wire:click="onEditExam({{ $exam->id }})"
                                        class="text-green-600 hover:text-green-900 transition-colors"
                                        title="Edit Exam">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="onTogglePublish({{ $exam->id }})"
                                        class="text-orange-600 hover:text-orange-900 transition-colors"
                                        title="{{ $exam->is_published ? 'Unpublish' : 'Publish' }}">
                                        <i class="fas {{ $exam->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                    </button>
                                    <button wire:click="onDeleteExam({{ $exam->id }})"
                                        class="text-red-600 hover:text-red-900 transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-search text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-lg font-medium text-gray-600">No exams found</p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        @if ($search || $filterAcademicYear || $filterExamType || $filterStatus)
                                            No results found for current filters
                                        @else
                                            No exams available. Create your first exam!
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
        @if ($exams->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $exams->links() }}
            </div>
        @endif
    </div>

    <!-- Add/Edit Modal -->
    <x-modal-form show="{{ $open }}" title="{{ $editId ? 'Edit Exam' : 'Add Exam' }}" submitAction="onSave"
        submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="resetForm">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-4">
            <x-input wire:model.defer="examName" label="Exam Name" required />

            <x-native-select wire:model.defer="examType" label="Exam Type" required>
                <option value="">Select Exam Type</option>
                @foreach ($examTypes as $key => $type)
                    <option value="{{ $key }}">{{ $type }}</option>
                @endforeach
            </x-native-select>

            <x-native-select wire:model="academicYear" label="Academic Year" required>
                @foreach ($academicYearOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </x-native-select>
            <x-datetime-picker without-time label="Start Date" wire:model.defer="startDate" required />

            <x-datetime-picker without-time label="End Date" wire:model.defer="endDate" required />

            <x-input type="number" wire:model.defer="totalMarks" label="Total Marks" required />

            <x-input type="number" wire:model.defer="passingMarks" label="Passing Marks" required />

            <div class="sm:col-span-2">
                <x-toggle wire:model.defer="isPublished" label="Publish Exam" />
            </div>
        </div>
    </x-modal-form>

    <!-- View Modal -->
    <x-view-modal :show="$showViewModal" :title="$viewModalTitle" closeAction="closeViewModal">
        <div class="space-y-4">
            @foreach ($viewData['details'] ?? [] as $label => $value)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-sm font-medium text-gray-500 mb-1">{{ $label }}</h3>
                    <p class="text-base font-semibold text-gray-800">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </x-view-modal>
</div>
</div>
