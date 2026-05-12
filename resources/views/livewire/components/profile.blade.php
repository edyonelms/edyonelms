<div>
    <!-- Header Tabs - Only show when not in view mode -->
    <div class="flex justify-center rounded-full items-center p-6">
        <div class="flex gap-2 bg-white p-1 rounded-lg shadow-md">
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'profile' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('profile')">School Profile</button>
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'info' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('info')">Edit School Info</button>
            <button
                class="px-6 py-2 rounded-md transition-all {{ $activeTab === 'view' ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                wire:click="showTab('view')">View School Info</button>
        </div>
    </div>

    @if ($activeTab === 'profile')
        <!-- School Profile Content -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-lg shadow-md">
            <!-- Profile Photo Section -->
            <div class="md:col-span-1">
                <div class="bg-white p-6 rounded-lg border border-gray-200">
                    <div class="text-center">
                        @if ($organization->logo)
                            <img src="{{ $organization->logo }}"
                                class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-md mb-4">
                        @else
                            <div
                                class="w-32 h-32 rounded-full mx-auto bg-gray-200 flex items-center justify-center text-gray-500 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        <div x-data="{ isUploading: false }" x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false">

                            @if ($tempPhotoUrl)
                                <img src="{{ $tempPhotoUrl }}"
                                    class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-white shadow-md mb-4">
                            @endif

                            <label class="cursor-pointer">
                                <span
                                    class="block px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition duration-300 text-sm font-medium">
                                    @if ($photo)
                                        Change Photo
                                    @else
                                        Upload Photo
                                    @endif
                                </span>
                                <input type="file" class="hidden" wire:model="photo">
                            </label>

                            <div class="mt-2 text-xs text-gray-500">
                                JPG, PNG or GIF (Max 2MB)
                            </div>

                            @error('photo')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror

                            @if ($photo && !$tempPhotoUrl)
                                <div wire:loading wire:target="photo" class="mt-2 text-sm text-gray-500">
                                    Uploading...
                                </div>
                            @endif

                            @if ($photo)
                                <button wire:click="savePhoto"
                                    class="mt-2 px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition duration-300 text-sm font-medium w-full">
                                    Save Photo
                                </button>
                            @endif

                            @if (session()->has('photo_message'))
                                <div class="mt-2 p-2 text-sm text-green-700 bg-green-100 rounded">
                                    {{ session('photo_message') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Organization Details and Password Section -->
            <div class="md:col-span-2 space-y-4">
                <!-- Organization Details -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">School Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">School Name</label>
                            <p class="mt-1 text-gray-800">{{ $organization->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Email</label>
                            <p class="mt-1 text-gray-800">{{ $organization->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Mobile Number</label>
                            <p class="mt-1 text-gray-800">{{ $organization->mobile_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">State</label>
                            <p class="mt-1 text-gray-800">{{ $organization->state ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Education Board</label>
                            <p class="mt-1 text-gray-800">{{ $organization->education_board ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">School Code</label>
                            <p class="mt-1 text-gray-800">{{ $organization->school_code ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Serial number</label>
                            <p class="mt-1 text-gray-800">{{ $organization->serial_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Address</label>
                            <p class="mt-1 text-gray-800">{{ $organization->address ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Password Change Section -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Change Password</h2>

                    <div class="space-y-4">
                        <!-- Current Password -->
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Current Password</label>
                            <div class="relative">
                                <input wire:model="currentPassword"
                                    type="{{ $showCurrentPassword ? 'text' : 'password' }}"
                                    placeholder="Enter current password"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                <button type="button" wire:click="togglePasswordVisibility('current')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        @if ($showCurrentPassword)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        @endif
                                    </svg>
                                </button>
                            </div>
                            @error('currentPassword')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">New Password</label>
                            <div class="relative">
                                <input wire:model="newPassword" type="{{ $showNewPassword ? 'text' : 'password' }}"
                                    placeholder="Enter new password"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                <button type="button" wire:click="togglePasswordVisibility('new')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        @if ($showNewPassword)
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
                            @error('newPassword')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-1">Confirm Password</label>
                            <div class="relative">
                                <input wire:model="confirmPassword"
                                    type="{{ $showConfirmPassword ? 'text' : 'password' }}"
                                    placeholder="Confirm new password"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent pr-10">
                                <button type="button" wire:click="togglePasswordVisibility('confirm')"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        @if ($showConfirmPassword)
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268-2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268-2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        @endif
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Update Button -->
                        <button wire:click="updatePassword"
                            class="w-full py-3 bg-gradient-3 text-white rounded-lg hover:bg-gradient-3-hover transition duration-300 shadow-md hover:shadow-lg">
                            Update Password
                        </button>

                        @if (session()->has('password_message'))
                            <div class="p-2 text-sm text-green-700 bg-green-100 rounded-lg">
                                {{ session('password_message') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($activeTab === 'info')
        <!-- School Info Content with USM Parameters -->
        <div class="max-w-6xl mx-auto p-4 md:p-6">
            <div class="space-y-8">
                <!-- Page Header -->
                <div class="mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Edit School Information</h1>
                    <p class="text-gray-600 mt-2">Update your school's details, vision, mission, and other important
                        information.</p>
                </div>

                <!-- About School Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">About School</h2>
                        </div>
                    </div>
                    <div class="p-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">School Description</label>
                        <textarea wire:model="aboutSchool" rows="4"
                            class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                            placeholder="Describe your school's history, achievements, facilities, and unique features..."></textarea>
                        <p class="text-sm text-gray-500 mt-2">This will be displayed on your school's profile page.</p>
                    </div>
                </div>

                <!-- USM Parameters Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">School Vision & Mission</h2>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Vision -->
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-blue-100 rounded-md flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <label class="text-sm font-medium text-gray-700">Vision</label>
                            </div>
                            <textarea wire:model="usmVision" rows="3"
                                class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200"
                                placeholder="What is the long-term vision for your school?"></textarea>
                        </div>

                        <!-- Mission -->
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-green-100 rounded-md flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <label class="text-sm font-medium text-gray-700">Mission</label>
                            </div>
                            <textarea wire:model="usmMission" rows="3"
                                class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                placeholder="What is the mission statement of your school?"></textarea>
                        </div>

                        <!-- Values -->
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-purple-100 rounded-md flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </div>
                                <label class="text-sm font-medium text-gray-700">Core Values</label>
                            </div>
                            <textarea wire:model="usmValues" rows="3"
                                class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition duration-200"
                                placeholder="What core values does your school uphold?"></textarea>
                        </div>

                        <!-- Goals -->
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-orange-100 rounded-md flex items-center justify-center mr-2">
                                    <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <label class="text-sm font-medium text-gray-700">Goals & Objectives</label>
                            </div>
                            <textarea wire:model="usmGoals" rows="3"
                                class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition duration-200"
                                placeholder="What are the key goals and objectives of your school?"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Contact Information Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-green-600 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <h2 class="text-xl font-semibold text-gray-900">Contact Information</h2>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Website URL -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    School Website
                                </label>
                                <input type="url" wire:model="websiteUrl"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                    placeholder="https://www.yourschool.edu">
                            </div>

                            <!-- Website Info -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Website Description
                                </label>
                                <textarea wire:model="websiteInfo" rows="3"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                    placeholder="Brief description about your school website..."></textarea>
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    School Email
                                </label>
                                <input type="email" wire:model="schoolEmail"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                    placeholder="contact@school.edu">
                                @error('schoolEmail')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Mobile Number -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    Mobile Number
                                </label>
                                <input type="text" wire:model="schoolMobileNo" inputmode="numeric"
                                    pattern="[0-9]*"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                    placeholder="9876543210">
                                @error('schoolMobileNo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-medium text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    School Address
                                </label>
                                <textarea wire:model="schoolAddress" rows="3"
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition duration-200"
                                    placeholder="Full school address with city, state, and pin code..."></textarea>
                                @error('schoolAddress')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Management Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-amber-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-yellow-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">School Management Team</h2>
                            </div>
                            <span class="text-sm text-gray-500">{{ count($schoolManagement) }} members</span>
                        </div>
                    </div>
                    <div class="p-6">
                        @if (count($schoolManagement) === 0)
                            <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                <p class="text-gray-500 mb-4">No management members added yet</p>
                                <button wire:click="addManagement"
                                    class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition duration-200">
                                    Add First Member
                                </button>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach ($schoolManagement as $index => $member)
                                    <div
                                        class="bg-gray-50 p-4 rounded-lg border border-gray-200 hover:border-yellow-300 transition duration-200">
                                        <div class="grid grid-cols-12 gap-4 items-center">
                                            <!-- Photo -->
                                            <div class="col-span-2 flex items-center">
                                                <div class="relative">
                                                    @if (isset($member['photo']))
                                                        <img src="{{ $member['photo']->temporaryUrl() }}"
                                                            class="h-16 w-16 rounded-full object-cover border-2 border-white shadow">
                                                    @elseif(!empty($member['photo_path']))
                                                        <img src="{{ $member['photo_path'] }}"
                                                            class="h-16 w-16 rounded-full object-cover border-2 border-white shadow">
                                                    @else
                                                        <div
                                                            class="h-16 w-16 rounded-full bg-gray-200 border-2 border-gray-300 flex items-center justify-center">
                                                            <svg class="h-8 w-8 text-gray-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="1"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    <label
                                                        class="absolute -bottom-1 -right-1 bg-white p-1 rounded-full shadow-md cursor-pointer hover:bg-gray-50 transition duration-200">
                                                        <input type="file" class="hidden"
                                                            wire:model="schoolManagement.{{ $index }}.photo">
                                                        <svg class="h-4 w-4 text-blue-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    </label>
                                                </div>
                                            </div>

                                            <!-- Name -->
                                            <div class="col-span-4">
                                                <label
                                                    class="block text-xs font-medium text-gray-500 mb-1">Name</label>
                                                <input type="text"
                                                    wire:model="schoolManagement.{{ $index }}.name"
                                                    class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200"
                                                    placeholder="Enter full name">
                                                @error("schoolManagement.$index.name")
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Designation -->
                                            <div class="col-span-4">
                                                <label
                                                    class="block text-xs font-medium text-gray-500 mb-1">Designation</label>
                                                <input type="text"
                                                    wire:model="schoolManagement.{{ $index }}.designation"
                                                    class="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200"
                                                    placeholder="e.g., Principal, Director">
                                                @error("schoolManagement.$index.designation")
                                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <!-- Actions -->
                                            <div class="col-span-2 flex justify-end space-x-2">
                                                @if (!empty($member['photo_path']) || isset($member['photo']))
                                                    <button wire:click="removeManagementPhoto({{ $index }})"
                                                        class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition duration-200"
                                                        title="Remove photo">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                @endif
                                                <button wire:click="removeManagement({{ $index }})"
                                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition duration-200"
                                                    title="Remove member">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <button wire:click="addManagement"
                            class="mt-6 w-full py-3 border-2 border-dashed border-gray-300 rounded-lg hover:border-yellow-400 hover:bg-yellow-50 transition duration-200 flex items-center justify-center text-gray-600 hover:text-yellow-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Management Member
                        </button>
                    </div>
                </div>

                <!-- School Documents Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-pink-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-900">School Documents</h2>
                            </div>
                            <span
                                class="text-sm text-gray-500">{{ count($uploadedDocuments) + count($documentFiles) }}
                                files</span>
                        </div>
                    </div>
                    <div class="p-6">
                        @if (count($uploadedDocuments) === 0 && count($documentFiles) === 0)
                            <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-gray-500 mb-4">No documents uploaded yet</p>
                                <label
                                    class="cursor-pointer inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Upload Documents
                                    <input type="file" class="hidden" wire:model="documentFiles" multiple>
                                </label>
                            </div>
                        @else
                            <!-- Existing Documents -->
                            @if (count($uploadedDocuments) > 0)
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-700 mb-3">Saved Documents</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach ($uploadedDocuments as $document)
                                            <div
                                                class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200 hover:border-red-200 transition duration-200">
                                                <div class="flex items-center">
                                                    <div
                                                        class="w-8 h-8 bg-red-100 rounded-md flex items-center justify-center mr-3">
                                                        <svg class="w-4 h-4 text-red-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="font-medium text-gray-800">{{ $document['title'] }}
                                                        </h4>
                                                        <p class="text-xs text-gray-500">
                                                            {{ strtoupper($document['file_type']) }} • Saved</p>
                                                    </div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <a href="{{ $document['file_path'] }}" target="_blank"
                                                        class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 transition duration-200 text-sm">
                                                        View
                                                    </a>
                                                    <button wire:click="removeDocument({{ $document['id'] }})"
                                                        class="px-3 py-1 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition duration-200 text-sm">
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Uploaded Files (Not Saved) -->
                            @if (count($documentFiles) > 0)
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-700 mb-3">New Files to Save</h3>
                                    <div class="space-y-3">
                                        @foreach ($documentFiles as $index => $file)
                                            <div class="p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                                <div class="flex items-center justify-between mb-2">
                                                    <div class="flex items-center">
                                                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <span
                                                            class="font-medium text-gray-700">{{ $file->getClientOriginalName() }}</span>
                                                    </div>
                                                    <button wire:click="removeUploadedFile({{ $index }})"
                                                        class="text-red-600 hover:text-red-800">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div class="flex items-center space-x-3">
                                                    <input type="text"
                                                        wire:model="documentTitles.{{ $index }}"
                                                        class="flex-1 p-2 border border-gray-300 rounded focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition duration-200"
                                                        placeholder="Enter document title">
                                                    <span class="text-xs text-gray-500 px-2 py-1 bg-gray-100 rounded">
                                                        {{ strtoupper($file->getClientOriginalExtension()) }}
                                                    </span>
                                                </div>
                                                @error('documentTitles.' . $index)
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Upload Button -->
                            <div class="mt-6">
                                <label
                                    class="cursor-pointer inline-flex items-center px-4 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-lg hover:from-red-700 hover:to-pink-700 transition duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Add More Documents
                                    <input type="file" class="hidden" wire:model="documentFiles" multiple>
                                </label>
                                <p class="text-sm text-gray-500 mt-3">
                                    <span class="font-medium text-red-600">*Note:</span>
                                    Please save the documents you have selected first, then upload additional documents.
                                </p>
                                @error('documentFiles.*')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end pt-6">
                    <button wire:click="saveSchoolInfo"
                        class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition duration-300 shadow-lg hover:shadow-xl flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        Save All Information
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($activeTab === 'view')
        <!-- View School Info Screen -->
        <div class="max-w-6xl mx-auto p-6">
            <!-- School Header -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6 border border-gray-200">
                <div class="flex items-center space-x-6">
                    @if ($organization->logo)
                        <img src="{{ $organization->logo }}"
                            class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                    @else
                        <div
                            class="w-24 h-24 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">{{ $organization->name }}</h1>
                        <p class="text-gray-600 mt-2">{{ $organization->address }}</p>
                        <div class="flex flex-wrap gap-4 mt-3">
                            <span class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ $schoolEmail ?? 'N/A' }}
                            </span>
                            <span class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                {{ $schoolMobileNo ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- About School -->
            @if ($aboutSchool)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2">About Our School</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $aboutSchool }}</p>
                </div>
            @endif

            <!-- USM Parameters -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                @if ($usmVision)
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Vision</h3>
                        </div>
                        <p class="text-gray-700">{{ $usmVision }}</p>
                    </div>
                @endif

                @if ($usmMission)
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Mission</h3>
                        </div>
                        <p class="text-gray-700">{{ $usmMission }}</p>
                    </div>
                @endif

                @if ($usmValues)
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Values</h3>
                        </div>
                        <p class="text-gray-700">{{ $usmValues }}</p>
                    </div>
                @endif

                @if ($usmGoals)
                    <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800">Goals</h3>
                        </div>
                        <p class="text-gray-700">{{ $usmGoals }}</p>
                    </div>
                @endif


            </div>
            @if ($aboutSchool)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-orange-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800">School Website Info</h3>
                    </div>
                    <p class="text-gray-700">{{ $aboutSchool }}</p>
                </div>
            @endif

            <!-- Management Team -->
            @if (count($schoolManagement) > 0)
                <div class="bg-white rounded-lg shadow-md p-6 mb-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">Management Team</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($schoolManagement as $member)
                            <div class="text-center bg-gray-50 rounded-lg p-4 border border-gray-200">
                                @if (!empty($member['photo_path']))
                                    <img src="{{ $member['photo_path'] }}"
                                        class="w-20 h-20 rounded-full mx-auto  border-1 border-white shadow-md mb-3">
                                @else
                                    <div
                                        class="w-20 h-20 rounded-full bg-gray-200 mx-auto flex items-center justify-center border-1 border-white shadow-md mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                @endif
                                <h3 class="font-semibold text-gray-800">{{ $member['name'] }}</h3>
                                <p class="text-sm text-gray-600">{{ $member['designation'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Documents -->
            @if (count($uploadedDocuments) > 0)
                <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">School Documents</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($uploadedDocuments as $document)
                            <div
                                class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-800">{{ $document['title'] }}</h4>
                                        <p class="text-sm text-gray-500">{{ strtoupper($document['file_type']) }}
                                            Document</p>
                                    </div>
                                </div>
                                <a href="{{ $document['file_path'] }}" target="_blank"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-300 text-sm">
                                    View
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
