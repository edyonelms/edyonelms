<div class="min-h-screen bg-gray-50/50">

    {{-- ═══════════════════════════════════════════════════════
         STICKY HEADER
    ═══════════════════════════════════════════════════════════ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">

        {{-- Title row --}}
        <div class="px-6 pt-4 pb-3 flex items-center justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold text-emerald-700 leading-tight">Transport</h1>
                <p class="text-xs text-gray-400 mt-0.5">View drivers, routes and pickup details</p>
            </div>
        </div>

        {{-- Analytics Strip --}}
        <div class="px-6 pb-3 grid grid-cols-3 gap-3">
            <div class="flex items-center gap-3 bg-emerald-50 rounded-xl px-4 py-3 border border-emerald-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="user" class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium truncate">Total Drivers</p>
                    <p class="text-lg font-bold text-emerald-700 leading-tight">{{ $this->statistics['drivers'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-blue-50 rounded-xl px-4 py-3 border border-blue-100">
                <div class="w-8 h-8 rounded-lg bg-blue-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="map" class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium truncate">Total Routes</p>
                    <p class="text-lg font-bold text-blue-700 leading-tight">{{ $this->statistics['routes'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-teal-50 rounded-xl px-4 py-3 border border-teal-100">
                <div class="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center flex-shrink-0">
                    <x-icon name="check-circle" class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-medium truncate">Active Routes</p>
                    <p class="text-lg font-bold text-teal-700 leading-tight">{{ $this->statistics['active_routes'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-5">

        {{-- ─── Filter Bar ─── --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Search</label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Name, vehicle, license..."
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 pl-9" />
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-icon name="magnifying-glass" class="w-4 h-4 text-gray-400" />
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Route</label>
                    <select wire:model.live="filterRoute"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Routes</option>
                        @foreach($this->routeOptions as $routeName => $label)
                            <option value="{{ $routeName }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                    <select wire:model.live="filterStatus"
                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- ─── Driver Cards Grid ─── --}}
        @if($drivers->isEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
                <x-icon name="truck" class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500 font-medium">No drivers found</p>
                <p class="text-sm text-gray-400 mt-1">Try adjusting your search or filter</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($drivers as $driver)
                    @php
                        $name     = $driver->user?->name ?? 'Unknown';
                        $initials = collect(explode(' ', $name))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
                        $photo    = $driver->user?->profile_photo_url ?? null;
                        $routes   = $driver->transportations;
                    @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        {{-- Card Header --}}
                        <div class="p-5 flex items-center gap-4 border-b border-gray-50">
                            {{-- Avatar --}}
                            <div class="flex-shrink-0">
                                @if($photo)
                                    <img src="{{ $photo }}" alt="{{ $name }}"
                                        class="w-14 h-14 rounded-full object-cover ring-2 ring-emerald-100" />
                                @else
                                    <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center ring-2 ring-emerald-200">
                                        <span class="text-emerald-700 font-bold text-lg">{{ $initials }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ $name }}</p>
                                @if($driver->phone)
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $driver->phone }}</p>
                                @endif
                                <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $driver->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $driver->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            <button wire:click="openViewModal({{ $driver->id }})"
                                class="flex-shrink-0 p-2 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-600 transition-colors"
                                title="View Details">
                                <x-icon name="eye" class="w-4 h-4" />
                            </button>
                        </div>

                        {{-- Vehicle Info --}}
                        <div class="px-5 py-3 bg-gray-50/60 flex items-center gap-4 text-xs text-gray-600 border-b border-gray-100">
                            @if($driver->vehicle_no)
                                <span class="flex items-center gap-1">
                                    <x-icon name="truck" class="w-3.5 h-3.5 text-gray-400" />
                                    {{ $driver->vehicle_no }}
                                    @if($driver->vehicle_type)
                                        <span class="text-gray-400">({{ $driver->vehicle_type }})</span>
                                    @endif
                                </span>
                            @endif
                            @if($driver->license_no)
                                <span class="flex items-center gap-1">
                                    <x-icon name="identification" class="w-3.5 h-3.5 text-gray-400" />
                                    {{ $driver->license_no }}
                                </span>
                            @endif
                        </div>

                        {{-- Routes --}}
                        <div class="px-5 py-4">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">
                                Routes ({{ $routes->count() }})
                            </p>
                            @if($routes->isEmpty())
                                <p class="text-xs text-gray-400 italic">No routes assigned</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($routes->take(3) as $route)
                                        <div class="flex items-start gap-2 text-xs">
                                            <div class="flex-shrink-0 w-5 h-5 rounded-full bg-blue-100 flex items-center justify-center mt-0.5">
                                                <x-icon name="map-pin" class="w-3 h-3 text-blue-600" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium text-gray-800 truncate">{{ $route->route_name }}</p>
                                                <p class="text-gray-400 truncate">
                                                    {{ $route->pickup_location ?? 'No pickup set' }}
                                                    @if($route->drop_location)
                                                        → {{ $route->drop_location }}
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="flex-shrink-0 px-1.5 py-0.5 rounded
                                                {{ $route->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                                {{ $route->is_active ? 'Active' : 'Off' }}
                                            </span>
                                        </div>
                                    @endforeach
                                    @if($routes->count() > 3)
                                        <p class="text-xs text-emerald-600 font-medium mt-1">
                                            +{{ $routes->count() - 3 }} more routes
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $drivers->links() }}
            </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════
         VIEW DRIVER MODAL
    ═══════════════════════════════════════════════════════════ --}}
    @if($viewModal && $this->viewDriver)
        @php
            $d        = $this->viewDriver;
            $dName    = $d->user?->name ?? 'Unknown';
            $dPhoto   = $d->user?->profile_photo_url ?? null;
            $dInitials = collect(explode(' ', $dName))->take(2)->map(fn($w) => strtoupper(substr($w,0,1)))->implode('');
        @endphp
        <div class="fixed inset-0 z-[999] flex items-center justify-center px-4"
             style="background: rgba(0,0,0,0.45); backdrop-filter: blur(4px);">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-emerald-700">Driver Details</h2>
                    <button wire:click="closeViewModal"
                        class="p-2 rounded-lg hover:bg-gray-100 text-gray-500 transition-colors">
                        <x-icon name="x-mark" class="w-5 h-5" />
                    </button>
                </div>

                {{-- Driver Profile --}}
                <div class="px-6 py-5 flex items-center gap-5 border-b border-gray-50">
                    @if($dPhoto)
                        <img src="{{ $dPhoto }}" alt="{{ $dName }}"
                            class="w-20 h-20 rounded-full object-cover ring-4 ring-emerald-100" />
                    @else
                        <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center ring-4 ring-emerald-200">
                            <span class="text-emerald-700 font-bold text-2xl">{{ $dInitials }}</span>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $dName }}</h3>
                        <p class="text-sm text-gray-500">{{ $d->user?->email ?? '—' }}</p>
                        <span class="inline-flex items-center mt-2 px-3 py-1 rounded-full text-xs font-medium
                            {{ $d->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            {{ $d->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                {{-- Driver Info Grid --}}
                <div class="px-6 py-4 grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 font-medium uppercase">Phone</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $d->phone ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 font-medium uppercase">License No</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $d->license_no ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 font-medium uppercase">Vehicle No</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $d->vehicle_no ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 font-medium uppercase">Vehicle Type</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $d->vehicle_type ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 font-medium uppercase">Experience</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5">{{ $d->experience_years ?? 0 }} yrs</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-3">
                        <p class="text-xs text-gray-400 font-medium uppercase">Address</p>
                        <p class="text-sm font-semibold text-gray-800 mt-0.5 truncate">{{ $d->address ?? '—' }}</p>
                    </div>
                </div>

                {{-- Routes --}}
                <div class="px-6 pb-6">
                    <h4 class="text-sm font-bold text-gray-700 mb-3">
                        Assigned Routes ({{ $d->transportations->count() }})
                    </h4>
                    @if($d->transportations->isEmpty())
                        <p class="text-sm text-gray-400 italic">No routes assigned</p>
                    @else
                        <div class="space-y-3">
                            @foreach($d->transportations as $route)
                                <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                                    <div class="flex items-center justify-between mb-2">
                                        <p class="font-semibold text-blue-800">{{ $route->route_name }}</p>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            {{ $route->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $route->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs text-gray-600">
                                        <div>
                                            <span class="text-gray-400">Pickup:</span>
                                            <span class="ml-1 font-medium">{{ $route->pickup_location ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Drop:</span>
                                            <span class="ml-1 font-medium">{{ $route->drop_location ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Capacity:</span>
                                            <span class="ml-1 font-medium">{{ $route->capacity ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Students:</span>
                                            <span class="ml-1 font-medium">{{ $route->students->count() }}</span>
                                        </div>
                                        @if($route->monthly_fee)
                                            <div class="col-span-2">
                                                <span class="text-gray-400">Monthly Fee:</span>
                                                <span class="ml-1 font-semibold text-emerald-700">₹{{ number_format($route->monthly_fee) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if(!empty($route->stops) && is_array($route->stops))
                                        <div class="mt-2">
                                            <p class="text-xs text-gray-400 mb-1">Stops:</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($route->stops as $stop)
                                                    <span class="px-2 py-0.5 bg-white border border-blue-200 rounded-full text-xs text-blue-700">
                                                        {{ $stop }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

</div>
