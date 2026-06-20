<div class="p-4 sm:p-6 lg:p-8">

    {{-- ── Header ── --}}
    <div class="mb-6 flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center shadow-sm shrink-0">
            <x-icon name="squares-2x2" class="w-6 h-6 text-white" />
        </div>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 leading-tight">Quick Links</h1>
            <p class="text-sm text-gray-500">Jump straight to any part of the super-admin panel.</p>
        </div>
    </div>

    {{-- ── Tiles ── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
               class="group relative flex flex-col gap-3 p-4 sm:p-5 rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-indigo-200 transition-all duration-300 overflow-hidden">
                {{-- soft accent glow on hover --}}
                <span class="pointer-events-none absolute -top-6 -right-6 w-20 h-20 rounded-full bg-{{ $link['color'] }}-100 opacity-0 group-hover:opacity-80 blur-2xl transition-opacity duration-300"></span>

                <div class="relative w-11 h-11 rounded-xl bg-{{ $link['color'] }}-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <x-icon name="{{ $link['icon'] }}" class="w-5 h-5 text-{{ $link['color'] }}-600" />
                </div>

                <div class="relative flex items-center justify-between gap-2">
                    <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 leading-snug transition-colors">{{ $link['title'] }}</span>
                    <svg class="w-4 h-4 shrink-0 text-gray-300 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 group-hover:text-indigo-500 transition-all duration-300"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </div>
            </a>
        @endforeach

        {{-- Notifications — opens the panel on the navbar --}}
        <button type="button"
            wire:click="$dispatchTo('components.nav-bar', 'open-notifications')"
            class="group relative flex flex-col gap-3 p-4 sm:p-5 rounded-2xl bg-white border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 hover:border-amber-200 transition-all duration-300 overflow-hidden text-left focus:outline-none">
            <span class="pointer-events-none absolute -top-6 -right-6 w-20 h-20 rounded-full bg-amber-100 opacity-0 group-hover:opacity-80 blur-2xl transition-opacity duration-300"></span>

            <div class="relative w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                <x-icon name="bell-alert" class="w-5 h-5 text-amber-600" />
            </div>

            <div class="relative flex items-center justify-between gap-2">
                <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900 leading-snug transition-colors">Notifications</span>
                <svg class="w-4 h-4 shrink-0 text-gray-300 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 group-hover:text-amber-500 transition-all duration-300"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </div>
        </button>
    </div>
</div>
