<div class="p-6 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between bg-white rounded-2xl border border-gray-100 px-6 py-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Profile</h1>
            <p class="text-sm text-gray-400 mt-0.5">Your account and organization details</p>
        </div>
        <div class="flex items-center gap-3 text-sm text-gray-500">
            <x-icon name="calendar-days" class="w-4 h-4 text-gray-400" />
            {{ now()->format('l, d M Y') }}
        </div>
    </div>

    {{-- Combined User + Staff Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

        {{-- Top: Avatar + Name + Status --}}
        <div class="px-6 py-5 flex items-center gap-4 border-b border-gray-50">
            @if (!empty($schoolUser['image']))
                <img src="{{ $schoolUser['image'] }}" alt="{{ $user['name'] }}"
                    class="w-16 h-16 rounded-full object-cover ring-2 ring-gray-100 flex-shrink-0">
            @else
                <div
                    class="w-16 h-16 rounded-full bg-purple-50 flex items-center justify-center flex-shrink-0 ring-2 ring-purple-100">
                    <span class="text-xl font-semibold text-purple-600">
                        {{ strtoupper(substr($user['name'] ?? 'U', 0, 1)) }}
                    </span>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <h2 class="text-base font-semibold text-gray-900 truncate">{{ $user['name'] ?? '-' }}</h2>
                <p class="text-sm text-gray-400 truncate">{{ $user['email'] ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-600">
                    {{ ucfirst($user['role'] ?? '-') }}
                </span>
                @if (!empty($schoolUser))
                    <span
                        class="px-2.5 py-1 rounded-full text-xs font-medium
                        {{ $schoolUser['is_active'] ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-500' }}">
                        {{ $schoolUser['is_active'] ? 'Active' : 'Inactive' }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Details Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-0 divide-y sm:divide-y-0 sm:divide-x divide-gray-50">

            {{-- Left: Account Info --}}
            <div class="px-6 py-5 space-y-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Account</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Mobile</span>
                        <span class="text-sm text-gray-700">{{ $user['phone'] ?? '-' }}</span>
                    </div>
                    @if (!empty($schoolUser['alternate_mobile']) && $schoolUser['alternate_mobile'] !== '-')
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-400">Alternate Mobile</span>
                            <span class="text-sm text-gray-700">{{ $schoolUser['alternate_mobile'] }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Member Since</span>
                        <span class="text-sm text-gray-700">{{ $user['created_at'] ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Right: Staff Info --}}
            <div class="px-6 py-5 space-y-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Staff</p>
                @if (!empty($schoolUser))
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-400">Employee ID</span>
                            <span class="text-sm text-gray-700">{{ $schoolUser['employee_id'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-400">Designation</span>
                            <span class="text-sm text-gray-700">{{ $schoolUser['designation'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-400">Department</span>
                            <span class="text-sm text-gray-700">{{ $schoolUser['department'] }}</span>
                        </div>
                        @if (!empty($schoolUser['address']) && $schoolUser['address'] !== '-')
                            <div class="flex justify-between items-start gap-4">
                                <span class="text-sm text-gray-400 flex-shrink-0">Address</span>
                                <span class="text-sm text-gray-700 text-right">{{ $schoolUser['address'] }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-sm text-gray-400">No staff profile found.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Organization Card --}}
    @if (!empty($organization))
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">

            {{-- Org Header --}}
            <div class="px-6 py-5 flex items-center gap-4 border-b border-gray-50">
                @if (!empty($organization['logo']))
                    <img src="{{ $organization['logo'] }}" alt="{{ $organization['name'] }}"
                        class="w-12 h-12 rounded-xl object-contain border border-gray-100 flex-shrink-0">
                @else
                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 border border-emerald-100">
                        <x-icon name="building-office-2" class="w-6 h-6 text-emerald-500" />
                    </div>
                @endif
                <div>
                    <h2 class="text-base font-semibold text-gray-900">{{ $organization['name'] }}</h2>
                    <p class="text-sm text-gray-400">Organization details</p>
                </div>
            </div>

            {{-- Org Details Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-0 divide-y sm:divide-y-0 sm:divide-x divide-gray-50">

                {{-- Left column --}}
                <div class="px-6 py-5 space-y-3">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Contact</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Email</span>
                        <span class="text-sm text-gray-700">{{ $organization['email'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Phone</span>
                        <span class="text-sm text-gray-700">{{ $organization['phone'] }}</span>
                    </div>
                    <div class="flex justify-between items-start gap-4">
                        <span class="text-sm text-gray-400 flex-shrink-0">Address</span>
                        <span class="text-sm text-gray-700 text-right">{{ $organization['address'] }}</span>
                    </div>
                </div>

                {{-- Right column --}}
                <div class="px-6 py-5 space-y-3">
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider mb-4">Identifiers</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Code</span>
                        <span class="text-sm text-gray-700 font-mono">{{ $organization['code'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Affiliation No.</span>
                        <span class="text-sm text-gray-700 font-mono">{{ $organization['affiliation_number'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">Serial No.</span>
                        <span class="text-sm text-gray-700 font-mono">{{ $organization['serial_number'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-400">UDISE No.</span>
                        <span class="text-sm text-gray-700 font-mono">{{ $organization['udise_number'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
