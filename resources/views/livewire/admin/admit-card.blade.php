<div class="p-4">
    <!-- Header Actions -->
    <div
        class="flex justify-between items-center p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-sm border mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Admit Card Management</h2>
            <p class="text-gray-600 mt-1">Manage and generate examination admit cards</p>
        </div>
        <div class="flex gap-3">
            <button wire:click="openBulkGenerate"
                class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <x-icon name="document-duplicate" class="h-5 w-5" />
                Bulk Generate
            </button>

            <button wire:click="addAdmitCard"
                class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <x-icon name="plus-circle" class="h-5 w-5" />
                Add Admit Card
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by admit card number, student name, roll number..."
                        class="pl-10 pr-4 py-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <!-- Exam Filter -->
            <div>
                <x-native-select wire:model.live="examFilter" label="Exam">
                    <option value="">All Exams</option>
                    @foreach ($this->exams as $exam)
                        <option value="{{ $exam->id }}">{{ $exam->exam_name }}</option>
                    @endforeach
                </x-native-select>
            </div>

            <!-- Class Filter -->
            <div>
                <x-native-select wire:model.live="standardFilter" label="Class">
                    <option value="">All Classes</option>
                    @foreach ($this->standards as $standard)
                        <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                    @endforeach
                </x-native-select>
            </div>
        </div>

        <div class="flex justify-between items-center mt-4">
            <div class="flex gap-3">
                <!-- Status Filter -->
                <div>
                    <x-native-select wire:model.live="statusFilter" label="Status">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="used">Used</option>
                    </x-native-select>
                </div>

                <!-- Show per page -->
                <x-native-select wire:model.live="perPage" label="Show" class="w-32">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </x-native-select>

                @if ($search || $examFilter || $standardFilter || $statusFilter)
                    <div class="flex items-end">
                        <x-button wire:click="resetFilters" slate outline label="Clear Filters" />
                    </div>
                @endif
            </div>

            <div class="text-sm text-gray-600">
                Total: {{ $admitCards->total() }} cards
            </div>
        </div>
    </div>

    <!-- Admit Cards Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Admit Card No.</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Student</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Exam</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Subjects</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Exam Center</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($admitCards as $card)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-mono font-medium text-gray-900">{{ $card->admit_card_number }}
                                </div>
                                <div class="text-xs text-gray-500">Roll: {{ $card->roll_number }}</div>
                                @if ($card->exam_roll_number)
                                    <div class="text-xs text-gray-500">Exam Roll: {{ $card->exam_roll_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-600 font-bold text-sm">
                                            {{ substr($card->student_name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $card->student_name }}</div>
                                        <div class="text-xs text-gray-500">
                                            {{ $card->studentDetail?->admission_no ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $card->studentDetail?->standard?->name ?? '' }}
                                            {{ $card->studentDetail?->section?->name ? '- ' . $card->studentDetail->section->name : '' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $card->exam_name }}</div>
                                <div class="text-xs text-gray-500">{{ $card->academic_year }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if (!empty($card->subjects))
                                    <div class="text-xs text-gray-600">
                                        {{ count($card->subjects) }} Subject(s)
                                    </div>
                                    @foreach (array_slice($card->subjects, 0, 2) as $subject)
                                        <div class="text-xs text-gray-500">
                                            • {{ $subject['subject_name'] ?? 'N/A' }}
                                            @if (isset($subject['subject_id']))
                                                @php
                                                    $subjectDetail = \App\Models\Student\Subject::find(
                                                        $subject['subject_id'],
                                                    );
                                                    $subjectCode = $subjectDetail->code ?? null;
                                                @endphp
                                                @if ($subjectCode)
                                                    <span class="text-gray-400">({{ $subjectCode }})</span>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                    @if (count($card->subjects) > 2)
                                        <div class="text-xs text-blue-600">+{{ count($card->subjects) - 2 }} more</div>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-400">No subjects</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $card->exam_center }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">
                                    {{ Str::limit($card->exam_center_address, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-800',
                                        'inactive' => 'bg-gray-100 text-gray-800',
                                        'used' => 'bg-blue-100 text-blue-800',
                                    ];
                                @endphp
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$card->status] ?? 'bg-gray-100' }}">
                                    {{ ucfirst($card->status) }}
                                </span>
                                @if ($card->seat_number)
                                    <div class="text-xs text-gray-600 mt-1">Seat: {{ $card->seat_number }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button wire:click="showAdmitCard({{ $card->id }})"
                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="View Admit Card">
                                        <x-icon name="eye" class="h-5 w-5" />
                                    </button>
                                    <button wire:click="editAdmitCard({{ $card->id }})"
                                        class="p-2 text-yellow-600 hover:text-yellow-800 hover:bg-yellow-50 rounded-lg transition-colors"
                                        title="Edit">
                                        <x-icon name="pencil-square" class="h-5 w-5" />
                                    </button>
                                    <button wire:click="confirmDelete({{ $card->id }})"
                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Delete">
                                        <x-icon name="trash" class="h-5 w-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400 mb-4">
                                    <x-icon name="document-text" class="h-16 w-16 mx-auto" />
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No admit cards found</h3>
                                <p class="text-gray-600 mb-4">
                                    @if ($search || $examFilter || $standardFilter)
                                        No admit cards match your search criteria
                                    @else
                                        Get started by adding your first admit card
                                    @endif
                                </p>
                                @if ($search || $examFilter || $standardFilter)
                                    <button wire:click="resetFilters"
                                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                        Clear Filters
                                    </button>
                                @else
                                    <button wire:click="addAdmitCard"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                        Add First Admit Card
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $admitCards->links() }}
        </div>
    </div>

    <!-- View Admit Card Modal -->
    @if ($showViewModal && $viewAdmitCard)
        <div
            class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl my-8 overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Admit Card Preview</h3>
                        <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x-mark" class="h-6 w-6" />
                        </button>
                    </div>

                    <!-- Admit Card Preview -->
                    <div
                        class="bg-gradient-to-br from-white to-gray-50 rounded-lg shadow-lg p-8 border-4 border-double border-purple-300">
                        <!-- Header -->
                        <div class="text-center border-b-2 border-purple-300 pb-4 mb-6">
                            <h1 class="text-3xl font-bold text-purple-900">
                                {{ $viewAdmitCard->organization->name }}
                            </h1>
                            <p class="text-xl text-gray-800 mt-2 font-semibold">{{ $viewAdmitCard->exam_name }}</p>
                            <p class="text-sm text-gray-600 mt-1">Academic Year: {{ $viewAdmitCard->academic_year }}
                            </p>
                            <p class="text-sm text-gray-600 font-bold mt-2">ADMIT CARD</p>
                        </div>

                        <div class="grid grid-cols-4 gap-6">
                            <!-- Student Info -->
                            <div class="col-span-3 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white p-3 rounded-lg border">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Student
                                            Name</label>
                                        <p class="text-lg font-bold text-gray-900">{{ $viewAdmitCard->student_name }}
                                        </p>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Father's
                                            Name</label>
                                        <p class="text-lg font-bold text-gray-900">{{ $viewAdmitCard->father_name }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4">
                                    <div class="bg-white p-3 rounded-lg border">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Admit Card
                                            No.</label>
                                        <p class="text-sm font-mono font-medium text-gray-900">
                                            {{ $viewAdmitCard->admit_card_number }}</p>
                                    </div>
                                    <div class="bg-white p-3 rounded-lg border">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Roll
                                            Number</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $viewAdmitCard->roll_number }}
                                        </p>
                                    </div>
                                    @if ($viewAdmitCard->exam_roll_number)
                                        <div class="bg-white p-3 rounded-lg border">
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Exam Roll
                                                No.</label>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $viewAdmitCard->exam_roll_number }}</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Subjects Schedule -->
                                @if (!empty($viewAdmitCard->subjects))
                                    <div class="bg-yellow-50 border-2 border-yellow-300 rounded-lg p-4">
                                        <h4 class="text-sm font-bold text-yellow-900 mb-3 uppercase">Examination
                                            Schedule</h4>
                                        <div class="space-y-2">
                                            @foreach ($viewAdmitCard->subjects as $subject)
                                                <div class="bg-white p-3 rounded border border-yellow-200">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <p class="font-bold text-gray-900">
                                                                {{ $subject['subject_name'] ?? 'N/A' }}
                                                                @if (isset($subject['subject_id']))
                                                                    @php
                                                                        $subjectDetail = \App\Models\Student\Subject::find(
                                                                            $subject['subject_id'],
                                                                        );
                                                                        $subjectCode = $subjectDetail->code ?? null;
                                                                    @endphp
                                                                    @if ($subjectCode)
                                                                        <span
                                                                            class="text-xs font-normal text-gray-600">({{ $subjectCode }})</span>
                                                                    @endif
                                                                @endif
                                                            </p>
                                                            <div class="grid grid-cols-3 gap-3 mt-2 text-xs">
                                                                <div>
                                                                    <span class="text-gray-600">Date:</span>
                                                                    <p class="font-medium text-gray-900">
                                                                        {{ isset($subject['exam_date']) ? \Carbon\Carbon::parse($subject['exam_date'])->format('d M, Y') : 'N/A' }}
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <span class="text-gray-600">Time:</span>
                                                                    <p class="font-medium text-gray-900">
                                                                        {{ isset($subject['exam_time']) ? \Carbon\Carbon::parse($subject['exam_time'])->format('h:i A') : 'N/A' }}
                                                                    </p>
                                                                </div>
                                                                <div>
                                                                    <span class="text-gray-600">Duration:</span>
                                                                    <p class="font-medium text-gray-900">
                                                                        {{ $subject['exam_duration'] ?? 'N/A' }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="mt-3 pt-3 border-t border-yellow-300">
                                            <p class="text-xs text-yellow-800">
                                                <strong>Reporting Time:</strong>
                                                {{ $viewAdmitCard->reporting_time ? \Carbon\Carbon::parse($viewAdmitCard->reporting_time)->format('h:i A') : 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                <!-- Exam Center -->
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="text-sm font-semibold text-blue-800 mb-2">EXAMINATION CENTER</h4>
                                    <p class="text-sm font-medium text-gray-900">{{ $viewAdmitCard->exam_center }}</p>
                                    <p class="text-xs text-gray-600 mt-1">{{ $viewAdmitCard->exam_center_address }}
                                    </p>
                                    @if ($viewAdmitCard->seat_number || $viewAdmitCard->room_number)
                                        <div class="grid grid-cols-2 gap-3 mt-2">
                                            @if ($viewAdmitCard->seat_number)
                                                <div>
                                                    <label class="text-xs text-gray-600">Seat Number</label>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $viewAdmitCard->seat_number }}</p>
                                                </div>
                                            @endif
                                            @if ($viewAdmitCard->room_number)
                                                <div>
                                                    <label class="text-xs text-gray-600">Room Number</label>
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $viewAdmitCard->room_number }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Photo and QR Code -->
                            <div class="space-y-4">
                                <!-- Student Photo -->
                                @if ($viewAdmitCard->studentDetail?->image)
                                    <div class="bg-white p-4 rounded-lg border text-center">
                                        <label class="text-xs font-semibold text-gray-500 uppercase block mb-2">Student
                                            Photo</label>
                                        <img src="{{ Storage::url($viewAdmitCard->studentDetail->image) }}"
                                            class="w-32 h-32 mx-auto object-cover rounded border">
                                    </div>
                                @endif

                                <!-- QR Code -->
                                @if ($viewAdmitCard->qr_code)
                                    <div class="bg-white p-4 rounded-lg border text-center">
                                        <label
                                            class="text-xs font-semibold text-gray-500 uppercase block mb-2">Verification
                                            QR</label>
                                        <img src="data:image/png;base64,{{ $viewAdmitCard->qr_code }}"
                                            class="w-32 h-32 mx-auto">
                                        <p class="text-xs text-gray-600 mt-2">Scan to verify</p>
                                    </div>
                                @endif

                                <!-- Issue Date -->
                                <div class="bg-white p-3 rounded-lg border">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Issue Date</label>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $viewAdmitCard->issue_date?->format('d M, Y') }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        @if ($viewAdmitCard->instructions || !empty($viewAdmitCard->allowed_items) || !empty($viewAdmitCard->prohibited_items))
                            <div class="mt-6 pt-4 border-t-2 border-gray-300">
                                <h4 class="text-sm font-semibold text-gray-800 mb-3">IMPORTANT INSTRUCTIONS</h4>

                                @if ($viewAdmitCard->instructions)
                                    <p class="text-sm text-gray-700 mb-3">{{ $viewAdmitCard->instructions }}</p>
                                @endif

                                <div class="grid grid-cols-2 gap-4">
                                    @if (!empty($viewAdmitCard->allowed_items))
                                        <div class="bg-green-50 p-3 rounded-lg border border-green-200">
                                            <h5 class="text-xs font-semibold text-green-800 mb-2">ALLOWED ITEMS</h5>
                                            <ul class="text-xs text-gray-700 space-y-1">
                                                @foreach ($viewAdmitCard->allowed_items as $item)
                                                    <li class="flex items-center">
                                                        <x-icon name="check-circle"
                                                            class="h-3 w-3 text-green-600 mr-2" />
                                                        {{ $item }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (!empty($viewAdmitCard->prohibited_items))
                                        <div class="bg-red-50 p-3 rounded-lg border border-red-200">
                                            <h5 class="text-xs font-semibold text-red-800 mb-2">PROHIBITED ITEMS</h5>
                                            <ul class="text-xs text-gray-700 space-y-1">
                                                @foreach ($viewAdmitCard->prohibited_items as $item)
                                                    <li class="flex items-center">
                                                        <x-icon name="x-circle" class="h-3 w-3 text-red-600 mr-2" />
                                                        {{ $item }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Signatures -->
                        <div class="mt-6 pt-4 border-t-2 border-gray-300">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center">
                                    <div class="border-t-2 border-gray-400 mt-8 pt-2 mx-auto w-32"></div>
                                    <p class="text-xs text-gray-600 mt-1">Student's Signature</p>
                                </div>
                                <div class="text-center">
                                    <div class="border-t-2 border-gray-400 mt-8 pt-2 mx-auto w-32"></div>
                                    <p class="text-xs text-gray-600 mt-1">Invigilator's Signature</p>
                                </div>
                                <div class="text-center">
                                    <div class="border-t-2 border-gray-400 mt-8 pt-2 mx-auto w-32"></div>
                                    <p class="text-xs text-gray-600 mt-1">Principal's Signature</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button wire:click="closeViewModal"
                            class="px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add/Edit Modal with Subjects -->
    @if ($showEditModal)
        <div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-3xl overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $admitCardId ? 'Edit Admit Card' : 'Add New Admit Card' }}
                        </h3>
                        <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x-mark" class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Exam Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam *</label>
                                <select wire:model="examId"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">Select Exam</option>
                                    @foreach ($this->exams as $exam)
                                        <option value="{{ $exam->id }}">{{ $exam->exam_name }}
                                            ({{ $exam->academic_year }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('examId')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Admit Card Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Admit Card Number *</label>
                                <input type="text" wire:model="admitCardNumber"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 font-mono"
                                    placeholder="Auto-generated">
                                @error('admitCardNumber')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Student Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Student *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="studentSearch"
                                    placeholder="Search by student name, admission number, or roll number..."
                                    class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    autocomplete="off">
                            </div>

                            @if (strlen($studentSearch) >= 2 && count($this->availableStudents) > 0)
                                <div
                                    class="mt-2 border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto bg-white z-50">
                                    @foreach ($this->availableStudents as $student)
                                        <div wire:click="selectStudent({{ $student['id'] }})"
                                            class="px-4 py-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                            <div class="font-medium text-gray-900">{{ $student['text'] }}</div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                Roll: {{ $student['roll_no'] }} | Class: {{ $student['class'] }}
                                                {{ $student['section'] ? '- ' . $student['section'] : '' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($studentId && strlen($studentSearch) < 2)
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="font-medium text-green-800">Selected Student:</div>
                                    <div class="text-sm text-green-700">{{ $studentSearch }}</div>
                                </div>
                            @endif

                            @error('studentId')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Roll Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Roll Number *</label>
                                <input type="text" wire:model="rollNumber"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('rollNumber')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Exam Roll Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Exam Roll Number</label>
                                <input type="text" wire:model="examRollNumber"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('examRollNumber')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Subjects Section -->
                        <div
                            class="bg-gradient-to-r from-purple-50 to-pink-50 p-4 rounded-lg border-2 border-purple-200">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-bold text-purple-900 uppercase">Subject-wise Exam Schedule *
                                </h4>
                                <button type="button" wire:click="addSubject"
                                    class="px-3 py-1.5 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-1">
                                    <x-icon name="plus" class="h-4 w-4" />
                                    Add Subject
                                </button>
                            </div>

                            <div class="space-y-3">
                                @foreach ($subjects as $index => $subject)
                                    <div class="bg-white p-4 rounded-lg border border-purple-200">
                                        <div class="flex justify-between items-start mb-3">
                                            <span class="text-xs font-semibold text-purple-700">Subject
                                                {{ $index + 1 }}</span>
                                            @if (count($subjects) > 1)
                                                <button type="button"
                                                    wire:click="removeSubject({{ $index }})"
                                                    class="text-red-600 hover:text-red-800">
                                                    <x-icon name="x-mark" class="h-4 w-4" />
                                                </button>
                                            @endif
                                        </div>

                                        <div class="space-y-3">
                                            <!-- Subject Selection -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Select
                                                    Subject *</label>
                                                <div class="relative">
                                                    <select wire:model="subjects.{{ $index }}.subject_id"
                                                        wire:change="addSubjectFromList({{ $index }})"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                                        <option value="">Choose Subject</option>
                                                        @foreach ($this->availableSubjects as $subjectItem)
                                                            <option value="{{ $subjectItem->id }}">
                                                                {{ $subjectItem->name }}
                                                                @if ($subjectItem->code)
                                                                    ({{ $subjectItem->code }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error("subjects.{$index}.subject_id")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="grid grid-cols-3 gap-3">
                                                <div class="col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam
                                                        Date *</label>
                                                    <input type="date"
                                                        wire:model="subjects.{{ $index }}.exam_date"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                                    @error("subjects.{$index}.exam_date")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Time
                                                        *</label>
                                                    <input type="time"
                                                        wire:model="subjects.{{ $index }}.exam_time"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                                    @error("subjects.{$index}.exam_time")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-xs font-medium text-gray-700 mb-1">Duration
                                                        *</label>
                                                    <input type="text"
                                                        wire:model="subjects.{{ $index }}.exam_duration"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                                        placeholder="3 Hrs">
                                                    @error("subjects.{$index}.exam_duration")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('subjects')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Reporting Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reporting Time *</label>
                            <input type="time" wire:model="reportingTime"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @error('reportingTime')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Exam Center -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Center *</label>
                            <input type="text" wire:model="examCenter"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 mb-2"
                                placeholder="e.g., Main Campus, Block A">
                            @error('examCenter')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Center Address *</label>
                            <textarea wire:model="examCenterAddress" rows="3"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Full address of the exam center"></textarea>
                            @error('examCenterAddress')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <!-- Seat Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Seat Number</label>
                                <input type="text" wire:model="seatNumber"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="e.g., A-12">
                            </div>

                            <!-- Room Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Room Number</label>
                                <input type="text" wire:model="roomNumber"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    placeholder="e.g., Room 101">
                            </div>
                        </div>

                        <!-- Allowed Items -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Allowed Items in Exam
                                Hall</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($commonAllowedItems as $item)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="allowedItems" value="{{ $item }}"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $item }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Prohibited Items -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Prohibited Items</label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach ($commonProhibitedItems as $item)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="prohibitedItems"
                                            value="{{ $item }}"
                                            class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ $item }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Special Instructions</label>
                            <textarea wire:model="instructions" rows="4"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Enter any special instructions for students..."></textarea>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select wire:model="status"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="used">Used (Exam Taken)</option>
                            </select>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                        <button wire:click="closeEditModal"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="saveAdmitCard"
                            class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            {{ $admitCardId ? 'Update' : 'Create' }} Admit Card
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Generate Modal -->
    @if ($showBulkGenerateModal)
        <div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-y-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Bulk Generate Admit Cards</h3>
                        <button wire:click="closeBulkModal" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x-mark" class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Exam Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Exam *</label>
                            <select wire:model.live="selectedExam"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Select Exam</option>
                                @foreach ($this->exams as $exam)
                                    <option value="{{ $exam->id }}">{{ $exam->exam_name }}
                                        ({{ $exam->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                            @error('selectedExam')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Class Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Select Class *</label>
                            <select wire:model.live="selectedStandard"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="">Select Class</option>
                                @foreach ($this->standards as $standard)
                                    <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                                @endforeach
                            </select>
                            @error('selectedStandard')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Section Selection -->
                        @if ($selectedStandard)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Section
                                    (Optional)</label>
                                <select wire:model.live="selectedSection"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    <option value="">All Sections</option>
                                    @foreach ($this->sections as $section)
                                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <!-- Exam Center Details -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Center *</label>
                            <input type="text" wire:model="bulkExamCenter"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 mb-2"
                                placeholder="e.g., Main Campus, Block A">
                            @error('bulkExamCenter')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Center Address *</label>
                            <textarea wire:model="bulkExamCenterAddress" rows="3"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Full address of the exam center"></textarea>
                            @error('bulkExamCenterAddress')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instructions (Optional)</label>
                            <textarea wire:model="bulkInstructions" rows="3"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Common instructions for all students..."></textarea>
                        </div>

                        <!-- Bulk Subjects Section -->
                        <div
                            class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border-2 border-blue-200">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-bold text-blue-900 uppercase">Exam Subjects for All Students *
                                </h4>
                                <button type="button" wire:click="addBulkSubject"
                                    class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-1">
                                    <x-icon name="plus" class="h-4 w-4" />
                                    Add Subject
                                </button>
                            </div>

                            <div class="space-y-3">
                                @foreach ($bulkSubjects as $index => $subject)
                                    <div class="bg-white p-4 rounded-lg border border-blue-200">
                                        <div class="flex justify-between items-start mb-3">
                                            <span class="text-xs font-semibold text-blue-700">Subject
                                                {{ $index + 1 }}</span>
                                            @if (count($bulkSubjects) > 1)
                                                <button type="button"
                                                    wire:click="removeBulkSubject({{ $index }})"
                                                    class="text-red-600 hover:text-red-800">
                                                    <x-icon name="x-mark" class="h-4 w-4" />
                                                </button>
                                            @endif
                                        </div>

                                        <div class="space-y-3">
                                            <!-- Subject Selection -->
                                            <div>
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Select
                                                    Subject *</label>
                                                <div class="relative">
                                                    <select wire:model="bulkSubjects.{{ $index }}.subject_id"
                                                        wire:change="addBulkSubjectFromList({{ $index }})"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="">Choose Subject</option>
                                                        @foreach ($this->availableSubjects as $subjectItem)
                                                            <option value="{{ $subjectItem->id }}">
                                                                {{ $subjectItem->name }}
                                                                @if ($subjectItem->code)
                                                                    ({{ $subjectItem->code }})
                                                                @endif
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error("bulkSubjects.{$index}.subject_id")
                                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>

                                            <div class="grid grid-cols-3 gap-3">
                                                <div class="col-span-2">
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Exam
                                                        Date *</label>
                                                    <input type="date"
                                                        wire:model="bulkSubjects.{{ $index }}.exam_date"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    @error("bulkSubjects.{$index}.exam_date")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label class="block text-xs font-medium text-gray-700 mb-1">Time
                                                        *</label>
                                                    <input type="time"
                                                        wire:model="bulkSubjects.{{ $index }}.exam_time"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                    @error("bulkSubjects.{$index}.exam_time")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-xs font-medium text-gray-700 mb-1">Duration
                                                        *</label>
                                                    <input type="text"
                                                        wire:model="bulkSubjects.{{ $index }}.exam_duration"
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                        placeholder="3 Hrs">
                                                    @error("bulkSubjects.{$index}.exam_duration")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('bulkSubjects')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Student Selection -->
                        @if ($selectedStandard)
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Select Students *</label>
                                    @if (!empty($this->availableStudentsForBulk) && count($this->availableStudentsForBulk) > 0)
                                        <div class="flex gap-2">
                                            <button type="button" wire:click="toggleAllStudents(true)"
                                                class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                                Select All
                                            </button>
                                            <button type="button" wire:click="toggleAllStudents(false)"
                                                class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                                                Deselect All
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                @if (!empty($this->availableStudentsForBulk) && count($this->availableStudentsForBulk) > 0)
                                    <div class="border border-gray-300 rounded-lg max-h-60 overflow-y-auto">
                                        @foreach ($this->availableStudentsForBulk as $student)
                                            <label
                                                class="flex items-center px-4 py-3 hover:bg-gray-50 border-b last:border-b-0">
                                                <input type="checkbox" wire:model.live="selectedStudents"
                                                    value="{{ $student['id'] }}"
                                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $student['name'] }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        Admission: {{ $student['admission_no'] }} |
                                                        Roll: {{ $student['roll_no'] }} |
                                                        Class: {{ $student['class'] }}@if ($student['section'])
                                                            - {{ $student['section'] }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-gray-500 mt-2">
                                        Selected: {{ count($selectedStudents) }} students
                                    </div>
                                @elseif ($selectedStandard && $selectedExam)
                                    <div class="text-center py-8 text-gray-500">
                                        <x-icon name="user-group" class="h-12 w-12 mx-auto text-gray-300 mb-2" />
                                        <p>All students in this class already have admit cards for this exam.</p>
                                    </div>
                                @elseif ($selectedStandard)
                                    <div class="text-center py-8 text-gray-500">
                                        <x-icon name="user-group" class="h-12 w-12 mx-auto text-gray-300 mb-2" />
                                        <p>Please select an exam first to see available students.</p>
                                    </div>
                                @endif

                                @error('selectedStudents')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Info Box -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="information-circle"
                                    class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Note</h4>
                                    <p class="text-sm text-blue-700 mt-1">
                                        All selected students will receive admit cards with the same subjects, dates,
                                        and times specified above.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                        <button wire:click="closeBulkModal"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="bulkGenerateAdmitCards"
                            class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Generate Admit Cards
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div
            class="fixed inset-0 flex justify-center bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm mx-auto my-auto">
                <div class="p-6">
                    <div class="text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                            <x-icon name="exclamation-triangle" class="h-6 w-6 text-red-600" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete Admit Card</h3>
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this admit card? This action
                            cannot be undone.</p>
                    </div>

                    <div class="flex justify-center gap-3">
                        <button wire:click="closeDeleteModal"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="deleteAdmitCard"
                            class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
