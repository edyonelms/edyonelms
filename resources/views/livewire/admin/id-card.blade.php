<div class="p-4">
    <!-- Header Actions -->
    <div
        class="flex justify-between items-center p-6 bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl shadow-sm border mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">ID Card Management</h2>
            <p class="text-gray-600 mt-1">Manage and generate identification cards</p>
        </div>
        <div class="flex gap-3">
            <button wire:click="openBulkGenerate"
                class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <x-icon name="document-duplicate" class="h-5 w-5" />
                Bulk Generate
            </button>

            <button wire:click="addCard"
                class="px-4 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <x-icon name="plus-circle" class="h-5 w-5" />
                Add ID Card
            </button>
        </div>
    </div>

    <!-- Card Type Toggle -->
    <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-gray-700">Card Type:</span>
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button wire:click="switchCardType('student')"
                    class="px-6 py-2 rounded-md font-medium transition-all duration-200 {{ $cardType === 'student' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <x-icon name="academic-cap" class="h-5 w-5" />
                        Students
                    </div>
                </button>
                <button wire:click="switchCardType('teacher')"
                    class="px-6 py-2 rounded-md font-medium transition-all duration-200 {{ $cardType === 'teacher' ? 'bg-purple-600 text-white shadow-md' : 'text-gray-600 hover:text-gray-900' }}">
                    <div class="flex items-center gap-2">
                        <x-icon name="user-group" class="h-5 w-5" />
                        Teachers
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-xl shadow-sm border mb-6 p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="w-full md:w-1/2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Search by card number, name or {{ $cardType === 'student' ? 'admission' : 'employee' }} number..."
                        class="pl-10 pr-4 py-3 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <div class="flex gap-3">
                <x-native-select wire:model.live="perPage" label="Show" class="w-32">
                    <option value="10">10 per page</option>
                    <option value="25">25 per page</option>
                    <option value="50">50 per page</option>
                    <option value="100">100 per page</option>
                </x-native-select>

                @if ($search)
                    <x-button wire:click="resetFilters" slate outline label="Clear" />
                @endif
            </div>
        </div>
    </div>

    <!-- Cards Table -->
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Card Number</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ ucfirst($cardType) }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ $cardType === 'student' ? 'Class' : 'Assigned Classes' }}</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Issue Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Expiry Date</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($cards as $card)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $card->card_number }}</div>
                                <div class="text-xs text-gray-500">ID: {{ $card->id }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @php
                                        if ($cardType === 'student') {
                                            $person = $card->studentDetail;
                                            $identifier = $person?->admission_no;
                                            $personName = $person?->full_name ?? 'N/A';
                                            $personImage = $person?->image;
                                        } else {
                                            $person = $card->teacherDetail;
                                            $identifier = $person?->employee_id;
                                            $personName = $person?->user?->name ?? 'N/A';
                                            $personImage = $person?->user?->image;
                                        }
                                    @endphp

                                    @if ($personImage)
                                        <img src="{{ Storage::url($personImage) }}"
                                            class="h-10 w-10 rounded-full object-cover border">
                                    @else
                                        <div
                                            class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                            <span class="text-purple-600 font-bold text-sm">
                                                {{ substr($personName, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $personName }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $identifier ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($cardType === 'student')
                                    <div class="text-sm text-gray-900">
                                        {{ $card->studentDetail?->standard?->name ?? 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $card->studentDetail?->section?->name ?? '' }}
                                    </div>
                                @else
                                    @php
                                        $assignedClasses = $card->teacherDetail?->assignedClasses
                                            ->map(function ($class) {
                                                return ($class->standard->name ?? '') .
                                                    ' ' .
                                                    ($class->section->name ?? '');
                                            })
                                            ->filter()
                                            ->implode(', ');
                                    @endphp
                                    <div class="text-sm text-gray-900">
                                        {{ $assignedClasses ?: 'N/A' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $card->teacherDetail?->qualification ?? '' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $card->issue_date?->format('d M Y') ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $card->expiry_date?->format('d M Y') ?? 'N/A' }}
                                </div>
                                @if ($card->expiry_date && $card->expiry_date->isPast())
                                    <div class="text-xs text-red-600">Expired</div>
                                @elseif($card->expiry_date && $card->expiry_date->diffInDays(now()) <= 30)
                                    <div class="text-xs text-amber-600">Expires soon</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $card->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($card->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <button wire:click="showCard({{ $card->id }})"
                                        class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="View Card">
                                        <x-icon name="eye" class="h-5 w-5" />
                                    </button>
                                    <button wire:click="editCard({{ $card->id }})"
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
                                    <x-icon name="identification" class="h-16 w-16 mx-auto" />
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No ID cards found</h3>
                                <p class="text-gray-600 mb-4">
                                    @if ($search)
                                        No cards match your search criteria
                                    @else
                                        Get started by adding your first {{ $cardType }} ID card
                                    @endif
                                </p>
                                @if ($search)
                                    <button wire:click="resetFilters"
                                        class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                                        Clear Search
                                    </button>
                                @else
                                    <button wire:click="addCard"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                        Add First Card
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $cards->links() }}
        </div>
    </div>

    <!-- View Card Modal -->
    @if ($showViewModal && $viewCard)
        <div
            class="fixed inset-0 flex justify-center bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl my-auto">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">{{ ucfirst($cardType) }} ID Card</h3>
                        <button wire:click="closeViewModal" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x-mark" class="h-6 w-6" />
                        </button>
                    </div>

                    <!-- ID Card Preview -->
                    <div
                        class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg shadow-lg p-6 border-2 border-purple-200">
                        <!-- Organization Header -->
                        <div class="text-center border-b-2 border-purple-300 pb-4 mb-6">
                            <h2 class="text-2xl font-bold text-purple-900">
                                {{ $viewCard->organization->name }}
                            </h2>
                            <p class="text-sm text-gray-700 mt-1 font-medium">{{ ucfirst($cardType) }} Identification
                                Card</p>
                        </div>

                        @php
                            if ($cardType === 'student') {
                                $person = $viewCard->studentDetail;
                                $personName = $person?->full_name ?? 'N/A';
                                $personImage = $person?->image;
                                $identifier = $person?->admission_no;
                            } else {
                                $person = $viewCard->teacherDetail;
                                $personName = $person?->user?->name ?? 'N/A';
                                $personImage = $person?->user?->image;
                                $identifier = $person?->employee_id;
                            }
                        @endphp

                        <div class="grid grid-cols-3 gap-6">
                            <!-- Photo -->
                            <div class="col-span-1 flex flex-col items-center">
                                @if ($personImage)
                                    <img src="{{ Storage::url($personImage) }}"
                                        class="w-32 h-32 rounded-lg object-cover border-4 border-white shadow-lg">
                                @else
                                    <div
                                        class="w-32 h-32 rounded-lg bg-purple-200 flex items-center justify-center border-4 border-white shadow-lg">
                                        <span class="text-purple-700 font-bold text-4xl">
                                            {{ substr($personName, 0, 1) }}
                                        </span>
                                    </div>
                                @endif

                                <!-- QR Code -->
                                @if ($viewCard->qr_code)
                                    <div class="mt-4 bg-white p-2 rounded-lg shadow-md">
                                        <img src="data:image/png;base64,{{ $viewCard->qr_code }}" class="w-24 h-24">
                                        <p class="text-xs text-center text-gray-600 mt-1">Scan to verify</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="col-span-2 space-y-3">
                                <div class="bg-white rounded-lg p-3 shadow-sm">
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Full Name</label>
                                    <p class="text-lg font-bold text-gray-900">
                                        {{ $personName }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Card
                                            Number</label>
                                        <p class="text-sm font-medium text-gray-900">{{ $viewCard->card_number }}</p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">
                                            {{ $cardType === 'student' ? 'Admission No' : 'Employee ID' }}
                                        </label>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $identifier ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                @if ($cardType === 'student')
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-white rounded-lg p-3 shadow-sm">
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Class</label>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $person?->standard?->name ?? 'N/A' }}
                                                {{ $person?->section?->name ? '- ' . $person->section->name : '' }}
                                            </p>
                                        </div>
                                        <div class="bg-white rounded-lg p-3 shadow-sm">
                                            <label class="text-xs font-semibold text-gray-500 uppercase">Roll
                                                No</label>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $person?->roll_no ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Assigned
                                            Classes</label>
                                        <p class="text-sm font-medium text-gray-900">
                                            @php
                                                $assignedClasses = $person?->assignedClasses
                                                    ->map(function ($class) {
                                                        return ($class->standard->name ?? '') .
                                                            ' ' .
                                                            ($class->section->name ?? '');
                                                    })
                                                    ->filter()
                                                    ->implode(', ');
                                            @endphp
                                            {{ $assignedClasses ?: 'No classes assigned' }}
                                        </p>
                                    </div>

                                    @if ($person?->qualification)
                                        <div class="bg-white rounded-lg p-3 shadow-sm">
                                            <label
                                                class="text-xs font-semibold text-gray-500 uppercase">Qualification</label>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $person->qualification }}
                                            </p>
                                        </div>
                                    @endif
                                @endif

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Issue Date</label>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $viewCard->issue_date?->format('d M Y') ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Expiry
                                            Date</label>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $viewCard->expiry_date?->format('d M Y') ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                @if ($cardType === 'student' && $person?->dob)
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Date of
                                            Birth</label>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($person->dob)->format('d M Y') }}
                                        </p>
                                    </div>
                                @elseif($cardType === 'teacher' && $person?->date_of_joining)
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Joining
                                            Date</label>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ \Carbon\Carbon::parse($person->date_of_joining)->format('d M Y') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-6 pt-4 border-t-2 border-purple-300 text-center">
                            <p class="text-xs text-gray-600">
                                This card is the property of {{ $viewCard->organization->name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                If found, please return to the administration office
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button wire:click="closeViewModal"
                            class="px-6 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add/Edit Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">
                            {{ $cardId ? 'Edit ID Card' : 'Add New ID Card' }} ({{ ucfirst($cardType) }})
                        </h3>
                        <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x-mark" class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <!-- Person Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ ucfirst($cardType) }}
                                *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-icon name="magnifying-glass" class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="text" wire:model.live.debounce.300ms="personSearch"
                                    placeholder="Search by name or {{ $cardType === 'student' ? 'admission' : 'employee' }} number..."
                                    class="pl-10 pr-4 py-2.5 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                    autocomplete="off">
                            </div>

                            @if (strlen($personSearch) >= 2 && count($this->availablePersons) > 0)
                                <div
                                    class="mt-2 border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto bg-white z-50">
                                    @foreach ($this->availablePersons as $person)
                                        <div wire:click="selectPerson({{ $person['id'] }})"
                                            class="px-4 py-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-b-0 transition-colors">
                                            <div class="font-medium text-gray-900">{{ $person['text'] }}</div>
                                            <div class="text-sm text-gray-600 mt-1">{{ $person['info'] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if ($personId && strlen($personSearch) < 2)
                                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="font-medium text-green-800">Selected:</div>
                                    <div class="text-sm text-green-700">{{ $personSearch }}</div>
                                </div>
                            @endif

                            @error('personId')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Card Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Number *</label>
                            <input type="text" wire:model="cardNumber"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Enter card number">
                            @error('cardNumber')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                            <input type="date" wire:model="expiryDate"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @error('expiryDate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                            <select wire:model="status"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
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
                        <button wire:click="saveCard"
                            class="px-4 py-2.5 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            {{ $cardId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Bulk Generate Modal -->
    @if ($showBulkGenerateModal)
        <div class="fixed inset-0 flex justify-end bg-white/10 backdrop-blur-sm z-[9999] pt-16 pb-4 overflow-y-auto">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Bulk Generate {{ ucfirst($cardType) }} ID Cards
                        </h3>
                        <button wire:click="closeBulkModal" class="text-gray-400 hover:text-gray-600">
                            <x-icon name="x-mark" class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Card Prefix *</label>
                            <input type="text" wire:model="cardPrefix"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                                placeholder="e.g., ID, STU, TCH, CARD">
                            @error('cardPrefix')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Validity Period (Months)
                                *</label>
                            <input type="number" wire:model="validityMonths" min="1" max="60"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @error('validityMonths')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <x-icon name="information-circle"
                                    class="h-5 w-5 text-blue-600 flex-shrink-0 mt-0.5" />
                                <div>
                                    <h4 class="text-sm font-medium text-blue-800">Note</h4>
                                    <p class="text-sm text-blue-700 mt-1">
                                        This will generate ID cards for all {{ $cardType }}s who don't have an
                                        active card.
                                        Each card will have a unique number and QR code.
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
                        <button wire:click="bulkGenerateCards"
                            class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Generate Cards
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
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Delete ID Card</h3>
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this ID card? This action cannot
                            be undone.</p>
                    </div>

                    <div class="flex justify-center gap-3">
                        <button wire:click="closeDeleteModal"
                            class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="deleteCard"
                            class="px-4 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
