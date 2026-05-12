<?php

namespace App\Livewire\Admin;

use App\Models\Calendar\TimeTable;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Omnia\LivewireCalendar\LivewireCalendar;

class TimeTableCalendar extends LivewireCalendar
{
    public string $view = 'month';
    public $showSlider = false;
    public $sliderTitle = '';
    public $sliderData = [];
    public $selectedDate = null;
    public $selectedEvent = null;
    public $showCalendar = true;

    public function mount(
        $initialYear = null,
        $initialMonth = null,
        $weekStartsAt = null,
        $calendarView = null,
        $dayView = null,
        $eventView = null,
        $dayOfWeekView = null,
        $dragAndDropClasses = null,
        $beforeCalendarView = null,
        $afterCalendarView = null,
        $pollMillis = null,
        $pollAction = null,
        $dragAndDropEnabled = true,
        $dayClickEnabled = true,
        $eventClickEnabled = true,
        $extras = []
    ) {
        $this->view = 'month';
        $this->showCalendar = true;

        // Force current month and year
        $now = Carbon::now();
        $initialYear =  $now->year;
        $initialMonth = $now->month;

        // Set week start (Sunday by default)
        $weekStartsAt = $weekStartsAt ?? Carbon::SUNDAY;

        parent::mount(
            $initialYear,
            $initialMonth,
            $weekStartsAt,
            $calendarView,
            $dayView,
            $eventView,
            $dayOfWeekView,
            $dragAndDropClasses,
            $beforeCalendarView,
            $afterCalendarView,
            $pollMillis,
            $pollAction,
            $dragAndDropEnabled,
            $dayClickEnabled,
            $eventClickEnabled,
            $extras
        );

        // Override parent's dates to ensure current month
        $this->startsAt = Carbon::create($initialYear, $initialMonth, 1)->startOfDay();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();
        $this->calculateGridStartsEnds();
    }

    public function toggleCalendar()
    {
        $this->showCalendar = !$this->showCalendar;
    }

    public function goToCurrentMonth()
    {
        $now = Carbon::now();
        $this->startsAt = Carbon::create($now->year, $now->month, 1)->startOfDay();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();
        $this->calculateGridStartsEnds();
    }

    public function goToPreviousMonth()
    {
        $this->startsAt->subMonthNoOverflow();
        $this->endsAt->subMonthNoOverflow();
        $this->calculateGridStartsEnds();

        // Trigger recomputation of computed properties
        $this->dispatch('refresh-calendar');
    }

    public function goToNextMonth()
    {
        $this->startsAt->addMonthNoOverflow();
        $this->endsAt->addMonthNoOverflow();
        $this->calculateGridStartsEnds();

        // Trigger recomputation of computed properties
        $this->dispatch('refresh-calendar');
    }

    public function calculateGridStartsEnds()
    {
        // Override parent method to ensure proper timezone handling
        $this->gridStartsAt = $this->startsAt->clone()
            ->startOfWeek($this->weekStartsAt)
            ->startOfDay();
        $this->gridEndsAt = $this->endsAt->clone()
            ->endOfWeek($this->weekEndsAt)
            ->startOfDay();
    }

    #[Computed]
    public function events(): Collection
    {
        $organizationId = Auth::user()->organization_id;

        return TimeTable::with(['academic.standard', 'academic.section', 'academic.subject', 'academic.teacher', 'location'])
            ->where('organization_id', $organizationId)
            ->where('is_cancelled', false)
            ->whereBetween('date', [$this->gridStartsAt, $this->gridEndsAt])
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'date' => $event->date,
                    'start_time' => $event->start_time?->format('H:i'),
                    'end_time' => $event->end_time?->format('H:i'),
                    'is_all_day' => $event->is_all_day,
                    'color' => $event->color ?? $this->getEventColor($event->event_type),
                    'event_type' => $event->event_type,
                    'location' => $event->location?->location_display,
                    'standard' => $event->academic?->standard?->name,
                    'section' => $event->academic?->section?->name,
                    'subject' => $event->academic?->subject?->name,
                    'teacher' => $event->academic?->teacher?->name,
                ];
            });
    }

    private function getEventColor($eventType)
    {
        return match ($eventType) {
            'class' => '#3b82f6',
            'exam' => '#ef4444',
            'meeting' => '#f59e0b',
            'event' => '#10b981',
            'holiday' => '#8b5cf6',
            default => '#6b7280'
        };
    }

    #[Computed]
    public function upcomingEvents()
    {
        $organizationId = Auth::user()->organization_id;

        return TimeTable::with(['academic.standard', 'academic.section', 'location'])
            ->where('organization_id', $organizationId)
            ->where('is_cancelled', false)
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->date,
                    'description' => $event->description,
                    'start_time' => $event->start_time?->format('h:i A'),
                    'is_all_day' => $event->is_all_day,
                    'color' => $event->color ?? $this->getEventColor($event->event_type),
                    'location' => $event->location?->location_display,
                    'class' => $event->academic?->standard?->name . ($event->academic?->section?->name ? ' - ' . $event->academic->section->name : ''),
                ];
            });
    }

    #[Computed]
    public function eventsCount()
    {
        $organizationId = Auth::user()->organization_id;

        $today = Carbon::today();
        $weekStart = $today->copy()->startOfWeek();
        $weekEnd = $today->copy()->endOfWeek();

        // For current month in view (not calendar's current month)
        $currentMonthStart = $this->startsAt->copy()->startOfMonth();
        $currentMonthEnd = $this->startsAt->copy()->endOfMonth();

        // For actual current calendar month (today's month)
        $calendarMonthStart = $today->copy()->startOfMonth();
        $calendarMonthEnd = $today->copy()->endOfMonth();

        $yearStart = $today->copy()->startOfYear();
        $yearEnd = $today->copy()->endOfYear();

        return [
            'today' => TimeTable::where('organization_id', $organizationId)
                ->where('is_cancelled', false)
                ->whereDate('date', $today)
                ->count(),
            'this_week' => TimeTable::where('organization_id', $organizationId)
                ->where('is_cancelled', false)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->count(),
            'current_month' => TimeTable::where('organization_id', $organizationId)
                ->where('is_cancelled', false)
                ->whereBetween('date', [$currentMonthStart, $currentMonthEnd])
                ->count(),
            'calendar_month' => TimeTable::where('organization_id', $organizationId)
                ->where('is_cancelled', false)
                ->whereBetween('date', [$calendarMonthStart, $calendarMonthEnd])
                ->count(),
            'this_year' => TimeTable::where('organization_id', $organizationId)
                ->where('is_cancelled', false)
                ->whereBetween('date', [$yearStart, $yearEnd])
                ->count(),
            'total' => TimeTable::where('organization_id', $organizationId)
                ->where('is_cancelled', false)
                ->count(),
            'month_name' => $this->startsAt->format('F Y'),
        ];
    }

    #[Computed]
    public function yearlyCalendar(): array
    {
        $year = $this->startsAt->year;
        $calendar = [];

        for ($month = 1; $month <= 12; $month++) {
            $date = Carbon::create($year, $month, 1);
            $daysInMonth = $date->daysInMonth;
            $days = [];

            $firstDayOfMonth = $date->dayOfWeek;

            for ($i = 0; $i < $firstDayOfMonth; $i++) {
                $days[] = [
                    'day' => '',
                    'isCurrentMonth' => false,
                    'isToday' => false,
                    'events' => [],
                ];
            }

            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($year, $month, $day);
                $days[] = [
                    'day' => $day,
                    'isCurrentMonth' => true,
                    'isToday' => $currentDate->isToday(),
                    'events' => $this->events->filter(function ($event) use ($currentDate) {
                        return Carbon::parse($event['date'])->isSameDay($currentDate);
                    })->values()->toArray(),
                ];
            }

            $calendar[] = [
                'name' => $date->format('F'),
                'month' => $month,
                'year' => $year,
                'days' => $days,
            ];
        }

        return $calendar;
    }

    public function switchToMonthlyView($year = null, $month = null)
    {
        $this->view = 'month';
        if ($year && $month) {
            $this->startsAt = Carbon::create($year, $month, 1);
            $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();
            $this->calculateGridStartsEnds();
        }

        // Trigger recomputation
        $this->dispatch('refresh-calendar');
    }

    public function switchToYearlyView()
    {
        $this->view = 'year';

        // Trigger recomputation
        $this->dispatch('refresh-calendar');
    }

    public function onEventClick($eventId)
    {
        $organizationId = Auth::user()->organization_id;

        $event = TimeTable::with([
            'academic.standard',
            'academic.section',
            'academic.subject',
            'academic.teacher',
            'location'
        ])
            ->where('organization_id', $organizationId)
            ->where('is_cancelled', false)
            ->find($eventId);

        if (!$event) {
            return;
        }

        // Convert to calendar event format
        $this->selectedEvent = [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'date' => $event->date,
            'start_time' => $event->start_time?->format('H:i'),
            'end_time' => $event->end_time?->format('H:i'),
            'is_all_day' => $event->is_all_day,
            'color' => $event->color ?? $this->getEventColor($event->event_type),
            'event_type' => $event->event_type,
            'location' => $event->location?->location_display,
            'standard' => $event->academic?->standard?->name,
            'section' => $event->academic?->section?->name,
            'subject' => $event->academic?->subject?->name,
            'teacher' => $event->academic?->teacher?->name,
        ];

        $this->sliderTitle = 'Event Details';
        $this->sliderData = ['event' => $this->selectedEvent, 'mode' => 'view'];
        $this->showSlider = true;
    }

    public function onDayClick($year, $month, $day)
    {
        $this->selectedDate = Carbon::create($year, $month, $day);
        $this->sliderTitle = 'Add New Event';
        $this->sliderData = ['date' => $this->selectedDate->format('Y-m-d'), 'mode' => 'create'];
        $this->showSlider = true;
    }

    public function onAddEvent()
    {
        $this->sliderTitle = 'Add New Event';
        $this->sliderData = ['date' => Carbon::today()->format('Y-m-d'), 'mode' => 'create'];
        $this->showSlider = true;
    }

    public function onEditEvent($eventId)
    {
        $organizationId = Auth::user()->organization_id;

        $event = TimeTable::with([
            'academic.standard',
            'academic.section',
            'academic.subject',
            'academic.teacher',
            'location'
        ])
            ->where('organization_id', $organizationId)
            ->where('is_cancelled', false)
            ->find($eventId);

        $this->sliderTitle = 'Edit Event';
        $this->sliderData = ['event' => $event, 'mode' => 'edit'];
        $this->showSlider = true;
    }

    #[On('eventSaved')]
    public function refreshEvents()
    {
        // Refresh the component when event is saved
        $this->showSlider = false;

        // Clear computed property cache
        unset($this->events);
        unset($this->upcomingEvents);
        unset($this->eventsCount);
        unset($this->yearlyCalendar);
    }

    public function closeSlider()
    {
        $this->showSlider = false;
        $this->sliderData = [];
        $this->sliderTitle = '';
        $this->selectedDate = null;
        $this->selectedEvent = null;
    }

    public function goToPreviousYear()
    {
        $this->startsAt = $this->startsAt->subYear();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();
        $this->calculateGridStartsEnds();

        // Trigger recomputation
        $this->dispatch('refresh-calendar');
    }

    public function goToNextYear()
    {
        $this->startsAt = $this->startsAt->addYear();
        $this->endsAt = $this->startsAt->clone()->endOfMonth()->startOfDay();
        $this->calculateGridStartsEnds();

        // Trigger recomputation
        $this->dispatch('refresh-calendar');
    }

    public function render()
    {
        return view('livewire.admin.time-table-calendar', [
            'monthGrid' => $this->monthGrid(),
            'events' => $this->events,
            'getEventsForDay' => function ($day) {
                return $this->getEventsForDay($day, $this->events);
            },
            'yearlyCalendar' => $this->yearlyCalendar,
            'view' => $this->view,
            'startsAt' => $this->startsAt,
            'upcomingEvents' => $this->upcomingEvents,
            'eventsCount' => $this->eventsCount,
        ]);
    }
}
