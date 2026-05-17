<div class="min-h-screen bg-gray-50">

    {{-- ══════════ TABS ══════════ --}}
    <div class="bg-white border-b border-gray-200 px-6 sticky top-0 z-40">
        <nav class="flex gap-1">
            <button wire:click="setTab('view')"
                class="py-3.5 px-5 text-sm font-semibold border-b-2 transition-colors
                       {{ $activeTab === 'view'
                           ? 'border-blue-500 text-blue-700'
                           : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                View About App
            </button>
            <button wire:click="setTab('edit')"
                class="py-3.5 px-5 text-sm font-semibold border-b-2 transition-colors
                       {{ $activeTab === 'edit'
                           ? 'border-purple-500 text-purple-700'
                           : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                {{ $aboutApp ? 'Edit' : 'Create' }} About App
            </button>
        </nav>
    </div>

    {{-- ══════════ VIEW TAB ══════════ --}}
    @if ($activeTab === 'view')
        @if (!$aboutApp)
            <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Information Available</h3>
                <p class="text-gray-400 text-sm max-w-sm mb-5">App information has not been configured yet.</p>
                <button wire:click="setTab('edit')"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Now
                </button>
            </div>
        @else
            {{-- HERO HEADER --}}
            <div class="bg-white border-b border-gray-200">
                <div class="max-w-5xl mx-auto px-6 py-10">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                        <div class="flex-shrink-0">
                            <img src="{{ $aboutApp->logo ?: asset('website-image/Group 11525.png') }}"
                                alt="App Logo"
                                class="w-24 h-24 rounded-2xl object-contain border border-gray-200 shadow-sm bg-white p-2"
                                onerror="this.src='{{ asset('website-image/Group 11525.png') }}'">
                        </div>
                        <div class="text-center sm:text-left flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $aboutApp->heading ?? 'About App' }}</h1>
                            <p class="text-gray-500 mt-1.5 text-sm max-w-xl">{{ $aboutApp->sub_heading ?? '' }}</p>
                        </div>
                        <button wire:click="setTab('edit')"
                            class="flex-shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium
                                   text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit
                        </button>
                    </div>
                </div>
            </div>

            <div class="max-w-5xl mx-auto px-6 py-8 space-y-6">

                {{-- Content Sections --}}
                @if (!empty($aboutApp->content))
                    @foreach ($aboutApp->content as $i => $section)
                        @if (!empty($section['title']) || !empty($section['description']))
                            <div
                                class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden
                                        hover:border-indigo-200 hover:shadow-md transition-all duration-200">
                                @if (!empty($section['title']))
                                    <div
                                        class="px-6 py-4 border-b border-gray-100 flex items-center gap-3
                                                bg-gradient-to-r from-indigo-50 to-blue-50">
                                        <span
                                            class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-blue-600
                                                     text-white text-sm font-bold rounded-xl flex items-center
                                                     justify-center flex-shrink-0 shadow-sm">
                                            {{ $i + 1 }}
                                        </span>
                                        <h2 class="text-base font-semibold text-gray-900">{{ $section['title'] }}</h2>
                                    </div>
                                @endif
                                @if (!empty($section['description']))
                                    <div class="px-6 py-5">
                                        <p class="text-sm text-gray-600 leading-relaxed">
                                            {!! nl2br(e($section['description'])) !!}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                @endif

                {{-- Contact Details --}}
                @if (!empty($aboutApp->contact_details))
                    <div
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden
                                hover:border-blue-200 hover:shadow-md transition-all duration-200">
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50
                                    flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h2 class="text-base font-semibold text-gray-900">Contact Details</h2>
                        </div>
                        <div class="px-6 py-5 space-y-3">
                            @foreach ($aboutApp->contact_details as $contact)
                                @php $type = strtolower($contact['type'] ?? ''); @endphp
                                <div
                                    class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100
                                            hover:border-blue-100 hover:bg-blue-50 transition-all duration-200">
                                    <div
                                        class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
                                        {{ $type === 'email' ? 'bg-blue-100' : ($type === 'phone' ? 'bg-green-100' : 'bg-gray-100') }}">
                                        @if ($type === 'email')
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        @elseif ($type === 'phone')
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider
                                            {{ $type === 'email' ? 'text-blue-500' : ($type === 'phone' ? 'text-green-500' : 'text-gray-400') }}">
                                            {{ $contact['type'] }}
                                        </p>
                                        <p class="text-sm font-medium text-gray-800 truncate">{{ $contact['value'] }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Address --}}
                @if ($aboutApp->address)
                    <div
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden
                                hover:border-green-200 hover:shadow-md transition-all duration-200">
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50
                                    flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-500 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <h2 class="text-base font-semibold text-gray-900">Address</h2>
                        </div>
                        <div class="px-6 py-5">
                            <p class="text-sm text-gray-600 leading-relaxed">{{ $aboutApp->address }}</p>
                        </div>
                    </div>
                @endif

                {{-- Core Team --}}
                @if (!empty($aboutApp->core_team))
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-amber-50
                                    flex items-center gap-3">
                            <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-base font-semibold text-gray-900">Our Core Team</h2>
                                <p class="text-xs text-gray-400">{{ count($aboutApp->core_team) }} members</p>
                            </div>
                        </div>
                        <div class="px-6 py-6">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                @foreach ($aboutApp->core_team as $member)
                                    @php $memberUrl = $member['link'] ?? null; @endphp
                                    <div
                                        class="group flex flex-col items-center text-center p-4 bg-gray-50
                                                rounded-2xl border border-gray-200 hover:border-amber-300
                                                hover:shadow-md hover:bg-amber-50 transition-all duration-200">
                                        @if (!empty($member['image']))
                                            <img src="{{ $member['image'] }}" alt="{{ $member['name'] ?? '' }}"
                                                class="w-16 h-16 rounded-full object-cover border-2 border-white
                                                       shadow-md mb-3 group-hover:scale-105 transition-transform duration-200">
                                        @else
                                            <div
                                                class="w-16 h-16 rounded-full bg-amber-100 border-2 border-white
                                                        shadow-md mb-3 flex items-center justify-center
                                                        group-hover:scale-105 transition-transform duration-200">
                                                <svg class="w-8 h-8 text-amber-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <p class="font-semibold text-sm text-gray-800 leading-tight">
                                            {{ $member['name'] ?? '' }}</p>
                                        @if (!empty($member['position']))
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $member['position'] }}</p>
                                        @endif
                                        @if ($memberUrl)
                                            <a href="{{ $memberUrl }}" target="_blank"
                                                class="mt-2 text-xs text-amber-600 font-medium hover:underline">
                                                View Profile →
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Social Media --}}
                @if (!empty($aboutApp->social_media))
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50
                                    flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <h2 class="text-base font-semibold text-gray-900">Follow Us On Social Media</h2>
                        </div>
                        <div class="px-6 py-6">
                            <div class="flex flex-wrap gap-3">
                                @foreach ($aboutApp->social_media as $social)
                                    @php
                                        $colors = [
                                            'facebook' => 'bg-blue-600',
                                            'twitter' => 'bg-sky-400',
                                            'instagram' => 'bg-gradient-to-r from-purple-500 to-pink-500',
                                            'linkedin' => 'bg-blue-700',
                                            'youtube' => 'bg-red-600',
                                            'github' => 'bg-gray-800',
                                            'whatsapp' => 'bg-green-500',
                                        ];
                                        $abbrs = [
                                            'facebook' => 'FB',
                                            'twitter' => 'TW',
                                            'instagram' => 'IG',
                                            'linkedin' => 'IN',
                                            'youtube' => 'YT',
                                            'github' => 'GH',
                                            'whatsapp' => 'WA',
                                        ];
                                        $platform = strtolower($social['platform'] ?? '');
                                        $bg = $colors[$platform] ?? 'bg-gray-400';
                                        $abbr = $abbrs[$platform] ?? strtoupper(substr($platform, 0, 2));
                                    @endphp
                                    <a href="{{ $social['url'] ?? '#' }}" target="_blank"
                                        class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200
                                               rounded-xl hover:shadow-md hover:border-blue-200 transition-all group">
                                        @if (!empty($social['icon']))
                                            <img src="{{ $social['icon'] }}" class="w-6 h-6 object-contain rounded">
                                        @else
                                            <div
                                                class="w-7 h-7 {{ $bg }} rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="text-white text-xs font-bold">{{ $abbr }}</span>
                                            </div>
                                        @endif
                                        <span
                                            class="text-sm font-medium text-gray-700 capitalize group-hover:text-blue-700 transition-colors">
                                            {{ $social['platform'] }}
                                        </span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        @endif
    @endif

    {{-- ══════════ EDIT TAB ══════════ --}}
    @if ($activeTab === 'edit')
        <div class="max-w-5xl mx-auto px-6 py-8 space-y-6">

            {{-- Basic Information --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-blue-50
                            flex items-center gap-3">
                    <div class="w-8 h-8 bg-indigo-500 rounded-xl flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">Basic Information</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Heading *</label>
                        <input type="text" wire:model.defer="heading" placeholder="App name or heading"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                        @error('heading')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Sub Heading</label>
                        <input type="text" wire:model.defer="sub_heading" placeholder="Tagline or subtitle"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Logo</label>
                        @if ($logoPreview)
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ $logoPreview }}"
                                    class="w-14 h-14 rounded-xl object-contain border border-gray-200">
                                <span class="text-xs text-gray-400">Current logo</span>
                            </div>
                        @endif
                        <input type="file" wire:model="logo" accept="image/*"
                            class="block w-full text-sm text-gray-500
                                   file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                   hover:file:bg-indigo-100 transition-colors">
                        @error('logo')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                        <textarea wire:model.defer="address" rows="2" placeholder="Office or contact address"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400"></textarea>
                    </div>
                </div>
            </div>

            {{-- Content Sections --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50
                            flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-purple-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Content Sections</h2>
                            <p class="text-xs text-gray-400">{{ count($content) }} section(s)</p>
                        </div>
                    </div>
                    <button wire:click="addContentSection"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-purple-600 hover:bg-purple-700
                               text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Section
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    @foreach ($content as $index => $section)
                        <div class="border border-gray-200 rounded-xl p-4 hover:border-purple-200 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <span class="flex items-center gap-2">
                                    <span
                                        class="w-6 h-6 bg-purple-100 text-purple-700 text-xs font-bold
                                                 rounded-full flex items-center justify-center">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">Section {{ $index + 1 }}</span>
                                </span>
                                @if (count($content) > 1)
                                    <button wire:click="removeContentSection({{ $index }})"
                                        class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Title</label>
                                    <input type="text" wire:model="content.{{ $index }}.title"
                                        placeholder="Section title"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                               focus:ring-2 focus:ring-purple-400 focus:border-purple-400">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                    <textarea wire:model="content.{{ $index }}.description" rows="3" placeholder="Section description..."
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                               focus:ring-2 focus:ring-purple-400 focus:border-purple-400"></textarea>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Contact Details --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50
                            flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900">Contact Details</h2>
                    </div>
                    <button wire:click="openContactModal()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700
                               text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Contact
                    </button>
                </div>
                <div class="p-6 space-y-2">
                    @forelse ($contact_details as $index => $contact)
                        <div
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200
                                    hover:border-blue-200 transition-colors">
                            <div class="min-w-0">
                                <span
                                    class="text-xs font-semibold text-gray-500 uppercase">{{ $contact['type'] }}</span>
                                <p class="text-sm font-medium text-gray-800 truncate">{{ $contact['value'] }}</p>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0 ml-3">
                                <button wire:click="openContactModal({{ $index }})"
                                    class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="removeContact({{ $index }})"
                                    class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No contacts added yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Core Team --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-amber-50
                            flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Core Team</h2>
                            <p class="text-xs text-gray-400">{{ count($core_team) }} members</p>
                        </div>
                    </div>
                    <button wire:click="openTeamModal()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-500 hover:bg-amber-600
                               text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Member
                    </button>
                </div>
                <div class="p-6">
                    @if (count($core_team))
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach ($core_team as $index => $member)
                                <div
                                    class="border border-gray-200 rounded-xl p-4 hover:border-amber-200 transition-colors">
                                    <div class="flex items-center gap-3 mb-3">
                                        @if (!empty($member['image']))
                                            <img src="{{ $member['image'] }}" alt="{{ $member['name'] }}"
                                                class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0">
                                        @else
                                            <div
                                                class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 text-amber-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="1.5"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate">
                                                {{ $member['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $member['position'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    @if (!empty($member['description']))
                                        <p class="text-xs text-gray-500 mb-3 line-clamp-2">
                                            {{ $member['description'] }}</p>
                                    @endif
                                    <div class="flex items-center gap-1">
                                        <button wire:click="openTeamModal({{ $index }})"
                                            class="flex-1 flex items-center justify-center gap-1 py-1.5 text-xs font-medium
                                                   text-amber-600 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button wire:click="removeTeamMember({{ $index }})"
                                            class="flex-1 flex items-center justify-center gap-1 py-1.5 text-xs font-medium
                                                   text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-4">No team members added yet.</p>
                    @endif
                </div>
            </div>

            {{-- Social Media --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50
                            flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900">Social Media</h2>
                    </div>
                    <button wire:click="openSocialModal()"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700
                               text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add Social
                    </button>
                </div>
                <div class="p-6 space-y-2">
                    @forelse ($social_media as $index => $social)
                        @php
                            $colors = [
                                'facebook' => 'bg-blue-600',
                                'twitter' => 'bg-sky-400',
                                'instagram' => 'bg-pink-500',
                                'linkedin' => 'bg-blue-700',
                                'youtube' => 'bg-red-600',
                                'github' => 'bg-gray-800',
                                'whatsapp' => 'bg-green-500',
                            ];
                            $abbrs = [
                                'facebook' => 'FB',
                                'twitter' => 'TW',
                                'instagram' => 'IG',
                                'linkedin' => 'IN',
                                'youtube' => 'YT',
                                'github' => 'GH',
                                'whatsapp' => 'WA',
                            ];
                            $p = strtolower($social['platform'] ?? '');
                            $bg = $colors[$p] ?? 'bg-gray-400';
                            $abbr = $abbrs[$p] ?? strtoupper(substr($p, 0, 2));
                        @endphp
                        <div
                            class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-200
                                    hover:border-blue-200 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                @if (!empty($social['icon']))
                                    <img src="{{ $social['icon'] }}"
                                        class="w-7 h-7 object-contain rounded-lg flex-shrink-0">
                                @else
                                    <div
                                        class="w-7 h-7 {{ $bg }} rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-white text-xs font-bold">{{ $abbr }}</span>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 capitalize">
                                        {{ $social['platform'] }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $social['url'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-1 flex-shrink-0 ml-3">
                                <button wire:click="openSocialModal({{ $index }})"
                                    class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button wire:click="removeSocialMedia({{ $index }})"
                                    class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                    title="Remove">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-400 text-center py-4">No social media links added yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Save Button --}}
            <div class="flex justify-end pb-6">
                <button wire:click="save"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600
                           hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-xl
                           shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save All Changes
                </button>
            </div>
        </div>
    @endif

    {{-- ══════════ DELETE CONTACT CONFIRM ══════════ --}}
    @if ($pendingDeleteContactIndex !== null)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-red-50 flex items-center gap-3">
                    <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Delete Contact</h3>
                </div>
                <div class="p-5">
                    <p class="text-sm text-gray-600">Are you sure you want to remove this contact detail? This action cannot be undone.</p>
                </div>
                <div class="px-5 pb-5 flex items-center gap-2">
                    <button wire:click="executeRemoveContact" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">Yes, Delete</button>
                    <button wire:click="cancelRemoveContact" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ DELETE TEAM MEMBER CONFIRM ══════════ --}}
    @if ($pendingDeleteTeamIndex !== null)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-red-50 flex items-center gap-3">
                    <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Delete Team Member</h3>
                </div>
                <div class="p-5">
                    @if ($pendingDeleteTeamIndex !== null && isset($core_team[$pendingDeleteTeamIndex]))
                        <p class="text-sm text-gray-600">Are you sure you want to remove <strong>{{ $core_team[$pendingDeleteTeamIndex]['name'] ?? 'this member' }}</strong>? This action cannot be undone.</p>
                    @else
                        <p class="text-sm text-gray-600">Are you sure you want to remove this team member? This action cannot be undone.</p>
                    @endif
                </div>
                <div class="px-5 pb-5 flex items-center gap-2">
                    <button wire:click="executeRemoveTeamMember" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">Yes, Delete</button>
                    <button wire:click="cancelRemoveTeamMember" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ DELETE SOCIAL MEDIA CONFIRM ══════════ --}}
    @if ($pendingDeleteSocialIndex !== null)
        <div class="fixed inset-0 flex items-center justify-center bg-black/40 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-red-50 flex items-center gap-3">
                    <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-900">Delete Social Media</h3>
                </div>
                <div class="p-5">
                    @if ($pendingDeleteSocialIndex !== null && isset($social_media[$pendingDeleteSocialIndex]))
                        <p class="text-sm text-gray-600">Are you sure you want to remove <strong class="capitalize">{{ $social_media[$pendingDeleteSocialIndex]['platform'] ?? 'this link' }}</strong>? This action cannot be undone.</p>
                    @else
                        <p class="text-sm text-gray-600">Are you sure you want to remove this social media link? This action cannot be undone.</p>
                    @endif
                </div>
                <div class="px-5 pb-5 flex items-center gap-2">
                    <button wire:click="executeRemoveSocialMedia" class="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg transition-colors">Yes, Delete</button>
                    <button wire:click="cancelRemoveSocialMedia" class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">Cancel</button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ CONTACT MODAL ══════════ --}}
    @if ($showContactModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/30 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        {{ $editContactIndex !== null ? 'Edit Contact' : 'Add Contact' }}
                    </h3>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type *</label>
                        <input type="text" wire:model.defer="newContact.type" placeholder="e.g. Email, Phone"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('newContact.type')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Value *</label>
                        <input type="text" wire:model.defer="newContact.value" placeholder="e.g. info@app.com"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('newContact.value')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button wire:click="saveContact"
                        class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        {{ $editContactIndex !== null ? 'Update' : 'Add Contact' }}
                    </button>
                    <button wire:click="closeContactModal"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ TEAM SLIDE-IN PANEL ══════════ --}}
    @if ($showTeamModal)
        <div class="fixed inset-0 z-[9999] flex items-start justify-end bg-black/30 backdrop-blur-sm">
            <div class="relative w-full max-w-lg h-screen bg-white shadow-2xl flex flex-col">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white flex-shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-500 flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">
                                {{ $editTeamIndex !== null ? 'Edit Team Member' : 'Add Team Member' }}
                            </h2>
                            <p class="text-xs text-gray-500">Fill in the member details below</p>
                        </div>
                    </div>
                    <button wire:click="closeTeamModal" type="button"
                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Form Body (scrollable) --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-5">

                    {{-- Profile Photo --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Profile Photo</p>
                        <div class="flex items-center gap-4">
                            @if ($editTeamIndex !== null && !empty($core_team[$editTeamIndex]['image']))
                                <img src="{{ $core_team[$editTeamIndex]['image'] }}"
                                    class="w-16 h-16 rounded-full object-cover border-2 border-white shadow flex-shrink-0">
                            @else
                                <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center border-2 border-white shadow flex-shrink-0">
                                    <svg class="w-7 h-7 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-1">
                                <input type="file" wire:model="newTeamMemberImage" accept="image/*"
                                    class="block w-full text-sm text-gray-500 file:mr-3 file:py-1.5 file:px-3
                                           file:rounded-lg file:border-0 file:text-xs file:font-semibold
                                           file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $editTeamIndex !== null ? 'Leave empty to keep current photo' : 'JPG, PNG up to 2MB' }}
                                </p>
                                <div wire:loading wire:target="newTeamMemberImage"
                                    class="flex items-center gap-1.5 text-xs text-amber-600 mt-1">
                                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                    </svg>
                                    Uploading…
                                </div>
                                @error('newTeamMemberImage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Basic Info --}}
                    <div class="bg-gray-50 rounded-xl p-4 space-y-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Basic Information</p>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="newTeamMember.name" placeholder="e.g. Annant Dagur"
                                class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400
                                       @error('newTeamMember.name') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                            @error('newTeamMember.name')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Position / Role <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="newTeamMember.position" placeholder="e.g. CEO, Lead Developer"
                                class="w-full border rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400
                                       @error('newTeamMember.position') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                            @error('newTeamMember.position')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                            <textarea wire:model="newTeamMember.description" rows="3"
                                placeholder="Brief bio or description..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400 resize-none"></textarea>
                        </div>
                    </div>

                    {{-- Social / Link --}}
                    <div class="bg-gray-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Profile Link</p>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Profile / Social URL</label>
                            <input type="url" wire:model="newTeamMember.link" placeholder="https://instagram.com/..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-amber-400 focus:border-amber-400">
                            @error('newTeamMember.link')
                                <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-white flex-shrink-0 flex items-center gap-3">
                    <button wire:click="saveTeamMember" wire:loading.attr="disabled"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-amber-500 hover:bg-amber-600
                               text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                        <span wire:loading.remove wire:target="saveTeamMember">
                            {{ $editTeamIndex !== null ? 'Update Member' : 'Add Member' }}
                        </span>
                        <span wire:loading wire:target="saveTeamMember" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Saving…
                        </span>
                    </button>
                    <button wire:click="closeTeamModal" type="button"
                        class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-xl transition-colors">
                        Cancel
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- ══════════ SOCIAL MODAL ══════════ --}}
    @if ($showSocialModal)
        <div class="fixed inset-0 flex items-center justify-center bg-black/30 backdrop-blur-sm z-[9999] px-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900">
                        {{ $editSocialIndex !== null ? 'Edit Social Media' : 'Add Social Media' }}
                    </h3>
                </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Platform *</label>
                        <select wire:model.defer="newSocialMedia.platform"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">Select Platform</option>
                            @foreach (['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github', 'whatsapp'] as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                        @error('newSocialMedia.platform')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">URL *</label>
                        <input type="url" wire:model.defer="newSocialMedia.url" placeholder="https://..."
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                   focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('newSocialMedia.url')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Custom Icon
                            {{ $editSocialIndex !== null ? '(leave empty to keep current)' : '(Optional)' }}
                        </label>
                        @if ($editSocialIndex !== null && !empty($social_media[$editSocialIndex]['icon']))
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ $social_media[$editSocialIndex]['icon'] }}"
                                    class="w-8 h-8 object-contain rounded border border-gray-200">
                                <span class="text-xs text-gray-400">Current icon</span>
                            </div>
                        @endif
                        <input type="file" wire:model="newSocialMediaIcon" accept="image/*"
                            class="block w-full text-sm text-gray-500
                                   file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100 transition-colors">
                        @error('newSocialMediaIcon')
                            <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-5">
                    <button wire:click="saveSocialMedia"
                        class="flex-1 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        {{ $editSocialIndex !== null ? 'Update' : 'Add Social Media' }}
                    </button>
                    <button wire:click="closeSocialModal"
                        class="flex-1 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
