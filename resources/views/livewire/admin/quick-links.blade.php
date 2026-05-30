<div class="bg-gray-50">

    {{-- ── Filter bar only (Enquiries-style) ── --}}
    <div class="bg-gray-50 border-b border-gray-200 px-4 sm:px-6 py-3">
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-1.5 text-sm font-semibold text-gray-700">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                Filter by:
            </div>

            <div class="flex items-center gap-1.5">
                <span class="text-xs text-gray-500">Sort</span>
                <select wire:model.live="sort"
                    class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="sidebar">Sidebar order</option>
                    <option value="asc">A–Z (ascending)</option>
                </select>
            </div>

            <div class="h-5 w-px bg-gray-300 hidden sm:block"></div>

            <div class="flex items-center gap-1.5">
                <span class="text-xs text-gray-500">Columns</span>
                <select wire:model.live="columns"
                    class="text-xs bg-white border border-gray-200 rounded-md px-2.5 py-1.5 text-gray-700
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($columnOptions as $opt)
                        <option value="{{ $opt }}">{{ $opt }} per row</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- ── Tile grid (compact so 7×5 fits without scroll) ── --}}
    <div class="p-3 sm:p-4">
        <div class="grid gap-2 sm:gap-2.5"
             style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr));">
            @foreach ($orderedLinks as $link)
                @php
                    $dense = $columns >= 8;
                    $iconBox = $dense ? 'w-7 h-7' : 'w-9 h-9';
                    $iconSize = $dense ? 'w-3.5 h-3.5' : 'w-4 h-4';
                    $labelSize = $columns >= 10
                        ? 'text-[10px] leading-tight'
                        : ($dense ? 'text-[11px] leading-tight' : 'text-xs leading-snug');
                    $cardPad = $dense ? 'p-1.5' : 'p-2';
                @endphp
                <a href="{{ route($link['route'], ['organization' => $organization]) }}"
                   wire:key="ql-{{ $link['route'] }}"
                   class="flex flex-col items-center justify-start {{ $cardPad }} rounded-lg border border-gray-200 hover:bg-gray-50 bg-white transition overflow-hidden min-w-0">
                    <div class="{{ $iconBox }} bg-{{ $link['color'] }}-100 rounded-full flex items-center justify-center mb-1 shrink-0">
                        <x-icon name="{{ $link['icon'] }}" class="{{ $iconSize }} text-{{ $link['color'] }}-600" />
                    </div>
                    <span class="block w-full {{ $labelSize }} font-medium text-center text-gray-800 break-words"
                          style="overflow-wrap: anywhere; word-break: break-word;">
                        {{ $link['title'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</div>
