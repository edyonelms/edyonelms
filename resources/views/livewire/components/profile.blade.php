<div class="min-h-screen bg-gray-50">

    {{-- ══════════════════════════════════════════════════
         SUB-ADMIN PERSONAL DETAILS (only for scoped sub-admins)
    ══════════════════════════════════════════════════ --}}
    @if (auth()->user()->role === 'sub-admin')
        @php
            $me = auth()->user();
            $adminCatalog = collect(config('menu.admin', []))
                ->mapWithKeys(fn($i) => [$i['link'] => $i['title']]);
            $myAccess = collect((array) $me->permissions)
                ->map(fn($p) => $adminCatalog[$p] ?? $p)
                ->all();
        @endphp
        <div class="px-4 sm:px-6 pt-4 sm:pt-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-4">
                    @if ($me->image)
                        <img src="{{ $me->image }}" class="w-14 h-14 rounded-full object-cover border border-gray-200" alt="">
                    @else
                        <span class="w-14 h-14 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-xl font-bold">{{ strtoupper(substr($me->name ?? '', 0, 1)) }}</span>
                    @endif
                    <div class="min-w-0">
                        <h2 class="text-lg font-bold text-gray-900 truncate">{{ $me->name }}</h2>
                        <p class="text-sm text-gray-500 truncate">{{ $me->email }}</p>
                        <span class="mt-1 inline-flex items-center px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 text-xs font-medium">Sub-admin</span>
                    </div>
                </div>
                <div class="px-5 py-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-3 text-sm">
                    <div class="flex justify-between sm:block">
                        <dt class="text-gray-500">Mobile</dt>
                        <dd class="text-gray-800 font-medium">{{ $me->mobile_number ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between sm:block">
                        <dt class="text-gray-500">Alt. Mobile</dt>
                        <dd class="text-gray-800 font-medium">{{ $me->alternative_mobile ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between sm:block">
                        <dt class="text-gray-500">Gender</dt>
                        <dd class="text-gray-800 font-medium capitalize">{{ $me->gender ?: '—' }}</dd>
                    </div>
                    <div class="flex justify-between sm:block">
                        <dt class="text-gray-500">Date of Birth</dt>
                        <dd class="text-gray-800 font-medium">{{ $me->dob ? \Carbon\Carbon::parse($me->dob)->format('d M Y') : '—' }}</dd>
                    </div>
                    <div class="flex justify-between sm:block">
                        <dt class="text-gray-500">Date of Joining</dt>
                        <dd class="text-gray-800 font-medium">{{ $me->date_of_joining ? \Carbon\Carbon::parse($me->date_of_joining)->format('d M Y') : '—' }}</dd>
                    </div>
                    <div class="flex justify-between sm:block">
                        <dt class="text-gray-500">Organization</dt>
                        <dd class="text-gray-800 font-medium truncate">{{ $organization->name ?? '—' }}</dd>
                    </div>
                </div>
                <div class="px-5 py-4 border-t border-gray-100">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Granted Access</h4>
                    @if (!empty($myAccess))
                        <div class="flex flex-wrap gap-2">
                            @foreach ($myAccess as $perm)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-medium">{{ $perm }}</span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-gray-400">No functionalities granted.</p>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         HEADER (full-width, sticky, with tabs)
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if ($organization && $organization->logo)
                        <img src="{{ $organization->logo }}" class="w-11 h-11 rounded-full object-cover border border-gray-200 flex-shrink-0">
                    @else
                        <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $organization->name ?? 'School Profile' }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">Manage school profile, info, and credentials</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div class="border-t border-gray-200 px-4 sm:px-6">
            <div class="flex gap-1">
                <button wire:click="showTab('profile')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors
                           {{ $activeTab === 'profile' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        School Profile
                    </span>
                </button>
                <button wire:click="showTab('view')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors
                           {{ $activeTab === 'view' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View School Info
                    </span>
                </button>
                <button wire:click="showTab('info')"
                    class="px-4 py-3 text-sm font-medium border-b-2 transition-colors
                           {{ $activeTab === 'info' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <span class="inline-flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit School Info
                    </span>
                </button>
            </div>
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
         TAB: Edit School Info (about-app style)
    ══════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'info')
        <div class="max-w-5xl mx-auto p-4 md:p-6 space-y-6">

            {{-- About School --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50 flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">About School</h2>
                </div>
                <div class="p-6">
                    <textarea wire:model="aboutSchool" rows="4"
                        class="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400 transition"
                        placeholder="Describe your school…"></textarea>
                </div>
            </div>

            {{-- Vision, Mission, Values & Goals --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50 flex items-center gap-3">
                    <div class="w-8 h-8 bg-purple-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">Vision, Mission, Values & Goals</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ([
                        ['label' => 'Vision', 'model' => 'usmVision', 'placeholder' => 'Long-term vision for your school…'],
                        ['label' => 'Mission', 'model' => 'usmMission', 'placeholder' => 'Mission statement…'],
                        ['label' => 'Core Values', 'model' => 'usmValues', 'placeholder' => 'Core values your school upholds…'],
                        ['label' => 'Goals & Objectives', 'model' => 'usmGoals', 'placeholder' => 'Key goals and objectives…'],
                    ] as $item)
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">{{ $item['label'] }}</label>
                            <textarea wire:model="{{ $item['model'] }}" rows="3"
                                class="w-full p-3 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-purple-400 focus:border-purple-400 transition"
                                placeholder="{{ $item['placeholder'] }}"></textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50 flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">Contact Information</h2>
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
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-amber-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Management Team</h2>
                            <p class="text-xs text-gray-400">{{ count($schoolManagement) }} member(s)</p>
                        </div>
                    </div>
                    <button wire:click="openMemberPanel()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Member
                    </button>
                </div>
                <div class="p-6 space-y-3">
                    @forelse ($schoolManagement as $index => $member)
                        <div class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:border-amber-200 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                @if (!empty($member['photo_path']))
                                    <img src="{{ $member['photo_path'] }}" class="h-12 w-12 rounded-full object-cover border-2 border-white shadow flex-shrink-0">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-amber-100 flex items-center justify-center border-2 border-white shadow flex-shrink-0">
                                        <svg class="h-6 w-6 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $member['name'] }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $member['designation'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <button wire:click="openMemberPanel({{ $index }})"
                                    class="p-1.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-amber-50 hover:text-amber-600 transition-colors" title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="confirmDeleteMember({{ $index }})"
                                    class="p-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors" title="Remove">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="text-sm text-gray-400">No members added yet.</p>
                            <p class="text-xs text-gray-300 mt-1">Click "Add Member" to add team members.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- School Documents --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-blue-50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-cyan-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">School Documents</h2>
                            <p class="text-xs text-gray-400">{{ count($uploadedDocuments) }} saved · {{ count($pendingDocuments) }} pending · Max 2MB PDF</p>
                        </div>
                    </div>
                    <button wire:click="openDocumentPanel"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Document
                    </button>
                </div>
                <div class="p-6 space-y-3">

                    {{-- Saved documents --}}
                    @foreach ($uploadedDocuments as $document)
                        <div class="flex items-center justify-between gap-3 p-3 bg-gray-50 rounded-xl border border-gray-200 hover:border-cyan-200 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $document['title'] }}</p>
                                    <p class="text-xs text-gray-400 uppercase">{{ $document['file_type'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <a href="{{ $document['file_path'] }}" target="_blank"
                                    class="p-1.5 rounded-lg border border-cyan-200 text-cyan-600 hover:bg-cyan-50 transition-colors" title="View">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                                <button wire:click="confirmDeleteDocument({{ $document['id'] }})"
                                    class="p-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors" title="Delete">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    {{-- Pending documents (queued, not yet saved) --}}
                    @foreach ($pendingDocuments as $index => $pending)
                        <div class="flex items-center justify-between gap-3 p-3 bg-amber-50 rounded-xl border border-amber-200">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $pending['title'] }}</p>
                                    <p class="text-xs text-amber-700 uppercase font-semibold">Pending Save</p>
                                </div>
                            </div>
                            <button wire:click="removePendingDocument({{ $index }})"
                                class="p-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition-colors flex-shrink-0" title="Remove">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    @endforeach

                    @if (count($uploadedDocuments) === 0 && count($pendingDocuments) === 0)
                        <div class="text-center py-8 border-2 border-dashed border-gray-200 rounded-xl">
                            <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm text-gray-400">No documents uploaded yet.</p>
                            <p class="text-xs text-gray-300 mt-1">PDF only · max 2 MB per file.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end pt-2 pb-6">
                <button wire:click="saveSchoolInfo" wire:loading.attr="disabled"
                    class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-xl text-sm font-semibold transition shadow-md hover:shadow-lg flex items-center gap-2 disabled:opacity-60">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    <span wire:loading.remove wire:target="saveSchoolInfo">Save All Changes</span>
                    <span wire:loading wire:target="saveSchoolInfo">Saving…</span>
                </button>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         MANAGEMENT MEMBER SLIDE-IN PANEL
    ══════════════════════════════════════════════════ --}}
    @if ($showMemberPanel)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeMemberPanel"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-xl bg-white shadow-2xl flex flex-col">

                <button wire:click="closeMemberPanel"
                    class="absolute top-4 right-4 z-20 w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 hover:bg-red-50 hover:border-red-300 text-gray-500 hover:text-red-500 transition-colors shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex-1 overflow-y-auto px-6 pt-6 pb-6 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $editMemberIndex !== null ? 'Edit Member' : 'Add Member' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Fill in the member details below</p>
                    </div>

                    {{-- Photo --}}
                    <div class="flex items-center gap-4">
                        @if ($newMemberPhoto instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                            <img src="{{ $newMemberPhoto->temporaryUrl() }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow flex-shrink-0">
                        @elseif (!empty($newMember['photo_path']))
                            <img src="{{ $newMember['photo_path'] }}" class="w-16 h-16 rounded-full object-cover border-2 border-white shadow flex-shrink-0">
                        @else
                            <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center border-2 border-white shadow flex-shrink-0">
                                <svg class="w-7 h-7 text-amber-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Photo</label>
                            <input type="file" wire:model="newMemberPhoto" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 border border-gray-300 rounded-md">
                            <p class="text-xs text-gray-400 mt-1">JPG/PNG up to 2MB. Leave empty to keep current.</p>
                            <div wire:loading wire:target="newMemberPhoto" class="text-xs text-amber-600 mt-1">Uploading…</div>
                            @error('newMemberPhoto')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="newMember.name" placeholder="e.g. Mr. Sharma"
                            class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                        @error('newMember.name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Designation <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="newMember.designation" placeholder="e.g. Principal"
                            class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-amber-500 focus:border-amber-500">
                        @error('newMember.designation')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="px-6 py-3.5 border-t border-gray-200 flex items-center justify-end gap-2 flex-shrink-0">
                    <button wire:click="closeMemberPanel" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="saveMember" wire:loading.attr="disabled"
                        class="px-5 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-md flex items-center gap-1.5 disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveMember">{{ $editMemberIndex !== null ? 'Update' : 'Add Member' }}</span>
                        <span wire:loading wire:target="saveMember">Saving…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         DOCUMENT SLIDE-IN PANEL
    ══════════════════════════════════════════════════ --}}
    @if ($showDocumentPanel)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeDocumentPanel"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-xl bg-white shadow-2xl flex flex-col">

                <button wire:click="closeDocumentPanel"
                    class="absolute top-4 right-4 z-20 w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 hover:bg-red-50 hover:border-red-300 text-gray-500 hover:text-red-500 transition-colors shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex-1 overflow-y-auto px-6 pt-6 pb-6 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Add Document</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Upload a PDF up to 2MB. Will be saved when you click "Save All Changes".</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Title <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="newDocument.title" placeholder="e.g. Affiliation Certificate"
                            class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 text-sm focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                        @error('newDocument.title')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            File <span class="text-red-500">*</span>
                            <span class="text-gray-400 font-normal">(PDF only — max 2MB)</span>
                        </label>
                        <input type="file" wire:model="newDocumentFile" accept=".pdf"
                            class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 border border-gray-300 rounded-md">
                        <div wire:loading wire:target="newDocumentFile" class="text-xs text-cyan-600 mt-1.5">Uploading…</div>
                        @error('newDocumentFile')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="px-6 py-3.5 border-t border-gray-200 flex items-center justify-end gap-2 flex-shrink-0">
                    <button wire:click="closeDocumentPanel" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="saveDocumentPanel" wire:loading.attr="disabled"
                        class="px-5 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-md flex items-center gap-1.5 disabled:opacity-60">
                        <span wire:loading.remove wire:target="saveDocumentPanel">Queue Document</span>
                        <span wire:loading wire:target="saveDocumentPanel">Queueing…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════
         DELETE CONFIRMS
    ══════════════════════════════════════════════════ --}}
    @if ($pendingDeleteMemberIndex !== null)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[1.5px]" wire:click="cancelDeleteMember"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900 mb-1">Remove member?</h3>
                        <p class="text-sm text-gray-500">
                            @if (isset($schoolManagement[$pendingDeleteMemberIndex]))
                                Remove <strong>{{ $schoolManagement[$pendingDeleteMemberIndex]['name'] ?? 'this member' }}</strong>? Their photo on S3 will be deleted.
                            @else
                                This action cannot be undone.
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-5">
                    <button wire:click="cancelDeleteMember" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="executeDeleteMember" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">Remove</button>
                </div>
            </div>
        </div>
    @endif

    @if ($pendingDeleteDocumentId !== null)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/40 backdrop-blur-[1.5px]" wire:click="cancelDeleteDocument"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900 mb-1">Delete document?</h3>
                        <p class="text-sm text-gray-500">The PDF will be permanently removed from S3 and the school records.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2 mt-5">
                    <button wire:click="cancelDeleteDocument" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="executeDeleteDocument" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
