<div class="p-4">
    {{-- Main Tabs --}}
    <div class="flex justify-center items-center mb-6">
        <div class="flex gap-2 bg-white p-1 rounded-lg shadow-md">
            @foreach (array_keys($tabs) as $tabKey)
                <button
                    class="px-6 py-2 rounded-md transition-all {{ $activeTab === $tabKey ? 'bg-gradient-3 text-white shadow-md' : 'text-gray-600 hover:bg-pink-200' }}"
                    wire:click="showTab('{{ $tabKey }}')">
                    {{ ucwords(str_replace('_', ' ', $tabKey)) }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Sub Tabs --}}
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            @foreach ($tabs[$activeTab] as $subTabKey => $subTabLabel)
                <button wire:click="setSubTab('{{ $subTabKey }}')"
                    class="relative whitespace-nowrap py-4 px-1 font-medium text-sm {{ $subTab === $subTabKey ? 'text-gradient-3' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $subTabLabel }}
                    @if ($subTab === $subTabKey)
                        <div
                            class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-[#ff00cc] via-[#cc00ff] to-[#3366ff]">
                        </div>
                    @endif
                </button>
            @endforeach
        </nav>
    </div>

    {{-- Content Area --}}
    <div class="py-6">
        {{-- Teacher Attendance Marking with Checkboxes --}}
        @if ($activeTab === 'teacher_attendance' && $subTab === 'teacher_attendance')
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Mark Teacher Attendance</h2>
                    <div class="flex items-center space-x-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Attendance Date</label>
                            <input type="date" wire:model.live="teacher_attendance_date"
                                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                @if ($teacherAttendanceData->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Teacher</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Attendance Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Remarks</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Last Updated</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($teacherAttendanceData as $teacher)
                                    @php
                                        $existingAttendance = $teacher->attendance->first();
                                        $currentDbStatus = $existingAttendance ? $existingAttendance->status : 4;
                                        $currentStatus = $this->getStatusLabel($currentDbStatus);
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                                        {{ substr($teacher->user->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $teacher->user->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $teacher->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-4">
                                                <!-- Present Checkbox (1) -->
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="radio" name="attendance_{{ $teacher->id }}"
                                                        value="present"
                                                        wire:click="updateTeacherAttendance({{ $teacher->id }}, 'present')"
                                                        {{ $currentStatus === 'present' ? 'checked' : '' }}
                                                        class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                                    <span
                                                        class="text-sm text-gray-700 {{ $currentStatus === 'present' ? 'font-semibold text-green-600' : '' }}">Present</span>
                                                </label>

                                                <!-- Absent Checkbox (0) -->
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="radio" name="attendance_{{ $teacher->id }}"
                                                        value="absent"
                                                        wire:click="updateTeacherAttendance({{ $teacher->id }}, 'absent')"
                                                        {{ $currentStatus === 'absent' ? 'checked' : '' }}
                                                        class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300">
                                                    <span
                                                        class="text-sm text-gray-700 {{ $currentStatus === 'absent' ? 'font-semibold text-red-600' : '' }}">Absent</span>
                                                </label>

                                                <!-- Late Checkbox (2) -->
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="radio" name="attendance_{{ $teacher->id }}"
                                                        value="late"
                                                        wire:click="updateTeacherAttendance({{ $teacher->id }}, 'late')"
                                                        {{ $currentStatus === 'late' ? 'checked' : '' }}
                                                        class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300">
                                                    <span
                                                        class="text-sm text-gray-700 {{ $currentStatus === 'late' ? 'font-semibold text-yellow-600' : '' }}">Late</span>
                                                </label>

                                                <!-- Half Day Checkbox (3) -->
                                                <label class="flex items-center space-x-2 cursor-pointer">
                                                    <input type="radio" name="attendance_{{ $teacher->id }}"
                                                        value="half_day"
                                                        wire:click="updateTeacherAttendance({{ $teacher->id }}, 'half_day')"
                                                        {{ $currentStatus === 'half_day' ? 'checked' : '' }}
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                                    <span
                                                        class="text-sm text-gray-700 {{ $currentStatus === 'half_day' ? 'font-semibold text-blue-600' : '' }}">Half
                                                        Day</span>
                                                </label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="text"
                                                wire:model.live="teacher_attendance_data.{{ $teacher->id }}.remarks"
                                                wire:change="updateTeacherRemarks({{ $teacher->id }})"
                                                placeholder="Optional remarks"
                                                class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($existingAttendance && $existingAttendance->updated_at)
                                                {{ $existingAttendance->updated_at->format('d M Y h:i A') }}
                                            @else
                                                Not marked yet
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 text-lg">No teachers found.</div>
                    </div>
                @endif
            </div>

            {{-- Teacher Attendance List --}}
        @elseif ($activeTab === 'teacher_attendance' && $subTab === 'teacher_attendance_list')
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Teacher Attendance List</h2>
                    <button wire:click="exportTeacherAttendance"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </div>

                {{-- Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" wire:model.live="filter_date"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                        <select wire:model.live="filter_teacher"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Teachers</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="filter_status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button wire:click="$set('filter_date', '{{ now()->format('Y-m-d') }}')"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Today
                        </button>
                    </div>
                </div>

                @if ($teacherAttendanceList->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Teacher</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Remarks</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Marked By</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Time</th>
                                </tr>
                            </thead>
                            @php
                                // Safe date formatting functions - DECLARE ONCE OUTSIDE LOOP
                                function formatDateSafe($date)
                                {
                                    try {
                                        if (is_string($date)) {
                                            return \Carbon\Carbon::parse($date)->format('d M Y');
                                        }
                                        return $date->format('d M Y');
                                    } catch (\Exception $e) {
                                        return 'Invalid Date';
                                    }
                                }

                                function formatTimeSafe($datetime)
                                {
                                    try {
                                        if (is_string($datetime)) {
                                            return \Carbon\Carbon::parse($datetime)->format('h:i A');
                                        }
                                        return $datetime->format('h:i A');
                                    } catch (\Exception $e) {
                                        return 'Invalid Time';
                                    }
                                }
                            @endphp

                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($teacherAttendanceList as $record)
                                    @php
                                        // Status mapping
                                        $statusMap = [
                                            1 => ['label' => 'Present', 'class' => 'bg-green-100 text-green-800'],
                                            0 => ['label' => 'Absent', 'class' => 'bg-red-100 text-red-800'],
                                            2 => ['label' => 'Late', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            3 => ['label' => 'Half Day', 'class' => 'bg-blue-100 text-blue-800'],
                                        ];
                                        $statusInfo = $statusMap[$record->status] ?? [
                                            'label' => 'Unknown',
                                            'class' => 'bg-gray-100 text-gray-800',
                                        ];
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                                        {{ $record->teacherDetail && $record->teacherDetail->user ? substr($record->teacherDetail->user->name, 0, 1) : 'N' }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $record->teacherDetail && $record->teacherDetail->user ? $record->teacherDetail->user->name : 'Unknown Teacher' }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ $record->teacherDetail && $record->teacherDetail->user ? $record->teacherDetail->user->email : 'N/A' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatDateSafe($record->attendance_date) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusInfo['class'] }}">
                                                {{ $statusInfo['label'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $record->remarks ?: 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $record->recordedBy->name ?? 'System' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatTimeSafe($record->updated_at) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No attendance records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $teacherAttendanceList->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 text-lg">No attendance records found.</div>
                    </div>
                @endif
            </div>

            {{-- Teacher Attendance Dashboard --}}
        @elseif ($activeTab === 'teacher_attendance' && $subTab === 'teacher_attendance_dashboard')
            <div class="space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Teachers -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Teachers</p>
                                <h3 class="text-2xl font-bold mt-1">
                                    {{ number_format($teacherStats['total_teachers']['count']) }}</h3>
                                <p
                                    class="{{ $teacherStats['total_teachers']['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }} text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        @if ($teacherStats['total_teachers']['trend'] === 'up')
                                            <path fill-rule="evenodd"
                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd"
                                                d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                                clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                    {{ abs($teacherStats['total_teachers']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Present Today -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Present Today</p>
                                <h3 class="text-2xl font-bold mt-1">
                                    {{ number_format($teacherStats['present_today']['count']) }}</h3>
                                <p
                                    class="{{ $teacherStats['present_today']['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }} text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        @if ($teacherStats['present_today']['trend'] === 'up')
                                            <path fill-rule="evenodd"
                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd"
                                                d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                                clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                    {{ abs($teacherStats['present_today']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Absent Today -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Absent Today</p>
                                <h3 class="text-2xl font-bold mt-1">{{ $teacherStats['absent_today']['count'] }}</h3>
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ abs($teacherStats['absent_today']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Late Today -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Late Today</p>
                                <h3 class="text-2xl font-bold mt-1">{{ $teacherStats['late_today']['count'] }}</h3>
                                <p
                                    class="{{ $teacherStats['late_today']['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }} text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        @if ($teacherStats['late_today']['trend'] === 'up')
                                            <path fill-rule="evenodd"
                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd"
                                                d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                                clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                    {{ abs($teacherStats['late_today']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Teacher Attendance Trend Chart -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Attendance Trend (Last 6 Months)</h3>
                        <div class="h-80" wire:ignore>
                            <canvas id="teacherTrendChart" x-data="{
                                chart: null,
                                init() {
                                    const ctx = this.$el.getContext('2d');
                                    this.chart = new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: @js($teacherTrendData['months']),
                                            datasets: [{
                                                label: 'Present',
                                                data: @js($teacherTrendData['present']),
                                                borderColor: 'rgb(16, 185, 129)',
                                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                                tension: 0.3,
                                                fill: true
                                            }, {
                                                label: 'Absent',
                                                data: @js($teacherTrendData['absent']),
                                                borderColor: 'rgb(239, 68, 68)',
                                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                                tension: 0.3,
                                                fill: true
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { position: 'top' } }
                                        }
                                    });
                                }
                            }"></canvas>
                        </div>
                    </div>

                    <!-- Teacher Status Distribution Chart -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Status Distribution</h3>
                        <div class="h-80" wire:ignore>
                            <canvas id="teacherStatusChart" x-data="{
                                chart: null,
                                init() {
                                    const ctx = this.$el.getContext('2d');
                                    this.chart = new Chart(ctx, {
                                        type: 'doughnut',
                                        data: {
                                            labels: @js(array_keys($teacherStatusData)),
                                            datasets: [{
                                                data: @js(array_values($teacherStatusData)),
                                                backgroundColor: [
                                                    'rgb(16, 185, 129)',
                                                    'rgb(239, 68, 68)',
                                                    'rgb(245, 158, 11)',
                                                    'rgb(59, 130, 246)'
                                                ],
                                                borderWidth: 0
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { position: 'right' } }
                                        }
                                    });
                                }
                            }"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Student Attendance List --}}
        @elseif ($activeTab === 'student_attendance' && $subTab === 'student_attendance_list')
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Student Attendance List</h2>
                    <button wire:click="exportStudentAttendance"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </div>

                {{-- Filters --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" wire:model.live="filter_date"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Standard</label>
                        <select wire:model.live="selected_standard"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Standards</option>
                            @foreach ($standards as $standard)
                                <option value="{{ $standard->id }}">{{ $standard->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="filter_status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="late">Late</option>
                            <option value="half_day">Half Day</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button wire:click="$set('filter_date', '{{ now()->format('Y-m-d') }}')"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Today
                        </button>
                    </div>
                </div>

                @if ($studentAttendanceList->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Class</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Remarks</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Marked By</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($studentAttendanceList as $record)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div
                                                        class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center text-white font-bold">
                                                        {{ substr($record->studentDetail->user->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $record->studentDetail->user->name }}</div>
                                                    <div class="text-sm text-gray-500">Roll:
                                                        {{ $record->studentDetail->roll_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $record->studentDetail->standard->name }} -
                                            {{ $record->studentDetail->section->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $record->attendance_date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $record->status === 'present'
                                            ? 'bg-green-100 text-green-800'
                                            : ($record->status === 'absent'
                                                ? 'bg-red-100 text-red-800'
                                                : ($record->status === 'late'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : 'bg-blue-100 text-blue-800')) }}">
                                                {{ ucfirst(str_replace('_', ' ', $record->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $record->remarks ?: 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $record->markerdBy->name ?? 'System' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $studentAttendanceList->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-500 text-lg">No attendance records found.</div>
                    </div>
                @endif
            </div>

            {{-- Student Attendance Dashboard --}}
        @elseif ($activeTab === 'student_attendance' && $subTab === 'student_attendance_dashboard')
            <div class="space-y-6">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Students -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Students</p>
                                <h3 class="text-2xl font-bold mt-1">
                                    {{ number_format($studentStats['total_students']['count']) }}</h3>
                                <p
                                    class="{{ $studentStats['total_students']['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }} text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        @if ($studentStats['total_students']['trend'] === 'up')
                                            <path fill-rule="evenodd"
                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd"
                                                d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                                clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                    {{ abs($studentStats['total_students']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Present Today -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Present Today</p>
                                <h3 class="text-2xl font-bold mt-1">
                                    {{ number_format($studentStats['present_today']['count']) }}</h3>
                                <p
                                    class="{{ $studentStats['present_today']['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }} text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        @if ($studentStats['present_today']['trend'] === 'up')
                                            <path fill-rule="evenodd"
                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd"
                                                d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                                clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                    {{ abs($studentStats['present_today']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Absent Today -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Absent Today</p>
                                <h3 class="text-2xl font-bold mt-1">{{ $studentStats['absent_today']['count'] }}</h3>
                                <p class="text-red-500 text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ abs($studentStats['absent_today']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Late Today -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Late Today</p>
                                <h3 class="text-2xl font-bold mt-1">{{ $studentStats['late_today']['count'] }}</h3>
                                <p
                                    class="{{ $studentStats['late_today']['trend'] === 'up' ? 'text-green-500' : 'text-red-500' }} text-sm mt-2 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        @if ($studentStats['late_today']['trend'] === 'up')
                                            <path fill-rule="evenodd"
                                                d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                                                clip-rule="evenodd" />
                                        @else
                                            <path fill-rule="evenodd"
                                                d="M12 13a1 1 0 100 2h5a1 1 0 001-1v-5a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586l-4.293-4.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                                                clip-rule="evenodd" />
                                        @endif
                                    </svg>
                                    {{ abs($studentStats['late_today']['growth']) }}% from yesterday
                                </p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-yellow-600"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Student Attendance Trend Chart -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Attendance Trend (Last 6 Months)</h3>
                        <div class="h-80" wire:ignore>
                            <canvas id="studentTrendChart" x-data="{
                                chart: null,
                                init() {
                                    const ctx = this.$el.getContext('2d');
                                    this.chart = new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: @js($studentTrendData['months']),
                                            datasets: [{
                                                label: 'Present',
                                                data: @js($studentTrendData['present']),
                                                borderColor: 'rgb(16, 185, 129)',
                                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                                tension: 0.3,
                                                fill: true
                                            }, {
                                                label: 'Absent',
                                                data: @js($studentTrendData['absent']),
                                                borderColor: 'rgb(239, 68, 68)',
                                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                                tension: 0.3,
                                                fill: true
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { position: 'top' } }
                                        }
                                    });
                                }
                            }"></canvas>
                        </div>
                    </div>

                    <!-- Student Status Distribution Chart -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Status Distribution</h3>
                        <div class="h-80" wire:ignore>
                            <canvas id="studentStatusChart" x-data="{
                                chart: null,
                                init() {
                                    const ctx = this.$el.getContext('2d');
                                    this.chart = new Chart(ctx, {
                                        type: 'doughnut',
                                        data: {
                                            labels: @js(array_keys($studentStatusData)),
                                            datasets: [{
                                                data: @js(array_values($studentStatusData)),
                                                backgroundColor: [
                                                    'rgb(16, 185, 129)',
                                                    'rgb(239, 68, 68)',
                                                    'rgb(245, 158, 11)',
                                                    'rgb(59, 130, 246)'
                                                ],
                                                borderWidth: 0
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { position: 'right' } }
                                        }
                                    });
                                }
                            }"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Class-wise Attendance -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Class-wise Attendance (%)</h3>
                    <div class="h-64" wire:ignore>
                        <canvas id="classWiseChart" x-data="{
                            chart: null,
                            init() {
                                const ctx = this.$el.getContext('2d');
                                this.chart = new Chart(ctx, {
                                    type: 'bar',
                                    data: {
                                        labels: @js(array_keys($classWiseData)),
                                        datasets: [{
                                            label: 'Attendance %',
                                            data: @js(array_values($classWiseData)),
                                            backgroundColor: 'rgba(79, 70, 229, 0.7)',
                                            borderColor: 'rgb(79, 70, 229)',
                                            borderWidth: 1
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        plugins: { legend: { display: false } },
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                max: 100,
                                                title: {
                                                    display: true,
                                                    text: 'Percentage (%)'
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        }"></canvas>
                    </div>
                </div>
            </div>

            {{-- Assign Teacher --}}
        @elseif ($activeTab === 'assign_teacher_class' && $subTab === 'assign_teacher')
            <div>
                <div class="flex justify-end gap-4 p-4 items-center">
                    <button class="px-4 py-2 bg-gradient-3 hover:bg-gradient-3-hover text-white rounded-lg"
                        wire:click="openModalAssignTeacher()">Assign Teacher</button>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Teacher
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Class
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Section
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($assignments as $assignment)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $assignment->teacher?->user?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $assignment->standard?->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                            {{ $assignment->section?->name ?? 'All Sections' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button type="button" wire:click="onEdit({{ $assignment->id }})"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                Edit
                                            </button>
                                            <button type="button" wire:click="onDelete({{ $assignment->id }})"
                                                wire:confirm="Delete this teacher assignment?"
                                                class="text-red-600 hover:text-red-900">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                            No teacher assignments found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Form Teacher Assign class -->
    <x-modal-form show="{{ $showModalAssignTeacher }}"
        title="{{ $editId ? 'Edit Teacher Assignment' : 'Assign Teacher to Class' }}" submitAction="saveAssignment"
        submitButton="{{ $editId ? 'Update' : 'Assign' }}" closeAction="closeModalAssignTeacher">
        <div class="grid grid-cols-1 gap-6">
            <!-- Teacher Select -->
            <x-native-select wire:model.defer="teacher_detail_id" label="Select Teacher"
                placeholder="Select a teacher" :options="$teachers
                    ->map(function ($teacher) {
                        return [
                            'value' => $teacher->id,
                            'label' => $teacher->user->name,
                        ];
                    })
                    ->toArray()" option-value="value" option-label="label" />

            <!-- Standard Select -->
            <x-native-select wire:model.live="standard_id" label="Select Standard/Class"
                placeholder="Select a standard" :options="$standards
                    ->map(function ($standard) {
                        return [
                            'value' => $standard->id,
                            'label' => $standard->name,
                        ];
                    })
                    ->toArray()" option-value="value" option-label="label" />

            <!-- Section Select -->
            <x-native-select wire:model.defer="section_id" label="Select Section"
                placeholder="{{ count($filteredSections) ? 'Select a section' : 'Please select standard first' }}"
                :options="$filteredSections" option-value="value" option-label="label" :disabled="!$standard_id" />
        </div>
    </x-modal-form>
</div>
