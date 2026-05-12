<div class="p-4">
    <div class="flex justify-between items-center p-4">
        <h1 class="text-3xl font-bold text-gray-700">Book Management</h1>
        <button wire:click="onAddBook()" class="px-4 py-2 bg-gradient-3 hover:bg-gradient-3-hover text-white rounded-lg">
            Add Book
        </button>
    </div>

    {{-- Add/Edit Modal --}}
    <x-modal-form show="{{ $open }}" title="{{ $editId ? 'Edit Book' : 'Add Book' }}" submitAction="onSave"
        submitButton="{{ $editId ? 'Update' : 'Create' }}" closeAction="closeModal">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-4">

            {{-- Book Logo and PDF Upload --}}
            <div class="sm:col-span-2">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Book Logo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Book Logo</label>
                        @if ($editId && !$book_logo)
                            @php $book = \App\Models\Admin\Book::find($editId) @endphp
                            @if ($book && $book->book_logo)
                                <div class="flex items-center gap-2 mb-2">
                                    <img src="{{ $book->book_logo }}" class="h-32 w-24 object-cover border rounded">
                                    <button wire:click="$set('book_logo', null)" type="button"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        Remove
                                    </button>
                                </div>
                            @endif
                        @endif

                        @if ($tempLogoUrl)
                            <img src="{{ $tempLogoUrl }}" class="h-32 w-24 object-cover border rounded mb-2">
                        @endif

                        <input type="file" wire:model="book_logo" accept="image/*"
                            class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">
                        @error('book_logo')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- PDF File Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PDF File</label>
                        @if ($editId && !$pdf_file)
                            @php $book = \App\Models\Admin\Book::find($editId) @endphp
                            @if ($book && $book->pdf_file)
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="text-blue-600">{{ basename($book->pdf_file) }}</span>
                                    <button wire:click="$set('pdf_file', null)" type="button"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        Remove
                                    </button>
                                </div>
                            @endif
                        @endif

                        @if ($tempPdfUrl)
                            <span class="text-sm text-gray-600">{{ $tempPdfUrl }}</span>
                        @endif

                        <input type="file" wire:model="pdf_file" accept=".pdf"
                            class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-blue-50 file:text-blue-700
                            hover:file:bg-blue-100">
                        @error('pdf_file')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Book Title --}}
            <div class="sm:col-span-2">
                <x-input wire:model.defer="title" label="Book Title" required />
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

            {{-- Subject --}}
            <x-native-select label="Subject" wire:model.defer="subject_id" required>
                <option value="">Select Subject</option>
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </x-native-select>

            {{-- Active Status --}}
            <div class="flex items-center">
                <x-checkbox wire:model.defer="is_active" label="Active" />
            </div>
        </div>
    </x-modal-form>

    {{-- View Modal --}}
    <x-view-modal :show="$showViewModal" :title="$viewModalTitle" closeAction="closeViewModal">

        @if ($viewBook)
            <div class="flex justify-center mb-4">
                @if ($viewBook->book_logo)
                    <img src="{{ $viewBook->book_logo }}" class="h-32 w-24 object-cover rounded shadow"
                        alt="Book Logo">
                @else
                    <div class="h-32 w-24 bg-gray-200 flex items-center justify-center text-gray-500 rounded">
                        No Logo
                    </div>
                @endif
            </div>

            <div class="space-y-2">
                <p><strong>Title:</strong> {{ $viewBook->title }}</p>
                <p><strong>Standard:</strong> {{ $viewBook->standard->name ?? '—' }}</p>
                <p><strong>Section:</strong> {{ $viewBook->section->name ?? 'N/A' }}</p>
                <p><strong>Subject:</strong> {{ $viewBook->subject->name ?? '—' }}</p>
                <p><strong>Status:</strong>
                    <span
                        class="px-2 py-1 rounded text-xs {{ $viewBook->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $viewBook->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </p>

                @if ($viewBook->pdf_file)
                    <p><strong>PDF File:</strong>
                        <a href="{{ $viewBook->pdf_file }}" target="_blank" class="text-blue-600 underline">View
                            PDF</a>
                    </p>
                @endif
            </div>
        @else
            <p class="text-center text-gray-500">No data available.</p>
        @endif
    </x-view-modal>

    {{-- Filters and Search --}}
    <div class="px-4 pb-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <x-input wire:model.live.debounce.300ms="search" placeholder="Search books..."
                        icon="magnifying-glass" />
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

                {{-- Status Filter --}}
                <div>
                    <x-native-select wire:model.live="filterStatus" placeholder="All Status">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </x-native-select>
                </div>
            </div>
        </div>
    </div>

    {{-- Books Grid View --}}
    <div class="px-4 pb-4">
        @if ($books->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($books as $book)
                    <div
                        class="bg-white rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 overflow-hidden group">
                        {{-- Book Cover --}}
                        <div
                            class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            @if ($book->book_logo)
                                <img src="{{ $book->book_logo }}" alt="{{ $book->title }}"
                                    class="h-full w-full object-cover">
                            @else
                                <div class="text-center p-4">
                                    <svg class="h-24 w-24 mx-auto text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="text-gray-500 text-sm mt-2">No Cover</p>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            <div class="absolute top-2 right-2">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-semibold {{ $book->is_active ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                                    {{ $book->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            {{-- PDF Badge --}}
                            @if ($book->pdf_file)
                                <div class="absolute top-2 left-2">
                                    <a href="{{ $book->pdf_file }}" target="_blank"
                                        class="bg-blue-500 text-white p-2 rounded-full hover:bg-blue-600 transition-colors"
                                        title="View PDF">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </a>
                                </div>
                            @endif

                            {{-- Quick Actions Overlay (shows on hover) --}}
                            <div
                                class="absolute inset-0 bg-white/10 backdrop-blur-sm bg-opacity-0 group-hover:bg-opacity-60 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                                <div class="flex gap-2">
                                    <button wire:click="onViewBook({{ $book->id }})"
                                        class="bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-full transition-colors transform hover:scale-110"
                                        title="View Details">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button wire:click="onEditBook({{ $book->id }})"
                                        class="bg-indigo-500 hover:bg-indigo-600 text-white p-3 rounded-full transition-colors transform hover:scale-110"
                                        title="Edit">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="onDeleteBook({{ $book->id }})"
                                        class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-full transition-colors transform hover:scale-110"
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

                        {{-- Book Details --}}
                        <div class="p-4">
                            {{-- Title --}}
                            <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2"
                                title="{{ $book->title }}">
                                {{ $book->title }}
                            </h3>

                            {{-- Details Grid --}}
                            <div class="space-y-1 text-sm">
                                <div class="flex items-center text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="font-medium">Standard:</span>
                                    <span class="ml-1">{{ $book->standard->name ?? '—' }}</span>
                                </div>

                                <div class="flex items-center text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <span class="font-medium">Section:</span>
                                    <span class="ml-1">{{ $book->section->name ?? 'N/A' }}</span>
                                </div>

                                <div class="flex items-center text-gray-600">
                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <span class="font-medium">Subject:</span>
                                    <span class="ml-1">{{ $book->subject->name ?? '—' }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="h-5 w-5 mr-2 text-red-100" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 3v5h5" />
                                        <text x="8" y="17" font-size="6" fill="currentColor" font-weight="bold">
                                            PDF
                                        </text>
                                    </svg>

                                    <p><strong>PDF File:</strong>
                                        <a href="{{ $book->pdf_file }}" target="_blank"
                                            class="text-blue-600 underline">View
                                            PDF</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="h-24 w-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Books Found</h3>
                <p class="text-gray-600 mb-6">No books match your current filters. Try adjusting your search criteria.
                </p>
                <button wire:click="onAddBook"
                    class="px-6 py-3 bg-gradient-3 hover:bg-gradient-3-hover text-white rounded-lg font-medium">
                    Add Your First Book
                </button>
            </div>
        @endif
    </div>
</div>
