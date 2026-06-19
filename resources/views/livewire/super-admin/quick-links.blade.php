<div class="p-4 sm:p-6">
    <div class="rounded-lg">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 bg-white transition">
                    <div class="w-10 h-10 bg-{{ $link['color'] }}-100 rounded-full flex items-center justify-center mb-2">
                        <x-icon name="{{ $link['icon'] }}" class="w-5 h-5 text-{{ $link['color'] }}-600" />
                    </div>
                    <span class="text-sm font-medium text-center">{{ $link['title'] }}</span>
                </a>
            @endforeach

            {{-- Notifications — opens the panel on the navbar --}}
            <button type="button"
                wire:click="$dispatchTo('components.nav-bar', 'open-notifications')"
                class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 bg-white transition focus:outline-none">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center mb-2">
                    <x-icon name="bell-alert" class="w-5 h-5 text-amber-600" />
                </div>
                <span class="text-sm font-medium text-center">Notifications</span>
            </button>
        </div>
    </div>
</div>
