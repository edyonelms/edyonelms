<div class="min-h-screen bg-gray-50">

    <!-- ══════════════════ HEADER ══════════════════ -->
    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-200">
        <div class="px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Transport Management</h1>
                    <p class="mt-1 text-sm text-gray-600">Manage routes, drivers and student assignments</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    @if ($activeTab === 'transportation')
                        <button wire:click="createTransport"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Route
                        </button>
                    @else
                        <button wire:click="createDriver"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Add Driver
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="p-6">

        <!-- ══════════════════ STATS ══════════════════ -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white border-l-4 border-l-blue-500 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Routes</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->statistics['routes'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-full">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-l-4 border-l-green-500 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Drivers</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->statistics['drivers'] }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-full">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-l-4 border-l-purple-500 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Students Assigned</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $this->statistics['students'] }}</p>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-full">
                        <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white border-l-4 border-l-orange-500 shadow-sm rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            ₹{{ number_format($this->statistics['monthly_revenue'], 0) }}</p>
                    </div>
                    <div class="p-3 bg-orange-50 rounded-full">
                        <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- ══════════════════ TABS ══════════════════ -->
        <div class="mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8">
                    <button wire:click="$set('activeTab', 'transportation')"
                        class="py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'transportation' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 inline mr-1.5 mb-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        Transportation Routes
                    </button>
                    <button wire:click="$set('activeTab', 'drivers')"
                        class="py-3 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'drivers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <svg class="w-4 h-4 inline mr-1.5 mb-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Drivers
                    </button>
                </nav>
            </div>
        </div>

        <!-- ══════════════════ FILTERS ══════════════════ -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="{{ $activeTab === 'transportation' ? 'Search routes, vehicle no...' : 'Search drivers...' }}"
                            class="w-full border rounded-lg px-4 py-2 pl-10 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <div>
                    <select wire:model.live="filterStatus"
                        class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div>
                    <select wire:model.live="perPage"
                        class="w-full border rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="10">10 per page</option>
                        <option value="25">25 per page</option>
                        <option value="50">50 per page</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ══════════════════ TRANSPORTATION TAB ══════════════════ -->
        @if ($activeTab === 'transportation')
            <div class="space-y-4">
                @forelse ($transportations as $transport)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">

                        <!-- Route Header -->
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-cyan-50 border-b border-gray-100">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="p-2.5 bg-blue-100 rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $transport->route_name }}</h3>
                                        <div class="flex items-center gap-3 mt-1 flex-wrap">
                                            @if ($transport->vehicle_no)
                                                <span
                                                    class="text-xs px-2 py-0.5 bg-white border border-gray-200 rounded font-mono text-gray-700">
                                                    🚌 {{ $transport->vehicle_no }}
                                                </span>
                                            @endif
                                            @if ($transport->vehicle_type)
                                                <span
                                                    class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded">{{ $transport->vehicle_type }}</span>
                                            @endif
                                            <span
                                                class="text-xs px-2 py-0.5 rounded-full font-medium {{ $transport->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $transport->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button wire:click="openAssignStudents({{ $transport->id }})"
                                        class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-medium transition flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Assign Students
                                    </button>
                                    <button wire:click="editTransport({{ $transport->id }})"
                                        class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="toggleTransportStatus({{ $transport->id }})"
                                        class="p-1.5 {{ $transport->is_active ? 'text-orange-500 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition"
                                        title="{{ $transport->is_active ? 'Deactivate' : 'Activate' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                    </button>
                                    <button wire:click="deleteTransport({{ $transport->id }})"
                                        class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Route Details -->
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm mb-4">
                                <!-- Route path -->
                                <div class="md:col-span-2">
                                    <p class="text-xs font-medium text-gray-500 mb-1">ROUTE</p>
                                    <div class="flex items-center gap-2 text-gray-700">
                                        <span class="font-medium">{{ $transport->pickup_location ?: '—' }}</span>
                                        @if ($transport->drop_location)
                                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                            </svg>
                                            <span class="font-medium">{{ $transport->drop_location }}</span>
                                        @endif
                                    </div>
                                    @if ($transport->stops && count($transport->stops))
                                        <p class="text-xs text-gray-500 mt-1">Stops:
                                            {{ implode(' → ', $transport->stops) }}</p>
                                    @endif
                                </div>

                                <!-- Driver -->
                                <div>
                                    <p class="text-xs font-medium text-gray-500 mb-1">DRIVER</p>
                                    @if ($transport->driver)
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <svg class="w-3.5 h-3.5 text-green-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900 text-xs">
                                                    {{ $transport->driver->user->name ?? '—' }}</p>
                                                <p class="text-xs text-gray-500">{{ $transport->driver->phone ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs text-red-500 font-medium">⚠ No driver assigned</span>
                                    @endif
                                </div>

                                <!-- Fee & Capacity -->
                                <div>
                                    <p class="text-xs font-medium text-gray-500 mb-1">FEE / CAPACITY</p>
                                    <p class="font-bold text-gray-900">
                                        ₹{{ number_format($transport->monthly_fee, 0) }}<span
                                            class="text-xs font-normal text-gray-500">/month</span></p>
                                    <p class="text-xs text-gray-500">
                                        {{ $transport->students->count() }} / {{ $transport->capacity ?: '∞' }}
                                        students
                                    </p>
                                    @if ($transport->capacity && $transport->students->count() >= $transport->capacity)
                                        <span class="text-xs text-red-500 font-medium">Full</span>
                                    @elseif ($transport->capacity)
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 mt-1">
                                            <div class="bg-blue-500 h-1.5 rounded-full"
                                                style="width: {{ min(100, ($transport->students->count() / $transport->capacity) * 100) }}%">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Assigned Students -->
                            @if ($transport->students->count())
                                <div class="border-t border-gray-100 pt-3">
                                    <p class="text-xs font-medium text-gray-500 mb-2">ASSIGNED STUDENTS
                                        ({{ $transport->students->count() }})
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($transport->students->take(8) as $student)
                                            <div
                                                class="flex items-center gap-1.5 px-2.5 py-1 bg-purple-50 border border-purple-100 rounded-full text-xs text-purple-800">
                                                <span>{{ $student->full_name }}</span>
                                                <button
                                                    wire:click="removeStudent({{ $transport->id }}, {{ $student->id }})"
                                                    class="text-purple-400 hover:text-red-500 transition ml-0.5">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                        @if ($transport->students->count() > 8)
                                            <span
                                                class="px-2.5 py-1 bg-gray-100 rounded-full text-xs text-gray-600">+{{ $transport->students->count() - 8 }}
                                                more</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <h3 class="mt-3 text-sm font-medium text-gray-900">No routes found</h3>
                        <p class="mt-1 text-sm text-gray-500">Add your first transport route to get started.</p>
                        <button wire:click="createTransport"
                            class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                            Add Route
                        </button>
                    </div>
                @endforelse
            </div>

            @if ($transportations->hasPages())
                <div class="mt-6">{{ $transportations->links() }}</div>
            @endif
        @endif

        <!-- ══════════════════ DRIVERS TAB ══════════════════ -->
        @if ($activeTab === 'drivers')
            <div class="space-y-4">
                @forelse ($drivers as $driver)
                    <div
                        class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-6">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-4">
                                    <!-- Avatar -->
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span
                                            class="text-white font-bold text-lg">{{ strtoupper(substr($driver->user->name ?? 'D', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-base font-bold text-gray-900">
                                            {{ $driver->user->name ?? 'Unknown' }}</h3>
                                        <p class="text-sm text-gray-500">{{ $driver->user->email ?? '' }}</p>
                                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                            <span
                                                class="px-2 py-0.5 text-xs font-medium rounded-full {{ $driver->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $driver->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if ($driver->vehicle_type)
                                                <span
                                                    class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded">{{ $driver->vehicle_type }}</span>
                                            @endif
                                            @if ($driver->transportations->count())
                                                <span
                                                    class="text-xs px-2 py-0.5 bg-purple-100 text-purple-700 rounded">
                                                    {{ $driver->transportations->count() }} route(s)
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    <button wire:click="editDriver({{ $driver->id }})"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button wire:click="toggleDriverStatus({{ $driver->id }})"
                                        class="p-2 {{ $driver->is_active ? 'text-orange-500 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50' }} rounded-lg transition"
                                        title="{{ $driver->is_active ? 'Deactivate' : 'Activate' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="{{ $driver->is_active ? 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}" />
                                        </svg>
                                    </button>
                                    <button wire:click="deleteDriver({{ $driver->id }})"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 pt-4 border-t border-gray-100">
                                <div>
                                    <p class="text-xs text-gray-500">Phone</p>
                                    <p class="text-sm font-medium text-gray-800">{{ $driver->phone ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">License No.</p>
                                    <p class="text-sm font-medium text-gray-800 font-mono">
                                        {{ $driver->license_no ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Vehicle No.</p>
                                    <p class="text-sm font-medium text-gray-800 font-mono">
                                        {{ $driver->vehicle_no ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Experience</p>
                                    <p class="text-sm font-medium text-gray-800">{{ $driver->experience_years }} yr(s)
                                    </p>
                                </div>
                            </div>

                            @if ($driver->address)
                                <p class="mt-2 text-xs text-gray-500">📍 {{ $driver->address }}</p>
                            @endif

                            <!-- Assigned Routes -->
                            @if ($driver->transportations->count())
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-xs font-medium text-gray-500 mb-1.5">ASSIGNED ROUTES</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($driver->transportations as $t)
                                            <span
                                                class="px-2.5 py-1 bg-blue-50 border border-blue-100 text-xs text-blue-700 rounded-full">
                                                {{ $t->route_name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <h3 class="mt-3 text-sm font-medium text-gray-900">No drivers found</h3>
                        <p class="mt-1 text-sm text-gray-500">Add your first driver to get started.</p>
                        <button wire:click="createDriver"
                            class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-sm">
                            Add Driver
                        </button>
                    </div>
                @endforelse
            </div>

            @if ($drivers->hasPages())
                <div class="mt-6">{{ $drivers->links() }}</div>
            @endif
        @endif

    </div><!-- /p-6 -->

    <!-- ══════════════════ DRIVER MODAL ══════════════════ -->
    <x-modal-form show="{{ $driverModal }}" title="{{ $editDriverId ? 'Edit Driver' : 'Add New Driver' }}"
        submitAction="saveDriver" submitButton="{{ $editDriverId ? 'Update Driver' : 'Add Driver' }}"
        closeAction="closeDriverModal">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-input wire:model.defer="driver_name" label="Full Name *" required
                    placeholder="Driver's full name" />
            </div>

            <x-input wire:model.defer="driver_email" label="Email *" type="email" required
                placeholder="email@example.com" />
            <x-input wire:model.defer="driver_phone" label="Phone" placeholder="Mobile number" />

            <x-input wire:model.defer="license_no" label="License No." placeholder="DL-XXXXXXXXXX" />

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                <select wire:model.defer="driver_vehicle_type"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Type</option>
                    @foreach ($vehicleTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>

            <x-input wire:model.defer="driver_vehicle_no" label="Vehicle No." placeholder="MH-XX-XXXX" />
            <x-input wire:model.defer="experience_years" label="Experience (Years)" type="number" min="0" />

            <div class="sm:col-span-2">
                <x-textarea wire:model.defer="driver_address" label="Address" rows="2"
                    placeholder="Driver's address" />
            </div>

            @if ($editDriverId)
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-md">
                        <input type="checkbox" wire:model.defer="driver_is_active" id="driver_active"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="driver_active" class="text-sm font-medium text-gray-700">Active</label>
                    </div>
                </div>
            @endif
        </div>
    </x-modal-form>

    <!-- ══════════════════ TRANSPORT MODAL ══════════════════ -->
    <x-modal-form show="{{ $transportModal }}" title="{{ $editTransportId ? 'Edit Route' : 'Add New Route' }}"
        submitAction="saveTransport" submitButton="{{ $editTransportId ? 'Update Route' : 'Create Route' }}"
        closeAction="closeTransportModal">

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2" x-data="{
            selectedDriverId: @entangle('driver_detail_id').live,
            drivers: {{ json_encode(collect($availableDrivers)->keyBy('id')->toArray()) }},
            get selectedDriver() {
                return this.selectedDriverId ? this.drivers[this.selectedDriverId] : null;
            }
        }">

            {{-- Route Name --}}
            <div class="sm:col-span-2">
                <x-input wire:model.defer="route_name" label="Route Name *" required
                    placeholder="e.g., City Center – School" />
            </div>

            {{-- Driver (required) --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Assign Driver <span class="text-red-500">*</span>
                </label>
                <select wire:model.live="driver_detail_id" x-model="selectedDriverId"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">— Select Driver —</option>
                    @foreach ($availableDrivers as $d)
                        <option value="{{ $d['id'] }}">
                            {{ $d['name'] }}{{ $d['license_no'] ? ' · ' . $d['license_no'] : '' }}
                        </option>
                    @endforeach
                </select>
                @error('driver_detail_id')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror

                {{-- Vehicle info auto-pulled from driver --}}
                <div x-show="selectedDriver" x-cloak
                    class="mt-2 flex items-center gap-3 px-3 py-2 bg-blue-50 border border-blue-100 rounded-lg">
                    <svg class="w-4 h-4 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-xs text-blue-700">
                        Vehicle:
                        <span class="font-semibold" x-text="selectedDriver?.vehicle_type || '—'"></span>
                        <span class="font-mono ml-1"
                            x-text="selectedDriver?.vehicle_no ? '(' + selectedDriver.vehicle_no + ')' : ''"></span>
                    </p>
                </div>
            </div>

            {{-- Route --}}
            <x-input wire:model.defer="pickup_location" label="Pickup Location" placeholder="Starting point" />
            <x-input wire:model.defer="drop_location" label="Drop Location" placeholder="Ending point" />

            <div class="sm:col-span-2">
                <x-input wire:model.defer="stops_input" label="Intermediate Stops"
                    placeholder="Stop 1, Stop 2, Stop 3 (comma separated)" />
            </div>

            {{-- Fee & Capacity --}}
            <x-input wire:model.defer="monthly_fee" label="Monthly Fee (₹) *" type="number" min="0"
                step="0.01" required placeholder="0.00" />
            <x-input wire:model.defer="capacity" label="Capacity (students)" type="number" min="0"
                placeholder="Max students" />

            {{-- Active toggle --}}
            <div class="sm:col-span-2">
                <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-md">
                    <input type="checkbox" wire:model.defer="transport_is_active" id="transport_active"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="transport_active" class="text-sm font-medium text-gray-700">Route is active</label>
                </div>
            </div>
        </div>
    </x-modal-form>

    <!-- ══════════════════ ASSIGN STUDENTS MODAL ══════════════════ -->
    @if ($assignStudentModal)
        <div class="fixed inset-0 z-[999] flex items-center justify-center p-4">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50" wire:click="closeAssignStudentModal"></div>

            {{-- Modal Box --}}
            <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[90vh]">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Assign Students</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Students with transportation required</p>
                    </div>
                    <button wire:click="closeAssignStudentModal"
                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Search --}}
                <div class="px-6 pt-4 pb-2">
                    <div class="relative">
                        <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="studentSearch"
                            placeholder="Search by name or admission no..."
                            class="w-full border border-gray-300 rounded-lg pl-9 pr-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500" />
                    </div>

                    {{-- Selected badge --}}
                    @if (count($selectedStudents))
                        <div
                            class="mt-2 px-3 py-1.5 bg-purple-50 border border-purple-200 rounded-lg text-xs text-purple-700 font-medium">
                            ✓ {{ count($selectedStudents) }} student(s) selected
                        </div>
                    @endif
                </div>

                {{-- Student List --}}
                <div class="flex-1 overflow-y-auto px-6 py-2">
                    @forelse ($availableStudents as $student)
                        <label wire:key="std-{{ $student['id'] }}"
                            class="flex items-center gap-3 px-3 py-3 rounded-lg cursor-pointer transition mb-1
                                {{ in_array((string) $student['id'], array_map('strval', $selectedStudents))
                                    ? 'bg-purple-50 border border-purple-200'
                                    : 'bg-white border border-gray-100 hover:bg-gray-50' }}">

                            <input type="checkbox" wire:model.live="selectedStudents" value="{{ $student['id'] }}"
                                class="h-4 w-4 rounded text-purple-600 border-gray-300 focus:ring-purple-500 flex-shrink-0" />

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ $student['full_name'] }}</p>
                                @if ($student['admission_no'])
                                    <p class="text-xs text-gray-400">Adm: {{ $student['admission_no'] }}</p>
                                @endif
                            </div>

                            @if (in_array((string) $student['id'], array_map('strval', $selectedStudents)))
                                <svg class="w-4 h-4 text-purple-500 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            @endif
                        </label>
                    @empty
                        <div class="py-12 text-center text-gray-400">
                            <svg class="mx-auto h-10 w-10 text-gray-200 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197" />
                            </svg>
                            <p class="text-sm">{{ $studentSearch ? 'No results found' : 'No students available' }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer --}}
                <div
                    class="px-6 py-4 border-t border-gray-100 flex items-center justify-end gap-3 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="closeAssignStudentModal"
                        class="px-5 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="saveAssignedStudents"
                        class="px-5 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-lg transition shadow-sm">
                        Save {{ count($selectedStudents) ? '(' . count($selectedStudents) . ' selected)' : '' }}
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
