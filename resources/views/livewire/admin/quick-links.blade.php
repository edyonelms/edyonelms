<div class="min-h-screen bg-gray-50">

    {{-- ── Header + Filter bar (same UI template as Enquiries) ── --}}
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 py-4 sm:py-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Quick Links</h1>
                    <p class="text-sm text-gray-500 mt-0.5">Jump to any module in one click</p>
                </div>
                <div class="hidden lg:flex items-center gap-4 text-sm text-gray-500 divide-x divide-gray-200">
                    <span class="pr-4">Total: <strong class="text-gray-800">{{ count($orderedLinks) }}</strong></span>
                    <span class="pl-4">Per row: <strong class="text-gray-800">{{ $columns }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Filter bar (matches Enquiries page filter UI) --}}
        <div class="border-t border-gray-200 bg-gray-50 px-4 sm:px-6 py-3">
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
    </div>

    {{-- ── Tile grid ── --}}
    <div class="p-4 sm:p-6">
        <div class="grid gap-3 sm:gap-4" style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr));">
            @foreach ($orderedLinks as $link)
                @php
                    $dense = $columns >= 8;
                    $iconBox = $dense ? 'w-8 h-8' : 'w-10 h-10';
                    $iconSize = $dense ? 'w-4 h-4' : 'w-5 h-5';
                    $labelSize = $columns >= 10 ? 'text-[11px] leading-tight' : ($dense ? 'text-xs leading-tight' : 'text-sm');
                    $cardPad = $dense ? 'p-2' : 'p-3';
                @endphp
                <a href="{{ route($link['route'], ['organization' => request()->route('organization')]) }}"
                   wire:key="ql-{{ $link['route'] }}"
                   class="flex flex-col items-center justify-start {{ $cardPad }} rounded-lg border border-gray-200 hover:bg-gray-50 bg-white transition overflow-hidden min-w-0">
                    <div class="{{ $iconBox }} bg-{{ $link['color'] }}-100 rounded-full flex items-center justify-center mb-2 shrink-0">
                        <x-icon name="{{ $link['icon'] }}" class="{{ $iconSize }} text-{{ $link['color'] }}-600" />
                    </div>
                    <span class="block w-full {{ $labelSize }} font-medium text-center text-gray-800 break-words hyphens-auto"
                          style="overflow-wrap: anywhere; word-break: break-word;">
                        {{ $link['title'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>
</div>
