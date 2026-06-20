<div class="p-3 sm:p-4">

    {{-- ── Tiles (compact — sized so the whole set fits without scrolling) ── --}}
    <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2.5 sm:gap-3">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}"
               class="group flex flex-col items-center text-center gap-2 p-3 rounded-xl bg-white border border-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-indigo-200 transition-all duration-200">
                <div class="w-9 h-9 rounded-lg bg-{{ $link['color'] }}-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                    <x-icon name="{{ $link['icon'] }}" class="w-5 h-5 text-{{ $link['color'] }}-600" />
                </div>
                <span class="text-xs font-semibold text-gray-700 group-hover:text-gray-900 leading-tight transition-colors">{{ $link['title'] }}</span>
            </a>
        @endforeach

        {{-- Notifications — opens the panel on the navbar --}}
        <button type="button"
            wire:click="$dispatchTo('components.nav-bar', 'open-notifications')"
            class="group flex flex-col items-center text-center gap-2 p-3 rounded-xl bg-white border border-gray-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-amber-200 transition-all duration-200 focus:outline-none">
            <div class="w-9 h-9 rounded-lg bg-amber-100 flex items-center justify-center group-hover:scale-110 transition-transform duration-200">
                <x-icon name="bell-alert" class="w-5 h-5 text-amber-600" />
            </div>
            <span class="text-xs font-semibold text-gray-700 group-hover:text-gray-900 leading-tight transition-colors">Notifications</span>
        </button>
    </div>
</div>
