<div class="min-h-screen bg-gray-50">

    {{-- ===== TAB NAVIGATION ===== --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-6">
            <nav class="flex gap-1 py-2">
                @foreach ([
                ['profile', 'School Profile', 'M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zM19.5 10a7.5 7.5 0 11-15 0 7.5 7.5 0 0115 0z'],
                ['info', 'Edit Info', 'M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zM19.5 7.125L16.862 4.487'],
                ['view', 'View Profile', 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ] as [$key, $label, $icon])
                <button wire:click="showTab('{{ $key }}')"
                    class="flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-medium transition-all duration-200
                        {{ $activeTab === $key
                            ? 'bg-indigo-600 text-white shadow-md'
                            : 'text-gray-600 hover:bg-indigo-50 hover:text-indigo-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                    </svg>
                    {{ $label }}
                </button>
                @endforeach
            </nav>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-6 py-8 space-y-6">

        {{-- ===================== PROFILE TAB ===================== --}}
        @if ($activeTab === 'profile')

        {{-- Hero Header (About App style) --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="h-32 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500"></div>
            <div class="px-8 pb-8 -mt-12">
                <div class="flex flex-col sm:flex-row sm:items-end gap-5">

                    {{-- Logo --}}
                    <div class="flex-shrink-0">
                        @if ($tempPhotoUrl)
                        <img src="{{ $tempPhotoUrl }}"
                            class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg">
                        @elseif ($organization->logo)
                        <img src="{{ $organization->logo }}"
                            class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg">
                        @else
                        <div class="w-24 h-24 rounded-2xl bg-indigo-100 border-4 border-white shadow-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                            </svg>
                        </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="sm:mb-2 flex-1">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $organization->name ?? 'School Name' }}</h1>
                        <p class="text-sm text-gray-500 mt-1">{{ $organization->education_board ?? '' }}</p>
                        <div class="flex flex-wrap gap-3 mt-2">
                            @if ($organization->state ?? false)
                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                {{ $organization->state }}
                            </span>
                            @endif
                            @if ($organization->school_code ?? false)
                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                                </svg>
                                Code: {{ $organization->school_code }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Photo upload button --}}
                    <div class="sm:mb-2">
                        <label class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl cursor-pointer hover:bg-indigo-700 transition shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                            Change Photo
                            <input type="file" class="hidden" wire:model="photo">
                        </label>
                        @error('photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if ($photo)
                <div class="mt-4 flex items-center gap-3">
                    <button wire:click="savePhoto"
                        class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition shadow-md">
                        Save Photo
                    </button>
                    <p class="text-xs text-gray-400">JPG, PNG or GIF · Max 2MB</p>
                </div>
                @endif

                @if (session()->has('photo_message'))
                <div class="mt-3 p-2 text-xs text-green-700 bg-green-50 rounded-lg border border-green-200">
                    ✓ {{ session('photo_message') }}
                </div>
                @endif
            </div>
        </div>

        {{-- School Info Card (About App content section style) --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-indigo-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 text-white text-sm font-bold rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">School Information</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ([
                    ['School Name', $organization->name ?? 'N/A'],
                    ['Email', $organization->email ?? 'N/A'],
                    ['Mobile Number', $organization->mobile_number ?? 'N/A'],
                    ['State', $organization->state ?? 'N/A'],
                    ['Education Board', $organization->education_board ?? 'N/A'],
                    ['School Code', $organization->school_code ?? 'N/A'],
                    ['Serial Number', $organization->serial_number ?? 'N/A'],
                    ['Address', $organization->address ?? 'N/A'],
                    ] as [$label, $value])
                    <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-indigo-100 hover:bg-indigo-50 transition-all duration-200">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ $label }}</p>
                            <p class="text-sm font-medium text-gray-800 truncate">{{ $value }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Change Password Card --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-purple-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-pink-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-purple-500 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">Change Password</h2>
            </div>
            <div class="px-6 py-5">
                <div class="space-y-4 max-w-md">
                    @foreach ([
                    ['currentPassword', 'Current Password', 'current', $showCurrentPassword],
                    ['newPassword', 'New Password', 'new', $showNewPassword],
                    ['confirmPassword', 'Confirm Password', 'confirm', $showConfirmPassword],
                    ] as [$model, $label, $toggle, $show])
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">{{ $label }}</label>
                        <div class="relative">
                            <input wire:model="{{ $model }}"
                                type="{{ $show ? 'text' : 'password' }}"
                                placeholder="Enter {{ strtolower($label) }}"
                                class="w-full px-4 py-3 pr-12 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:border-transparent transition bg-gray-50">
                            <button type="button"
                                wire:click="togglePasswordVisibility('{{ $toggle }}')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    @if ($show)
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                    @else
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    @endif
                                </svg>
                            </button>
                        </div>
                        @error($model)
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach

                    <button wire:click="updatePassword"
                        class="w-full py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-semibold rounded-xl hover:from-purple-700 hover:to-pink-700 transition shadow-md hover:shadow-lg mt-2">
                        Update Password
                    </button>

                    @if (session()->has('password_message'))
                    <div class="p-3 text-sm text-green-700 bg-green-50 rounded-xl border border-green-200">
                        ✓ {{ session('password_message') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @endif

        {{-- ===================== EDIT INFO TAB ===================== --}}
        @if ($activeTab === 'info')

        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit School Information</h1>
            <p class="text-gray-500 text-sm mt-1">Update your school's details, vision, mission, and more.</p>
        </div>

        {{-- About School --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-blue-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">About School</h2>
            </div>
            <div class="px-6 py-5">
                <textarea wire:model="aboutSchool" rows="4"
                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent transition bg-gray-50 resize-none"
                    placeholder="Describe your school's history, achievements, and unique features..."></textarea>
                <p class="text-xs text-gray-400 mt-2">Displayed on your school's public profile page.</p>
            </div>
        </div>

        {{-- Vision, Mission, Values, Goals --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-purple-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-violet-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-purple-600 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">Vision, Mission & Values</h2>
            </div>
            <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach ([
                ['usmVision', 'Vision', 'What is the long-term vision?', 'blue', 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                ['usmMission', 'Mission', 'What is your mission statement?', 'green', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['usmValues', 'Core Values', 'What values does your school uphold?', 'purple', 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z'],
                ['usmGoals', 'Goals & Objectives', 'What are your key goals?', 'orange', 'M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941'],
                ] as [$model, $label, $placeholder, $color, $icon])
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-6 h-6 rounded-md bg-{{ $color }}-100 flex items-center justify-center">
                            <svg class="w-3.5 h-3.5 text-{{ $color }}-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                            </svg>
                        </div>
                        <label class="text-sm font-medium text-gray-700">{{ $label }}</label>
                    </div>
                    <textarea wire:model="{{ $model }}" rows="3"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-{{ $color }}-400 focus:border-transparent transition bg-gray-50 resize-none"
                        placeholder="{{ $placeholder }}"></textarea>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-green-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-green-600 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">Contact Information</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">School Website</label>
                        <input type="url" wire:model="websiteUrl"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-400 transition bg-gray-50"
                            placeholder="https://www.yourschool.edu">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">School Email</label>
                        <input type="email" wire:model="schoolEmail"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-400 transition bg-gray-50"
                            placeholder="contact@school.edu">
                        @error('schoolEmail') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Mobile Number</label>
                        <input type="text" wire:model="schoolMobileNo" inputmode="numeric" pattern="[0-9]*"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-400 transition bg-gray-50"
                            placeholder="9876543210">
                        @error('schoolMobileNo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">Website Description</label>
                        <textarea wire:model="websiteInfo" rows="1"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-400 transition bg-gray-50 resize-none"
                            placeholder="Brief description about your school website..."></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600 mb-1.5">School Address</label>
                        <textarea wire:model="schoolAddress" rows="2"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-400 transition bg-gray-50 resize-none"
                            placeholder="Full address with city, state, and pin code..."></textarea>
                        @error('schoolAddress') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Management Team --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-amber-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-amber-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">Management Team</h2>
                </div>
                <span class="text-xs font-medium bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full">
                    {{ count($schoolManagement) }} members
                </span>
            </div>
            <div class="px-6 py-5">
                @if (count($schoolManagement) === 0)
                <div class="text-center py-10 border-2 border-dashed border-gray-200 rounded-xl">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm mb-4">No management members added yet</p>
                    <button wire:click="addManagement"
                        class="px-5 py-2.5 bg-amber-500 text-white text-sm font-medium rounded-xl hover:bg-amber-600 transition">
                        Add First Member
                    </button>
                </div>
                @else
                <div class="space-y-3">
                    @foreach ($schoolManagement as $index => $member)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-amber-200 transition group">
                        <div class="relative flex-shrink-0">
                            @if (isset($member['photo']))
                            <img src="{{ $member['photo']->temporaryUrl() }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow">
                            @elseif (!empty($member['photo_path']))
                            <img src="{{ $member['photo_path'] }}" class="w-14 h-14 rounded-full object-cover border-2 border-white shadow">
                            @else
                            <div class="w-14 h-14 rounded-full bg-indigo-100 border-2 border-white shadow flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            @endif
                            <label class="absolute -bottom-1 -right-1 w-5 h-5 bg-indigo-600 rounded-full flex items-center justify-center cursor-pointer shadow">
                                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                <input type="file" class="hidden" wire:model="schoolManagement.{{ $index }}.photo">
                            </label>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Full Name</label>
                                <input type="text" wire:model="schoolManagement.{{ $index }}.name"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white transition"
                                    placeholder="Enter full name">
                                @error("schoolManagement.$index.name") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Designation</label>
                                <input type="text" wire:model="schoolManagement.{{ $index }}.designation"
                                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white transition"
                                    placeholder="e.g., Principal">
                                @error("schoolManagement.$index.designation") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <button wire:click="removeManagement({{ $index }})"
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                @endif
                <button wire:click="addManagement"
                    class="mt-4 w-full py-3 border-2 border-dashed border-gray-200 rounded-xl text-sm font-medium text-gray-500 hover:border-amber-400 hover:text-amber-600 hover:bg-amber-50 transition flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Add Management Member
                </button>
            </div>
        </div>

        {{-- Documents --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-red-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-pink-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">School Documents</h2>
                </div>
                <span class="text-xs font-medium bg-red-100 text-red-700 px-2.5 py-1 rounded-full">
                    {{ count($uploadedDocuments) + count($documentFiles) }} files
                </span>
            </div>
            <div class="px-6 py-5">
                @if (count($uploadedDocuments) > 0)
                <div class="mb-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Saved Documents</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach ($uploadedDocuments as $document)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-red-200 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800 truncate max-w-[120px]">{{ $document['title'] }}</p>
                                    <p class="text-xs text-gray-400">{{ strtoupper($document['file_type']) }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ $document['file_path'] }}" target="_blank"
                                    class="px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">View</a>
                                <button wire:click="removeDocument({{ $document['id'] }})"
                                    class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-lg hover:bg-red-100 hover:text-red-600 transition">Delete</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if (count($documentFiles) > 0)
                <div class="mb-5">
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">New Files (Pending Save)</p>
                    <div class="space-y-2">
                        @foreach ($documentFiles as $index => $file)
                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 truncate">{{ $file->getClientOriginalName() }}</span>
                                <button wire:click="removeUploadedFile({{ $index }})" class="ml-2 text-gray-400 hover:text-red-500 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                            <input type="text" wire:model="documentTitles.{{ $index }}"
                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-amber-400 focus:outline-none bg-white transition"
                                placeholder="Enter document title">
                            @error('documentTitles.' . $index) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <label class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-pink-500 text-white text-sm font-medium rounded-xl hover:from-red-600 hover:to-pink-600 transition shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    {{ count($documentFiles) > 0 ? 'Add More Documents' : 'Upload Documents' }}
                    <input type="file" class="hidden" wire:model="documentFiles" multiple>
                </label>
                @if (count($documentFiles) > 0)
                <p class="text-xs text-gray-400 mt-2">⚠ Save current documents before uploading more.</p>
                @endif
                @error('documentFiles.*') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex justify-end pt-2 pb-6">
            <button wire:click="saveSchoolInfo"
                class="flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition shadow-lg hover:shadow-xl">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Save All Information
            </button>
        </div>
        @endif

        {{-- ===================== VIEW TAB ===================== --}}
        @if ($activeTab === 'view')

        {{-- School Header Banner (About App hero style) --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="h-32 bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500"></div>
            <div class="px-8 pb-8 -mt-12">
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    @if ($organization->logo)
                    <img src="{{ $organization->logo }}" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg">
                    @else
                    <div class="w-24 h-24 rounded-2xl bg-indigo-100 border-4 border-white shadow-lg flex items-center justify-center">
                        <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.636 50.636 0 00-2.658-.813A59.906 59.906 0 0112 3.493a59.903 59.903 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                        </svg>
                    </div>
                    @endif
                    <div class="sm:mb-2">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $organization->name }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $organization->education_board ?? '' }}</p>
                        <div class="flex flex-wrap gap-4 mt-2">
                            @if ($schoolEmail)
                            <span class="flex items-center gap-1.5 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                {{ $schoolEmail }}
                            </span>
                            @endif
                            @if ($schoolMobileNo)
                            <span class="flex items-center gap-1.5 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                                {{ $schoolMobileNo }}
                            </span>
                            @endif
                            @if ($schoolAddress)
                            <span class="flex items-center gap-1.5 text-sm text-gray-500">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                                {{ $schoolAddress }}
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- About School --}}
        @if ($aboutSchool)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-indigo-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">About Our School</h2>
            </div>
            <div class="px-6 py-5">
                <p class="text-sm text-gray-600 leading-relaxed">{{ $aboutSchool }}</p>
            </div>
        </div>
        @endif

        {{-- USM Cards (About App content style) --}}
        @if ($usmVision || $usmMission || $usmValues || $usmGoals)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach ([
            ['Vision', $usmVision, 'indigo', 'M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
            ['Mission', $usmMission, 'green', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['Core Values', $usmValues, 'purple', 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z'],
            ['Goals & Objectives', $usmGoals, 'orange', 'M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941'],
            ] as [$label, $value, $color, $icon])
            @if ($value)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-{{ $color }}-200 hover:shadow-md transition-all duration-200">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-{{ $color }}-50 to-{{ $color }}-100/50 flex items-center gap-3">
                    <div class="w-8 h-8 bg-{{ $color }}-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900">{{ $label }}</h3>
                </div>
                <div class="px-6 py-5">
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $value }}</p>
                </div>
            </div>
            @endif
            @endforeach
        </div>
        @endif

        {{-- Contact Details (About App contact style) --}}
        @if ($schoolEmail || $schoolMobileNo || $schoolAddress)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-blue-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">Contact Details</h2>
            </div>
            <div class="px-6 py-5 space-y-3">
                @if ($schoolEmail)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-100 hover:bg-blue-50 transition-all duration-200">
                    <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-blue-500">Email</p>
                        <a href="mailto:{{ $schoolEmail }}" class="text-sm font-medium text-gray-800 hover:text-blue-600 transition-colors truncate block">{{ $schoolEmail }}</a>
                    </div>
                    <a href="mailto:{{ $schoolEmail }}" class="flex-shrink-0 w-8 h-8 bg-white border border-gray-200 rounded-lg flex items-center justify-center hover:border-blue-300 hover:bg-blue-50 transition-colors">
                        <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
                @endif
                @if ($schoolMobileNo)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-green-100 hover:bg-green-50 transition-all duration-200">
                    <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-green-500">Phone</p>
                        <a href="tel:{{ $schoolMobileNo }}" class="text-sm font-medium text-gray-800 hover:text-green-600 transition-colors truncate block">{{ $schoolMobileNo }}</a>
                    </div>
                    <a href="tel:{{ $schoolMobileNo }}" class="flex-shrink-0 w-8 h-8 bg-white border border-gray-200 rounded-lg flex items-center justify-center hover:border-green-300 hover:bg-green-50 transition-colors">
                        <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </a>
                </div>
                @endif
                @if ($schoolAddress)
                <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-gray-200 transition-all duration-200">
                    <div class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Address</p>
                        <p class="text-sm font-medium text-gray-800">{{ $schoolAddress }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Management Team (About App core team style) --}}
        @if (count($schoolManagement) > 0)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-amber-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Management Team</h2>
                    <p class="text-xs text-gray-400">{{ count($schoolManagement) }} members</p>
                </div>
            </div>
            <div class="px-6 py-6">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @foreach ($schoolManagement as $member)
                    <div class="group flex flex-col items-center text-center p-4 bg-gray-50 rounded-2xl border border-gray-200 hover:border-amber-300 hover:shadow-md hover:bg-amber-50 transition-all duration-200">
                        @if (!empty($member['photo_path']))
                        <img src="{{ $member['photo_path'] }}" alt="{{ $member['name'] ?? '' }}"
                            class="w-16 h-16 rounded-full object-cover border-2 border-white shadow-md mb-3 group-hover:scale-105 transition-transform duration-200">
                        @else
                        <div class="w-16 h-16 rounded-full bg-amber-100 border-2 border-white shadow-md mb-3 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        @endif
                        <p class="font-semibold text-sm text-gray-800 leading-tight">{{ $member['name'] ?? '' }}</p>
                        @if (!empty($member['designation']))
                        <p class="text-xs text-gray-500 mt-0.5">{{ $member['designation'] }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        {{-- Documents (About App style) --}}
        @if (count($uploadedDocuments) > 0)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-red-200 hover:shadow-md transition-all duration-200">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-red-50 to-pink-50 flex items-center gap-3">
                <div class="w-8 h-8 bg-red-500 rounded-xl flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h2 class="text-base font-semibold text-gray-900">School Documents</h2>
            </div>
            <div class="px-6 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($uploadedDocuments as $document)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 transition-all duration-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $document['title'] }}</p>
                                <p class="text-xs text-gray-400">{{ strtoupper($document['file_type']) }} Document</p>
                            </div>
                        </div>
                        <a href="{{ $document['file_path'] }}" target="_blank"
                            class="flex-shrink-0 w-8 h-8 bg-white border border-gray-200 rounded-lg flex items-center justify-center hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                            <svg class="w-3.5 h-3.5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        @endif

    </div>
</div>