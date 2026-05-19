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
            {{-- COMPACT HEADER (fees style) --}}
            <div class="bg-white border-b border-gray-200 px-4 sm:px-6 py-4 sm:py-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <img src="{{ $aboutApp->logo ?: asset('website-image/Group 11525.png') }}"
                            alt="App Logo"
                            class="w-12 h-12 rounded-xl object-contain border border-gray-200 shadow-sm bg-white p-1 flex-shrink-0"
                            onerror="this.src='{{ asset('website-image/Group 11525.png') }}'">
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 truncate">{{ $aboutApp->heading ?? 'About App' }}</h1>
                            <p class="text-sm text-gray-500 mt-0.5 truncate">{{ $aboutApp->sub_heading ?? '' }}</p>
                        </div>
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
                                        @if (!empty($member['description']))
                                            <p class="text-xs text-gray-500 mt-2 leading-relaxed line-clamp-3">
                                                {{ $member['description'] }}
                                            </p>
                                        @endif
                                        @if ($memberUrl)
                                            <a href="{{ $memberUrl }}" target="_blank" rel="noopener noreferrer"
                                                class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-amber-500 hover:bg-amber-600 rounded-lg transition-colors shadow-sm">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
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
                            @php
                                $platformIconsView = [
                                    'facebook'  => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073C0 18.062 4.388 23.027 10.125 23.927v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                                    'twitter'   => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#1DA1F2"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>',
                                    'instagram' => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#E4405F"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
                                    'linkedin'  => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#0A66C2"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                                    'youtube'   => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#FF0000"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                                    'github'    => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#181717"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>',
                                    'whatsapp'  => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
                                ];
                            @endphp
                            <div class="flex flex-wrap gap-3">
                                @foreach ($aboutApp->social_media as $social)
                                    @php
                                        $platform = strtolower($social['platform'] ?? '');
                                        $defaultIcon = $platformIconsView[$platform] ?? null;
                                    @endphp
                                    <a href="{{ $social['url'] ?? '#' }}" target="_blank" rel="noopener noreferrer"
                                        class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200
                                               rounded-xl hover:shadow-md hover:border-blue-200 transition-all group">
                                        <div class="w-6 h-6 flex items-center justify-center flex-shrink-0">
                                            @if (!empty($social['icon']))
                                                <img src="{{ $social['icon'] }}" class="w-6 h-6 object-contain">
                                            @elseif ($defaultIcon)
                                                {!! $defaultIcon !!}
                                            @else
                                                <div class="w-6 h-6 bg-gray-400 rounded-lg flex items-center justify-center">
                                                    <span class="text-white text-[10px] font-bold">{{ strtoupper(substr($platform, 0, 2)) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <span class="text-sm font-medium text-gray-700 capitalize group-hover:text-blue-700 transition-colors">
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

    {{-- ══════════ CONTACT SLIDE-IN PANEL ══════════ --}}
    @if ($showContactModal)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeContactModal"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-xl bg-white shadow-2xl flex flex-col">

                {{-- Floating close --}}
                <button wire:click="closeContactModal"
                    class="absolute top-4 right-4 z-20 w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 hover:bg-red-50 hover:border-red-300 text-gray-500 hover:text-red-500 transition-colors shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex-1 overflow-y-auto px-6 pt-6 pb-6 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $editContactIndex !== null ? 'Edit Contact' : 'Add Contact' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Add or update a contact detail</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="newContact.type" placeholder="e.g. Email, Phone"
                            class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 text-sm
                                   focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @error('newContact.type')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Value <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.defer="newContact.value" placeholder="e.g. info@app.com"
                            class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 text-sm
                                   focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @error('newContact.value')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="px-6 py-3.5 border-t border-gray-200 flex items-center justify-end gap-2 flex-shrink-0">
                    <button wire:click="closeContactModal" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="saveContact"
                        class="px-5 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-md">
                        {{ $editContactIndex !== null ? 'Update' : 'Add Contact' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════ TEAM SLIDE-IN PANEL ══════════ --}}
    @if ($showTeamModal)
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeTeamModal"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-xl bg-white shadow-2xl flex flex-col">

                {{-- Floating close --}}
                <button wire:click="closeTeamModal" type="button"
                    class="absolute top-4 right-4 z-20 w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 hover:bg-red-50 hover:border-red-300 text-gray-500 hover:text-red-500 transition-colors shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Form Body (scrollable) --}}
                <div class="flex-1 overflow-y-auto px-6 pt-6 pb-6 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $editTeamIndex !== null ? 'Edit Team Member' : 'Add Team Member' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Fill in the member details below</p>
                    </div>

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

    {{-- ══════════ SOCIAL SLIDE-IN PANEL ══════════ --}}
    @if ($showSocialModal)
        @php
            $platformIcons = [
                'facebook'  => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#1877F2"><path d="M24 12.073c0-6.627-5.373-12-12-12S0 5.446 0 12.073C0 18.062 4.388 23.027 10.125 23.927v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
                'twitter'   => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#1DA1F2"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>',
                'instagram' => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#E4405F"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>',
                'linkedin'  => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#0A66C2"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.063 2.063 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>',
                'youtube'   => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#FF0000"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
                'github'    => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#181717"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>',
                'whatsapp'  => '<svg class="w-full h-full" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>',
            ];
            $currentPlatform = $newSocialMedia['platform'] ?? '';
            $autoIcon = $platformIcons[$currentPlatform] ?? null;
        @endphp
        <div class="fixed inset-0 z-50 overflow-hidden">
            <div class="absolute inset-0 bg-black/[0.04] backdrop-blur-[1.5px]" wire:click="closeSocialModal"></div>
            <div class="absolute top-0 right-0 bottom-0 w-full max-w-xl bg-white shadow-2xl flex flex-col">

                {{-- Floating close --}}
                <button wire:click="closeSocialModal"
                    class="absolute top-4 right-4 z-20 w-9 h-9 flex items-center justify-center rounded-full bg-white border border-gray-200 hover:bg-red-50 hover:border-red-300 text-gray-500 hover:text-red-500 transition-colors shadow-md">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <div class="flex-1 overflow-y-auto px-6 pt-6 pb-6 space-y-5">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $editSocialIndex !== null ? 'Edit Social Media' : 'Add Social Media' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Select platform to auto-fill logo</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Platform <span class="text-red-500">*</span></label>
                        <select wire:model.live="newSocialMedia.platform"
                            class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 text-sm bg-white
                                   focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Platform</option>
                            @foreach (['facebook', 'twitter', 'instagram', 'linkedin', 'youtube', 'github', 'whatsapp'] as $p)
                                <option value="{{ $p }}">{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                        @error('newSocialMedia.platform')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    @if ($autoIcon)
                        <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-lg p-2 flex items-center justify-center shadow-sm">
                                {!! $autoIcon !!}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-blue-700 uppercase tracking-wide">Auto-selected Logo</p>
                                <p class="text-sm text-gray-700 capitalize">{{ $currentPlatform }} logo will be used by default.</p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">URL <span class="text-red-500">*</span></label>
                        <input type="url" wire:model.defer="newSocialMedia.url" placeholder="https://..."
                            class="w-full border border-gray-300 rounded-md px-3.5 py-2.5 text-sm
                                   focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        @error('newSocialMedia.url')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            Custom Icon <span class="text-gray-400 font-normal">(optional — overrides auto logo)</span>
                        </label>
                        @if ($editSocialIndex !== null && !empty($social_media[$editSocialIndex]['icon']))
                            <div class="flex items-center gap-3 mb-2">
                                <img src="{{ $social_media[$editSocialIndex]['icon'] }}"
                                    class="w-10 h-10 object-contain rounded border border-gray-200 bg-white p-1">
                                <span class="text-xs text-gray-500">Current custom icon</span>
                            </div>
                        @endif
                        <input type="file" wire:model="newSocialMediaIcon" accept="image/*"
                            class="block w-full text-sm text-gray-500
                                   file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0
                                   file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100 transition-colors border border-gray-300 rounded-md">
                        @error('newSocialMediaIcon')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="px-6 py-3.5 border-t border-gray-200 flex items-center justify-end gap-2 flex-shrink-0">
                    <button wire:click="closeSocialModal" class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">Cancel</button>
                    <button wire:click="saveSocialMedia"
                        class="px-5 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-md">
                        {{ $editSocialIndex !== null ? 'Update' : 'Add Social Media' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
