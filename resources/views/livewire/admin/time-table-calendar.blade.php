<div>

    {{-- ══════════════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════════════ --}}
    <div class="bg-white border-b border-gray-200 px-6 py-5 sticky top-0 z-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">School Calendar</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage your academic schedule and events</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">

                {{-- Analytics inline text --}}
                <div class="hidden sm:flex items-center gap-4 text-sm text-gray-500 mr-3 divide-x divide-gray-200">
                    <span class="pr-4">
                        Today: <strong class="text-gray-800">{{ $eventsCount['today'] }}</strong>
                    </span>
                    <span class="px-4">
                        This Week: <strong class="text-gray-800">{{ $eventsCount['this_week'] }}</strong>
                    </span>
                    <span class="px-4">
                        This Month: <strong class="text-gray-800">{{ $eventsCount['current_month'] }}</strong>
                    </span>
                    <span class="pl-4">
                        This Year: <strong class="text-gray-800">{{ $eventsCount['this_year'] }}</strong>
                    </span>
                </div>

                <button wire:click="goToCurrentMonth"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200
                           text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Today
                </button>

                <button wire:click="onAddEvent"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700
                           text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Event
                </button>
            </div>
        </div>

        {{-- Mobile analytics --}}
        <div class="flex sm:hidden items-center gap-4 text-xs text-gray-500 mt-3 flex-wrap">
            <span>Today: <strong class="text-gray-800">{{ $eventsCount['today'] }}</strong></span>
            <span>Week: <strong class="text-gray-800">{{ $eventsCount['this_week'] }}</strong></span>
            <span>Month: <strong class="text-gray-800">{{ $eventsCount['current_month'] }}</strong></span>
            <span>Year: <strong class="text-gray-800">{{ $eventsCount['this_year'] }}</strong></span>
        </div>
    </div>

    <div class="p-6 space-y-6">

        {{-- ══════════════════════════════════════════════════
             FULL CALENDAR
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

            {{-- Calendar Header --}}
            <div
                class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">

                {{-- View Toggle --}}
                <div class="flex gap-2">
                    <button wire:click="switchToMonthlyView"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-all
                               {{ $view === 'month' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Monthly
                    </button>
                    <button wire:click="switchToYearlyView"
                        class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg transition-all
                               {{ $view === 'year' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Yearly
                    </button>
                </div>

                {{-- Navigation --}}
                <div class="flex items-center gap-3">
                    @if ($view === 'month')
                        <button wire:click="goToPreviousMonth"
                            class="p-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <h2 class="text-base font-bold text-gray-800 min-w-40 text-center">
                            {{ $startsAt->format('F Y') }}
                        </h2>
                        <button wire:click="goToNextMonth"
                            class="p-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <button wire:click="goToPreviousYear"
                            class="p-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>
                        <h2 class="text-base font-bold text-gray-800 min-w-24 text-center">
                            {{ $startsAt->format('Y') }}
                        </h2>
                        <button wire:click="goToNextYear"
                            class="p-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>

            {{-- Calendar Body --}}
            <div class="p-4">

                {{-- Monthly View --}}
                @if ($view === 'month')
                    <div class="rounded-xl overflow-hidden border border-gray-200">
                        <div class="grid grid-cols-7 bg-gray-50 border-b border-gray-200">
                            @foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                                <div
                                    class="py-3 text-center text-sm font-semibold text-gray-600 border-r last:border-r-0 border-gray-200">
                                    {{ $day }}
                                </div>
                            @endforeach
                        </div>
                        <div class="grid grid-cols-7">
                            @foreach ($monthGrid as $week)
                                @foreach ($week as $day)
                                    @php
                                        $isCurrentMonth = $day->month == $startsAt->month;
                                        $isToday = $day->isToday();
                                        $dayEvents = $getEventsForDay($day);
                                        $eventsCount = count($dayEvents);
                                    @endphp
                                    <div class="min-h-32 p-2 border border-gray-100 cursor-pointer transition-all duration-200
                                        {{ !$isCurrentMonth ? 'bg-gray-50/60' : 'bg-white hover:bg-blue-50/30' }}"
                                        wire:click="onDayClick('{{ $day->year }}', '{{ $day->month }}', '{{ $day->day }}')">

                                        <div class="flex justify-between items-center mb-1.5">
                                            <span
                                                class="text-sm font-medium leading-none
                                                {{ $isToday ? 'w-7 h-7 bg-blue-600 text-white rounded-full flex items-center justify-center' : ($isCurrentMonth ? 'text-gray-800' : 'text-gray-400') }}">
                                                {{ $day->day }}
                                            </span>
                                            @if ($eventsCount > 0)
                                                <span
                                                    class="text-xs px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium">
                                                    {{ $eventsCount }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="space-y-1 max-h-24 overflow-y-auto">
                                            @foreach ($dayEvents as $event)
                                                <div class="text-xs px-2 py-1 rounded-lg border-l-4 truncate cursor-pointer hover:opacity-90 transition-opacity"
                                                    style="border-left-color: {{ $event['color'] }}; background-color: {{ $event['color'] }}18;"
                                                    wire:click.stop="onEventClick('{{ $event['id'] }}')"
                                                    title="{{ $event['title'] }}">
                                                    <div class="font-medium text-gray-800 truncate">
                                                        {{ $event['title'] }}</div>
                                                    @if ($event['start_time'] && !$event['is_all_day'])
                                                        <div class="text-gray-500 text-xs">{{ $event['start_time'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Yearly View --}}
                @if ($view === 'year')
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($yearlyCalendar as $month)
                            <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md hover:border-blue-200
                                transition-all duration-200 cursor-pointer group"
                                wire:click="switchToMonthlyView('{{ $month['year'] }}', '{{ $month['month'] }}')">
                                <div class="flex justify-between items-center mb-3">
                                    <h3
                                        class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                                        {{ $month['name'] }}
                                    </h3>
                                    <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full">
                                        {{ count(array_filter($month['days'], fn($day) => count($day['events']) > 0)) }}
                                        events
                                    </span>
                                </div>
                                <div class="grid grid-cols-7 gap-0.5 text-center text-xs">
                                    @foreach (['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                                        <div class="font-medium text-gray-400 py-1">{{ $day }}</div>
                                    @endforeach
                                    @foreach ($month['days'] as $day)
                                        <div
                                            class="py-1.5 rounded-lg relative transition-colors
                                            {{ $day['isCurrentMonth'] ? 'text-gray-700 hover:bg-blue-50' : 'text-gray-300' }}
                                            {{ $day['isToday'] ? 'bg-blue-100 text-blue-700 font-bold' : '' }}">
                                            {{ $day['day'] }}
                                            @if (count($day['events']) > 0)
                                                <div
                                                    class="absolute bottom-0.5 left-1/2 -translate-x-1/2 w-1 h-1 bg-blue-500 rounded-full">
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════
             UPCOMING EVENTS — FULL WIDTH
        ══════════════════════════════════════════════════ --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-base font-semibold text-gray-900">Upcoming Events</h3>
                <span class="text-xs text-gray-400">{{ count($upcomingEvents) }} events</span>
            </div>

            @if (count($upcomingEvents) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-6">
                    @foreach ($upcomingEvents as $event)
                        <div class="group relative bg-gray-50 border border-gray-200 rounded-xl p-4
                hover:border-blue-300 hover:shadow-md transition-all duration-200 cursor-pointer"
                            wire:click="onEventClick({{ $event['id'] }})">

                            {{-- Color bar --}}
                            <div class="absolute top-0 left-0 w-1 h-full rounded-l-xl"
                                style="background-color: {{ $event['color'] }}"></div>

                            <div class="pl-3">
                                {{-- Title + dot --}}
                                <div class="flex items-start justify-between gap-2 mb-2">
                                    <h4
                                        class="font-semibold text-gray-900 text-sm group-hover:text-blue-600
                            transition-colors leading-snug line-clamp-1">
                                        {{ $event['title'] }}
                                    </h4>
                                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0 mt-0.5"
                                        style="background-color: {{ $event['color'] }}"></div>
                                </div>

                                {{-- Description --}}
                                @if (!empty($event['description']))
                                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-2 mb-2">
                                        {{ $event['description'] }}
                                    </p>
                                @endif

                                {{-- Date --}}
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-1">
                                    <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ Carbon\Carbon::parse($event['date'])->format('D, M d') }}

                                    @if ($event['start_time'] && !$event['is_all_day'])
                                        <span class="text-gray-300 mx-1">•</span>
                                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $event['start_time'] }}
                                    @elseif($event['is_all_day'])
                                        <span class="text-gray-300 mx-1">•</span>
                                        <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">All
                                            Day</span>
                                    @endif
                                </div>

                                {{-- Badges --}}
                                <div class="flex flex-wrap gap-1.5 mt-2">
                                    @if ($event['location'])
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                bg-gray-100 text-gray-600 rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            </svg>
                                            {{ $event['location'] }}
                                        </span>
                                    @endif
                                    @if ($event['class'])
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2 py-0.5
                                bg-purple-100 text-purple-700 rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            {{ $event['class'] }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm mb-3">No upcoming events scheduled</p>
                    <button wire:click="onAddEvent"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700
                   text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Add your first event
                    </button>
                </div>
            @endif
        </div>

    </div>


    @if ($showSlider)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[9999] flex items-center justify-center p-4 sm:p-6">

            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl flex flex-col max-h-[90vh]">

                {{-- ✅ STICKY HEADER --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                    <h3 class="text-lg font-bold text-gray-900">{{ $sliderTitle }}</h3>
                    <button wire:click="closeSlider"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-400
                           hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- ✅ SCROLLABLE BODY ONLY --}}
                <div class="overflow-y-auto flex-1 p-6">

                    @if (isset($sliderData['mode']) && $sliderData['mode'] === 'view')
                        <div class="space-y-4">

                            {{-- Event Title + Type --}}
                            <div class="flex items-center gap-3 pb-4 border-b border-gray-100">
                                <div class="w-4 h-4 rounded-full flex-shrink-0"
                                    style="background-color: {{ $sliderData['event']['color'] ?? '#3b82f6' }}"></div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">{{ $sliderData['event']['title'] }}
                                    </h2>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <span
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1
                                        bg-blue-50 text-blue-700 rounded-full border border-blue-100">
                                            {{ ucfirst($sliderData['event']['event_type'] ?? 'Event') }}
                                        </span>
                                        @if ($sliderData['event']['is_all_day'] ?? false)
                                            <span
                                                class="inline-flex items-center gap-1 text-xs px-2.5 py-1
                                            bg-green-50 text-green-700 rounded-full border border-green-100">
                                                All Day
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Date & Time --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Date
                                    </p>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ Carbon\Carbon::parse($sliderData['event']['date'] ?? now())->format('l, F d, Y') }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Time
                                    </p>
                                    <p class="text-sm font-medium text-gray-800">
                                        @if ($sliderData['event']['is_all_day'] ?? false)
                                            All Day Event
                                        @elseif (isset($sliderData['event']['start_time']))
                                            {{ $sliderData['event']['start_time'] }} –
                                            {{ $sliderData['event']['end_time'] }}
                                        @else
                                            —
                                        @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Description --}}
                            @if (!empty($sliderData['event']['description']))
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                        Description</p>
                                    <p class="text-sm text-gray-700 leading-relaxed">
                                        {{ $sliderData['event']['description'] }}</p>
                                </div>
                            @endif

                            {{-- Location --}}
                            @if (!empty($sliderData['event']['location']))
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                        Location</p>
                                    <p class="text-sm font-medium text-gray-800">
                                        {{ $sliderData['event']['location'] }}</p>
                                </div>
                            @endif

                            {{-- Class Info --}}
                            @if (
                                !empty($sliderData['event']['standard']) ||
                                    !empty($sliderData['event']['subject']) ||
                                    !empty($sliderData['event']['teacher']))
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Class
                                        Information</p>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        @if (!empty($sliderData['event']['standard']))
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Class</p>
                                                <p class="text-sm font-medium text-gray-800">
                                                    {{ $sliderData['event']['standard'] }}
                                                    @if (!empty($sliderData['event']['section']))
                                                        – {{ $sliderData['event']['section'] }}
                                                    @endif
                                                </p>
                                            </div>
                                        @endif
                                        @if (!empty($sliderData['event']['subject']))
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Subject</p>
                                                <p class="text-sm font-medium text-gray-800">
                                                    {{ $sliderData['event']['subject'] }}</p>
                                            </div>
                                        @endif
                                        @if (!empty($sliderData['event']['teacher']))
                                            <div>
                                                <p class="text-xs text-gray-400 mb-0.5">Teacher</p>
                                                <p class="text-sm font-medium text-gray-800">
                                                    {{ $sliderData['event']['teacher'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                        </div>
                    @else
                        <livewire:admin.event-form :date="$sliderData['date'] ?? null" :event="$sliderData['event'] ?? null" :mode="$sliderData['mode'] ?? 'create'" />
                    @endif

                </div>

                {{-- ✅ STICKY FOOTER --}}
                <div
                    class="flex items-center justify-between px-6 py-4 border-t border-gray-200 flex-shrink-0 bg-white rounded-b-xl">
                    @if (isset($sliderData['mode']) && $sliderData['mode'] === 'view')
                        <p class="text-xs text-gray-400"># {{ $sliderData['event']['id'] }}</p>
                        <button wire:click="onEditEvent({{ $sliderData['event']['id'] ?? 0 }})"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700
                               text-white text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit event
                        </button>
                    @else
                        <button wire:click="closeSlider"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                            Cancel
                        </button>
                        {{-- Save button event-form ke andar se emit hoga --}}
                    @endif
                </div>

            </div>
        </div>
    @endif

</div>
