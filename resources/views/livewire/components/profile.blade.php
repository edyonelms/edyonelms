<div>
    {{-- Tab Bar --}}
    <div class="flex justify-center items-center px-6 pt-6 pb-4">
        <div class="flex gap-1 bg-white p-1 rounded-lg shadow-sm border border-gray-100">
            <button
                class="px-5 py-2 rounded-md text-sm font-medium transition-all {{ $activeTab === 'profile' ? 'bg-gradient-3 text-white shadow' : 'text-gray-500 hover:bg-gray-100' }}"
                wire:click="showTab('profile')">School Profile</button>
            <button
                class="px-5 py-2 rounded-md text-sm font-medium transition-all {{ $activeTab === 'view' ? 'bg-gradient-3 text-white shadow' : 'text-gray-500 hover:bg-gray-100' }}"
                wire:click="showTab('view')">View School Info</button>
            <button
                class="px-5 py-2 rounded-md text-sm font-medium transition-all {{ $activeTab === 'info' ? 'bg-gradient-3 text-white shadow' : 'text-gray-500 hover:bg-gray-100' }}"
                wire:click="showTab('info')">Edit School Info</button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         TAB: School Profile
    ══════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'profile')
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">

            {{-- Left column: photo + bank details --}}
            <div class="space-y-4">
                {{-- Photo Card --}}
                <div class="bg-white rounded-xl border border-gray-200 p-6 text-center">
                    @if ($tempPhotoUrl)
                        <img src="{{ $tempPhotoUrl }}"
                            class="w-28 h-28 rounded-full mx-auto object-cover border-4 border-white shadow mb-3">
                    @elseif ($organization->logo)
                        <img src="{{ $organization->logo }}"
                            class="w-28 h-28 rounded-full mx-auto object-cover border-4 border-white shadow mb-3">
                    @else
                        <div class="w-28 h-28 rounded-full mx-auto bg-gray-100 flex items-center justify-center mb-3">
                            <svg class="h-14 w-14 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif

                    <p class="font-semibold text-gray-800 mb-3">{{ $organization->name }}</p>

                    <div x-data="{ isUploading: false }" x-on:livewire-upload-start="isUploading = true"
                        x-on:livewire-upload-finish="isUploading = false"
                        x-on:livewire-upload-error="isUploading = false">
                        <label class="cursor-pointer">
                            <span
                                class="inline-block px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition">
                                {{ $photo ? 'Change Photo' : 'Upload Photo' }}
                            </span>
                            <input type="file" class="hidden" wire:model="photo">
                        </label>
                        <p class="text-xs text-gray-400 mt-1">JPG, PNG — max 2 MB</p>

                        @error('photo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <div wire:loading wire:target="photo" class="mt-2 text-sm text-gray-500">Uploading…</div>

                        @if ($photo)
                            <button wire:click="savePhoto"
                                class="mt-3 w-full py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition">
                                Save Photo
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Bank Account Details Card --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Bank Details
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-400">Bank Name</p>
                            <p class="text-sm font-medium text-gray-800">{{ $organization->bank_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Account Number</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $organization->bank_account_no ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">IFSC Code</p>
                            <p class="text-sm font-medium text-gray-800 tracking-wider">
                                {{ $organization->bank_ifsc ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Account Holder Name</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $organization->bank_holder_name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right column: org info + password --}}
            <div class="md:col-span-2 space-y-4">
                {{-- Org Details --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-4">School Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ([
                            'School Name' => $organization->name,
                            'Email' => $organization->email,
                            'Mobile Number' => $organization->mobile_number,
                            'State' => $organization->state,
                            'Education Board' => $organization->education_board,
                            'School Code' => $organization->school_code,
                            'Serial Number' => $organization->serial_number,
                            'Address' => $organization->address,
                        ] as $label => $value)
                            <div>
                                <p class="text-xs text-gray-400">{{ $label }}</p>
                                <p class="text-sm font-medium text-gray-800">{{ $value ?? '—' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Change Password --}}
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-4">Change Password</h2>
                    <div class="space-y-4">
                        @foreach ([
                            ['label' => 'Current Password', 'model' => 'currentPassword', 'show' => $showCurrentPassword, 'toggle' => 'current', 'error' => 'currentPassword'],
                            ['label' => 'New Password', 'model' => 'newPassword', 'show' => $showNewPassword, 'toggle' => 'new', 'error' => 'newPassword'],
                            ['label' => 'Confirm Password', 'model' => 'confirmPassword', 'show' => $showConfirmPassword, 'toggle' => 'confirm', 'error' => null],
                        ] as $field)
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">{{ $field['label'] }}</label>
                                <div class="relative">
                                    <input wire:model="{{ $field['model'] }}"
                                        type="{{ $field['show'] ? 'text' : 'password' }}"
                                        placeholder="{{ $field['label'] }}"
                                        class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 pr-10">
                                    <button type="button" wire:click="togglePasswordVisibility('{{ $field['toggle'] }}')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            @if ($field['show'])
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268-2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            @endif
                                        </svg>
                                    </button>
                                </div>
                                @if ($field['error'])
                                    @error($field['error'])
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                @endif
                            </div>
                        @endforeach

                        <button wire:click="updatePassword"
                            class="w-full py-2.5 bg-gradient-3 text-white rounded-lg text-sm font-medium hover:opacity-90 transition shadow">
                            Update Password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         TAB: View School Info
    ══════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'view')
        <div class="max-w-5xl mx-auto p-4 md:p-6 space-y-5">

            {{-- Header --}}
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5">
                    @if ($organization->logo)
                        <img src="{{ $organization->logo }}"
                            class="w-20 h-20 rounded-full object-cover border-4 border-white shadow shrink-0">
                    @else
                        <div
                            class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center border-4 border-white shadow shrink-0">
                            <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1 text-center sm:text-left">
                        <h1 class="text-2xl font-bold text-gray-800">{{ $organization->name }}</h1>
                        @if ($organization->address)
                            <p class="text-gray-500 text-sm mt-1">{{ $organization->address }}</p>
                        @endif
                        <div class="flex flex-wrap gap-4 mt-2 justify-center sm:justify-start">
                            @if ($schoolEmail)
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    {{ $schoolEmail }}
                                </span>
                            @endif
                            @if ($schoolMobileNo)
                                <span class="text-xs text-gray-500 flex items-center gap-1">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    {{ $schoolMobileNo }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Account Details --}}
            @if ($organization->bank_account_no || $organization->bank_name)
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Bank Account Details
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-400">Bank Name</p>
                            <p class="text-sm font-medium text-gray-800">{{ $organization->bank_name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Account Number</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $organization->bank_account_no ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">IFSC Code</p>
                            <p class="text-sm font-medium text-gray-800 tracking-widest">
                                {{ $organization->bank_ifsc ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Account Holder</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $organization->bank_holder_name ?? '—' }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- About School --}}
            @if ($aboutSchool)
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-2">About Our School</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $aboutSchool }}</p>
                </div>
            @endif

            {{-- Vision / Mission / Values / Goals --}}
            @if ($usmVision || $usmMission || $usmValues || $usmGoals)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ([
                        ['label' => 'Vision', 'value' => $usmVision, 'color' => 'blue'],
                        ['label' => 'Mission', 'value' => $usmMission, 'color' => 'green'],
                        ['label' => 'Values', 'value' => $usmValues, 'color' => 'purple'],
                        ['label' => 'Goals', 'value' => $usmGoals, 'color' => 'orange'],
                    ] as $item)
                        @if ($item['value'])
                            <div class="bg-white rounded-xl border border-gray-200 p-5">
                                <h3 class="text-sm font-semibold text-gray-800 mb-2">{{ $item['label'] }}</h3>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $item['value'] }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- Management Team --}}
            @if (count($schoolManagement) > 0)
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-4">Management Team</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($schoolManagement as $member)
                            <div class="text-center">
                                @if (!empty($member['photo_path']))
                                    <img src="{{ $member['photo_path'] }}"
                                        class="w-16 h-16 rounded-full mx-auto object-cover border border-gray-200 shadow-sm mb-2">
                                @else
                                    <div
                                        class="w-16 h-16 rounded-full mx-auto bg-gray-100 flex items-center justify-center mb-2">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <p class="text-sm font-medium text-gray-800">{{ $member['name'] }}</p>
                                <p class="text-xs text-gray-500">{{ $member['designation'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Documents --}}
            @if (count($uploadedDocuments) > 0)
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h2 class="text-base font-semibold text-gray-800 mb-4">School Documents</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($uploadedDocuments as $document)
                            <div
                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 bg-red-100 rounded flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 truncate">
                                            {{ $document['title'] }}</p>
                                        <p class="text-xs text-gray-400">{{ strtoupper($document['file_type']) }}</p>
                                    </div>
                                </div>
                                <a href="{{ $document['file_path'] }}" target="_blank"
                                    class="ml-3 shrink-0 text-xs px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">View</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════
         TAB: Edit School Info
    ══════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'info')
        <div class="max-w-4xl mx-auto p-4 md:p-6 space-y-6">

            {{-- About School --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700">About School</h2>
                </div>
                <div class="p-5">
                    <textarea wire:model="aboutSchool" rows="4"
                        class="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                        placeholder="Describe your school…"></textarea>
                </div>
            </div>

            {{-- Vision & Mission --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700">Vision, Mission, Values & Goals</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        ['label' => 'Vision', 'model' => 'usmVision', 'placeholder' => 'Long-term vision for your school…'],
                        ['label' => 'Mission', 'model' => 'usmMission', 'placeholder' => 'Mission statement…'],
                        ['label' => 'Core Values', 'model' => 'usmValues', 'placeholder' => 'Core values your school upholds…'],
                        ['label' => 'Goals & Objectives', 'model' => 'usmGoals', 'placeholder' => 'Key goals and objectives…'],
                    ] as $item)
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ $item['label'] }}</label>
                            <textarea wire:model="{{ $item['model'] }}" rows="3"
                                class="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition"
                                placeholder="{{ $item['placeholder'] }}"></textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-sm font-semibold text-gray-700">Contact Information</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">School Email</label>
                        <input type="email" wire:model="schoolEmail"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 transition"
                            placeholder="contact@school.edu">
                        @error('schoolEmail')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Mobile Number</label>
                        <input type="text" wire:model="schoolMobileNo" inputmode="numeric"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 transition"
                            placeholder="9876543210">
                        @error('schoolMobileNo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Website URL</label>
                        <input type="url" wire:model="websiteUrl"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 transition"
                            placeholder="https://www.yourschool.edu">
                        @error('websiteUrl')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">School Address</label>
                        <textarea wire:model="schoolAddress" rows="2"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 transition"
                            placeholder="Full address with city, state, pin code…"></textarea>
                        @error('schoolAddress')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Website Description</label>
                        <textarea wire:model="websiteInfo" rows="2"
                            class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-400 transition"
                            placeholder="Brief description of your website…"></textarea>
                    </div>
                </div>
            </div>

            {{-- Management Team --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">Management Team</h2>
                    <span class="text-xs text-gray-400">{{ count($schoolManagement) }} members</span>
                </div>
                <div class="p-5 space-y-3">
                    @forelse ($schoolManagement as $index => $member)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            {{-- Photo --}}
                            <div class="shrink-0 relative">
                                @if (isset($member['photo']) && $member['photo'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                    <img src="{{ $member['photo']->temporaryUrl() }}"
                                        class="h-12 w-12 rounded-full object-cover border border-gray-200">
                                @elseif (!empty($member['photo_path']))
                                    <img src="{{ $member['photo_path'] }}"
                                        class="h-12 w-12 rounded-full object-cover border border-gray-200">
                                @else
                                    <div
                                        class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center border border-gray-300">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <label
                                    class="absolute -bottom-0.5 -right-0.5 bg-white rounded-full p-0.5 shadow cursor-pointer border border-gray-200">
                                    <input type="file" class="hidden"
                                        wire:model="schoolManagement.{{ $index }}.photo">
                                    <svg class="h-3 w-3 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>
                            </div>

                            {{-- Name + Designation --}}
                            <div class="flex-1 grid grid-cols-2 gap-2">
                                <div>
                                    <input type="text"
                                        wire:model="schoolManagement.{{ $index }}.name"
                                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-yellow-400 transition"
                                        placeholder="Name">
                                    @error("schoolManagement.$index.name")
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text"
                                        wire:model="schoolManagement.{{ $index }}.designation"
                                        class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-yellow-400 transition"
                                        placeholder="Designation">
                                    @error("schoolManagement.$index.designation")
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            {{-- Remove --}}
                            <button wire:click="removeManagement({{ $index }})"
                                class="shrink-0 p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No members added yet.</p>
                    @endforelse

                    <button wire:click="addManagement"
                        class="w-full py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-yellow-400 hover:text-yellow-600 hover:bg-yellow-50 transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Member
                    </button>
                </div>
            </div>

            {{-- School Documents --}}
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-700">School Documents</h2>
                    <span class="text-xs text-gray-400">{{ count($uploadedDocuments) }} saved</span>
                </div>
                <div class="p-5 space-y-4">

                    {{-- Saved documents --}}
                    @if (count($uploadedDocuments) > 0)
                        <div class="space-y-2">
                            <p class="text-xs font-medium text-gray-500">Saved</p>
                            @foreach ($uploadedDocuments as $document)
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div
                                            class="w-7 h-7 bg-red-100 rounded flex items-center justify-center shrink-0">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">
                                                {{ $document['title'] }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ strtoupper($document['file_type']) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2 ml-3 shrink-0">
                                        <a href="{{ $document['file_path'] }}" target="_blank"
                                            class="text-xs px-2.5 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 transition">View</a>
                                        <button wire:click="removeDocument({{ $document['id'] }})"
                                            class="text-xs px-2.5 py-1 bg-gray-200 text-gray-600 rounded hover:bg-red-100 hover:text-red-600 transition">Delete</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Pending document slots --}}
                    @if (count($pendingDocuments) > 0)
                        <div class="space-y-3">
                            <p class="text-xs font-medium text-gray-500">New Documents (not saved yet)</p>
                            @foreach ($pendingDocuments as $index => $pending)
                                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200"
                                    x-data="{ sizeError: '' }">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-medium text-gray-600">Document
                                            {{ $index + 1 }}</span>
                                        <button wire:click="removeUploadedFile({{ $index }})"
                                            class="text-red-400 hover:text-red-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="space-y-2">
                                        <input type="text"
                                            wire:model="pendingDocuments.{{ $index }}.title"
                                            class="w-full p-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-amber-400 transition"
                                            placeholder="Document title">
                                        <div>
                                            <input type="file"
                                                wire:model="pendingDocuments.{{ $index }}.file"
                                                accept=".pdf"
                                                class="w-full text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-medium file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200"
                                                x-on:change="
                                                    const f = $event.target.files[0];
                                                    if (f && f.size > 2097152) {
                                                        sizeError = 'File is too large (' + (f.size / 1048576).toFixed(1) + ' MB). Maximum allowed size is 2 MB.';
                                                    } else {
                                                        sizeError = '';
                                                    }
                                                ">
                                            <p x-show="sizeError !== ''" x-text="sizeError"
                                                class="text-red-500 text-xs mt-1"></p>
                                            @error('pendingDocuments.' . $index . '.file')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        @if (!empty($pending['file']))
                                            <p class="text-xs text-green-600 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                File selected
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Add Document button --}}
                    <button wire:click="addDocumentSlot"
                        class="w-full py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-500 hover:border-red-400 hover:text-red-600 hover:bg-red-50 transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Add Document
                    </button>
                    <p class="text-xs text-gray-400">PDF only · max 2 MB per file</p>
                    @error('pendingDocuments.*.file')
                        <p class="text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end pt-2 pb-6">
                <button wire:click="saveSchoolInfo"
                    class="px-8 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition shadow flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 13l4 4L19 7" />
                    </svg>
                    Save All Information
                </button>
            </div>
        </div>
    @endif
</div>
