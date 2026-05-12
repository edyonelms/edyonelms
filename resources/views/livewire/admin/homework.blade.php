<div class="p-4">
    <div class="flex justify-between items-center p-4">
        <h1 class="text-3xl font-bold text-gray-700">Homework Management</h1>
        <button wire:click="onAddHomework()"
            class="px-4 py-2 bg-gradient-3 hover:bg-gradient-3-hover text-white rounded-lg">
            Add Homework
        </button>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 px-4">
        <x-card class="bg-white border-l-4 border-l-blue-500 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Homeworks</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['total'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full">
                    <x-icon name="document-text" class="w-6 h-6 text-blue-500" />
                </div>
            </div>
        </x-card>

        <x-card class="bg-white border-l-4 border-l-green-500 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">This Week</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['this_week'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full">
                    <x-icon name="calendar" class="w-6 h-6 text-green-500" />
                </div>
            </div>
        </x-card>

        <x-card class="bg-white border-l-4 border-l-purple-500 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Teachers</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['by_teacher'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-full">
                    <x-icon name="users" class="w-6 h-6 text-purple-500" />
                </div>
            </div>
        </x-card>

        <x-card class="bg-white border-l-4 border-l-orange-500 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Classes</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $statistics['by_class'] ?? 0 }}</p>
                </div>
                <div class="p-3 bg-orange-50 rounded-full">
                    <x-icon name="academic-cap" class="w-6 h-6 text-orange-500" />
                </div>
            </div>
        </x-card>
    </div>

    {{-- Add/Edit Modal --}}
    <x-modal-form show="{{ $open }}" title="{{ $editId ? 'Edit Homework' : 'Add Homework' }}"
        submitAction="onSave" submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="closeModal">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-4">
            {{-- Homework Title --}}
            <div class="sm:col-span-2">
                <x-input wire:model.defer="title" label="Homework Title" required />
            </div>

            {{-- Standard --}}
            <x-native-select label="Standard" wire:model.live="standard_id" required>
                <option value="">Select Standard</option>
                @foreach ($standards as $standard)
                    <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                @endforeach
            </x-native-select>

            {{-- Section (Optional) --}}
            <x-native-select label="Section (Optional)" wire:model.live="section_id">
                <option value="">Select Section</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}">{{ $section->name }}</option>
                @endforeach
            </x-native-select>

            {{-- Subject Selection Type --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Subject Selection</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model.live="subject_selection" value="single"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">Single Subject</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" wire:model.live="subject_selection" value="all"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-2 text-sm text-gray-700">All Subjects</span>
                    </label>
                </div>
            </div>

            {{-- Subject (Only for single selection) --}}
            @if ($subject_selection === 'single')
                <x-native-select label="Subject" wire:model.defer="subject_id" required>
                    <option value="">Select Subject</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </x-native-select>
            @else
                <div class="sm:col-span-2">
                    <div class="p-3 bg-blue-50 rounded-md">
                        <p class="text-sm text-blue-700">
                            <strong>Note:</strong> This homework will be assigned to <strong>ALL SUBJECTS</strong>
                            for the selected class{{ $section_id ? ' and section' : '' }}.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Description --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea wire:model.defer="description" rows="4" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                    placeholder="Enter homework description..."></textarea>
                @error('description')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- File Upload --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Attachment (Optional)</label>

                @if ($editId && !$homework_file)
                    @php $homework = \App\Models\Admin\HomeWork::find($editId) @endphp
                    @if ($homework && $homework->file)
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-blue-600">{{ basename($homework->file) }}</span>
                            <button wire:click="$set('homework_file', null)" type="button"
                                class="text-red-600 hover:text-red-800 text-sm">
                                Remove
                            </button>
                        </div>
                    @endif
                @endif

                @if ($tempFileUrl)
                    <span class="text-sm text-gray-600">{{ $tempFileUrl }}</span>
                @endif

                <input type="file" wire:model="homework_file"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png"
                    class="block w-full text-sm text-gray-500
                       file:mr-4 file:py-2 file:px-4
                       file:rounded-md file:border-0
                       file:text-sm file:font-semibold
                       file:bg-blue-50 file:text-blue-700
                       hover:file:bg-blue-100">
                @error('homework_file')
                    <span class="text-red-500 text-xs">{{ $message }}</span>
                @enderror
                <p class="text-xs text-gray-500 mt-1">
                    Allowed: PDF, Word, Excel, PowerPoint, Text, Images (Max: 10MB)
                </p>
            </div>
        </div>
    </x-modal-form>

    {{-- View Modal --}}
    <x-view-modal :show="$showViewModal" :title="$viewModalTitle" closeAction="closeViewModal">

        @if ($viewHomework)
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $viewHomework->title }}</h3>
                    <p class="text-gray-600">{{ $viewHomework->description }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>Teacher:</strong> {{ $viewHomework->user->name ?? 'Unknown' }}</p>
                        <p><strong>Class:</strong>
                            {{ $viewHomework->standard->name ?? 'Unknown' }}
                            @if ($viewHomework->section)
                                - {{ $viewHomework->section->name }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p><strong>Subject:</strong>
                            {{ $viewHomework->subject ? $viewHomework->subject->name : 'All Subjects' }}
                        </p>
                        <p><strong>Assigned:</strong>
                            {{ $viewHomework->created_at?->format('M d, Y h:i A') ?? 'Unknown' }}
                        </p>
                    </div>
                </div>

                @if ($viewHomework->file)
                    <div class="pt-4 border-t">
                        <p><strong>Attachment:</strong></p>
                        <a href="{{ $viewHomework->file }}" target="_blank"
                            class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 mt-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            View Attachment
                        </a>
                    </div>
                @endif
            </div>
        @else
            <p class="text-center text-gray-500">No data available.</p>
        @endif
    </x-view-modal>

    {{-- Filters and Search --}}
    <div class="px-4 pb-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-7 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <x-input wire:model.live.debounce.300ms="search"
                        placeholder="Search by title, description, teacher..." icon="magnifying-glass" />
                </div>

                {{-- Teacher Filter --}}
                <div>
                    <x-native-select wire:model.live="filterTeacher" placeholder="All Teachers">
                        <option value="">All Teachers</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>

                {{-- Standard Filter --}}
                <div>
                    <x-native-select wire:model.live="filterStandard" placeholder="All Standards">
                        <option value="">All Standards</option>
                        @foreach ($standards as $standard)
                            <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>

                {{-- Section Filter --}}
                <div>
                    <x-native-select wire:model.live="filterSection" placeholder="All Sections">
                        <option value="">All Sections</option>
                        @foreach ($filterSections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>

                {{-- Subject Filter --}}
                <div>
                    <x-native-select wire:model.live="filterSubject" placeholder="All Subjects">
                        <option value="">All Subjects</option>
                        @foreach ($filterSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </x-native-select>
                </div>
            </div>
        </div>
    </div>

    {{-- Homework List View --}}
    <div class="px-4 pb-4">
        @if ($homeworks->count() > 0)
            <div class="space-y-4">
                @foreach ($homeworks as $homework)
                    <div
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                        {{ $homework->title ?? 'No Title' }}
                                    </h3>
                                    <p class="text-gray-600 mb-3">
                                        {{ Str::limit($homework->description ?? 'No description', 200) }}
                                    </p>

                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="font-medium text-gray-700">Teacher:</span>
                                            <p class="text-gray-900">{{ $homework->user->name ?? 'Unknown' }}</p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Class:</span>
                                            <p class="text-gray-900">
                                                {{ $homework->standard->name ?? 'Unknown' }}
                                                @if ($homework->section)
                                                    - {{ $homework->section->name }}
                                                @endif
                                            </p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Subject:</span>
                                            <p class="text-gray-900">
                                                {{ $homework->subject ? $homework->subject->name : 'All Subjects' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-700">Assigned:</span>
                                            <p class="text-gray-900">
                                                {{ $homework->created_at?->format('M d, Y h:i A') ?? 'Unknown' }}
                                            </p>
                                        </div>
                                    </div>

                                    @if ($homework->file)
                                        <div class="mt-4 pt-4 border-t">
                                            <a href="{{ $homework->file }}" target="_blank"
                                                class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                View Attachment
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex gap-2 ml-4">
                                    <button wire:click="onViewHomework({{ $homework->id }})"
                                        class="p-2 text-blue-600 hover:text-blue-800 rounded-full hover:bg-blue-50"
                                        title="View Details">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="onEditHomework({{ $homework->id }})"
                                        class="p-2 text-indigo-600 hover:text-indigo-800 rounded-full hover:bg-indigo-50"
                                        title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="onDeleteHomework({{ $homework->id }})"
                                        class="p-2 text-red-600 hover:text-red-800 rounded-full hover:bg-red-50"
                                        title="Delete">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $homeworks->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Homeworks Found</h3>
                <p class="text-gray-600 mb-6">
                    @if ($search || $filterTeacher || $filterStandard || $filterSection || $filterSubject)
                        Try adjusting your search or filters.
                    @else
                        No homework assignments have been created yet.
                    @endif
                </p>
                <button wire:click="onAddHomework"
                    class="px-6 py-3 bg-gradient-3 hover:bg-gradient-3-hover text-white rounded-lg font-medium">
                    Add Your First Homework
                </button>
            </div>
        @endif
    </div>
</div>
