<div class="min-h-screen bg-gray-50">

    @if (!$aboutApp)
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-5">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Information Available</h3>
            <p class="text-gray-400 text-sm max-w-sm">App information has not been configured yet.</p>
        </div>
    @else
        {{-- ══════════════════════════════════════════════════
         COMPACT HEADER (super-admin about-app style)
    ══════════════════════════════════════════════════ --}}
        <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    @if ($aboutApp->logo)
                        <img src="{{ $aboutApp->logo }}" alt="App Logo"
                            class="w-12 h-12 rounded-xl object-contain border border-gray-200 shadow-sm bg-white p-1 flex-shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-xl bg-indigo-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="min-w-0">
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $aboutApp->heading ?? 'About App' }}</h1>
                        <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $aboutApp->sub_heading ?? 'Platform application details' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 flex-shrink-0">
                    @if ($lastUpdated)
                        <span class="inline-block px-4 py-1.5 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-full border border-indigo-100">
                            Last updated: {{ $lastUpdated }}
                        </span>
                    @endif
                    @if ($aboutApp->title ?? false)
                        <span class="inline-block px-4 py-1.5 bg-gray-50 text-gray-700 text-sm font-semibold rounded-full border border-gray-200">
                            {{ $aboutApp->title }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-6 py-8 space-y-6">

            {{-- ── About Content Sections ── --}}
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

            {{-- ── Contact Details ── --}}
            @if (!empty($aboutApp->contact_details))
                <div
                    class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden
        hover:border-blue-200 hover:shadow-md transition-all duration-200">
                    <div
                        class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50
            flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900">Contact Details</h2>
                    </div>
                    <div class="px-6 py-5">
                        <div class="space-y-3">
                            @foreach ($aboutApp->contact_details as $contact)
                                @php $type = strtolower($contact['type'] ?? ''); @endphp
                                <div
                                    class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl border border-gray-100
                        hover:border-blue-100 hover:bg-blue-50 transition-all duration-200">

                                    {{-- Icon --}}
                                    @if ($type === 'email')
                                        <div
                                            class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @elseif ($type === 'phone')
                                        <div
                                            class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </div>
                                    @else
                                        <div
                                            class="w-9 h-9 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Label + Value --}}
                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-xs font-semibold uppercase tracking-wider
                                {{ $type === 'email' ? 'text-blue-500' : ($type === 'phone' ? 'text-green-500' : 'text-gray-400') }}">
                                            {{ $contact['type'] }}
                                        </p>
                                        @if ($type === 'email')
                                            <a href="mailto:{{ $contact['value'] }}"
                                                class="text-sm font-medium text-gray-800 hover:text-blue-600 transition-colors truncate block">
                                                {{ $contact['value'] }}
                                            </a>
                                        @elseif ($type === 'phone')
                                            <a href="tel:{{ $contact['value'] }}"
                                                class="text-sm font-medium text-gray-800 hover:text-green-600 transition-colors truncate block">
                                                {{ $contact['value'] }}
                                            </a>
                                        @else
                                            <p class="text-sm font-medium text-gray-800 truncate">
                                                {{ $contact['value'] }}</p>
                                        @endif
                                    </div>

                                    {{-- Action icon --}}
                                    @if ($type === 'email')
                                        <a href="mailto:{{ $contact['value'] }}"
                                            class="flex-shrink-0 w-8 h-8 bg-white border border-gray-200 rounded-lg
                                    flex items-center justify-center hover:border-blue-300 hover:bg-blue-50 transition-colors">
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        </a>
                                    @elseif ($type === 'phone')
                                        <a href="tel:{{ $contact['value'] }}"
                                            class="flex-shrink-0 w-8 h-8 bg-white border border-gray-200 rounded-lg
                                    flex items-center justify-center hover:border-green-300 hover:bg-green-50 transition-colors">
                                            <svg class="w-3.5 h-3.5 text-green-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </a>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Address ── --}}
            @if ($aboutApp->address ?? false)
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
                        <h2 class="text-base font-semibold text-gray-900">📍 Address</h2>
                    </div>
                    <div class="px-6 py-5">
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $aboutApp->address }}</p>
                    </div>
                </div>
            @endif

            {{-- ── Core Team ── --}}
            @if (!empty($aboutApp->core_team))
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-yellow-50 to-amber-50
                        flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                                @php
                                    $memberUrl = $member['url'] ?? $member['link'] ?? null;
                                @endphp
                                <div class="group flex flex-col items-center text-center p-4 bg-gray-50
                                            rounded-2xl border border-gray-200 hover:border-amber-300
                                            hover:shadow-md hover:bg-amber-50 transition-all duration-200">

                                    {{-- Avatar --}}
                                    @if (!empty($member['image']))
                                        <img src="{{ $member['image'] }}" alt="{{ $member['name'] ?? '' }}"
                                            class="w-16 h-16 rounded-full object-cover border-2 border-white
                                                   shadow-md mb-3 group-hover:scale-105 transition-transform duration-200">
                                    @else
                                        <div class="w-16 h-16 rounded-full bg-amber-100 border-2 border-white
                                                    shadow-md mb-3 flex items-center justify-center
                                                    group-hover:scale-105 transition-transform duration-200">
                                            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                        </div>
                                    @endif

                                    <p class="font-semibold text-sm text-gray-800 leading-tight">
                                        {{ $member['name'] ?? '' }}
                                    </p>
                                    @if (!empty($member['position']))
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $member['position'] }}</p>
                                    @endif

                                    {{-- Profile button (only when URL exists) --}}
                                    @if ($memberUrl)
                                        <a href="{{ $memberUrl }}" target="_blank" rel="noopener"
                                            class="mt-3 inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold
                                                   text-white bg-amber-500 hover:bg-amber-600 rounded-full
                                                   shadow-sm transition-colors">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            View Profile
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            {{-- ── Social Media ── --}}
            @if (!empty($aboutApp->social_media))
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-sky-50
                                flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-500 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                            </svg>
                        </div>
                        <h2 class="text-base font-semibold text-gray-900">Follow Us On Social Media</h2>
                    </div>
                    <div class="px-6 py-6">
                        <div class="flex flex-wrap gap-4 justify-center">
                            @foreach ($aboutApp->social_media as $social)
                                @php
                                    $platform = $social['platform'] ?? $social['name'] ?? 'Link';
                                    $url      = $social['url'] ?? '#';
                                    $icon     = $social['icon'] ?? null;
                                @endphp
                                <a href="{{ $url }}" target="_blank" rel="noopener"
                                    title="{{ $platform }}"
                                    class="group flex flex-col items-center gap-2 px-4 py-3 bg-gray-50
                                           rounded-2xl border border-gray-200 hover:border-blue-300
                                           hover:bg-blue-50 hover:shadow-sm transition-all duration-200 w-28">
                                    @if ($icon)
                                        <img src="{{ $icon }}" alt="{{ $platform }}"
                                            class="w-12 h-12 object-contain rounded-lg bg-white p-1 border border-gray-100
                                                   group-hover:scale-110 transition-transform">
                                    @else
                                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="text-xs font-medium text-gray-700 group-hover:text-blue-700
                                                 transition-colors capitalize truncate w-full text-center">
                                        {{ $platform }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

        </div>{{-- end max-w-5xl --}}
    @endif

</div>
