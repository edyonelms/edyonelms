<div class="p-6">

    {{-- ── Filter tab (small, just below the header) ── --}}
    <div class="flex flex-wrap items-center justify-end gap-3 mb-4">
        <div class="flex items-center gap-1.5">
            <span class="text-xs text-gray-500">Sort</span>
            <select wire:model.live="sort"
                class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 bg-white text-gray-700
                       focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                <option value="sidebar">Sidebar order</option>
                <option value="asc">A–Z (ascending)</option>
            </select>
        </div>
        <div class="flex items-center gap-1.5">
            <span class="text-xs text-gray-500">Columns</span>
            <select wire:model.live="columns"
                class="text-xs border border-gray-200 rounded-lg px-2 py-1.5 bg-white text-gray-700
                       focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400">
                @foreach ($columnOptions as $opt)
                    <option value="{{ $opt }}">{{ $opt }} per row</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="rounded-lg">
        <div class="grid gap-4" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr));">
            @foreach ($orderedLinks as $link)
                <a href="{{ route($link['route'], ['organization' => request()->route('organization')]) }}"
                   wire:key="ql-{{ $link['route'] }}"
                   class="flex flex-col items-center p-3 rounded-lg border border-gray-200 hover:bg-gray-50 bg-white transition">
                    <div class="w-10 h-10 bg-{{ $link['color'] }}-100 rounded-full flex items-center justify-center mb-2">
                        <x-icon name="{{ $link['icon'] }}" class="w-5 h-5 text-{{ $link['color'] }}-600" />
                    </div>
                    <span class="text-sm font-medium text-center">{{ $link['title'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
