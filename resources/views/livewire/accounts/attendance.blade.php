<div class="min-h-screen bg-gray-50/50">

    {{-- ═══ STICKY HEADER ═══ --}}
    <div class="sticky top-0 z-30 bg-white border-b border-gray-200 shadow-sm">

        {{-- Title + stats --}}
        <div class="px-6 pt-4 pb-3">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h1 class="text-xl font-bold text-emerald-700 leading-tight">Attendance</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Mark and view attendance for teachers &amp; students</p>
                </div>
                @if($activeTab === 'teacher' && $subTab === 'by_date')
                    <x-button emerald label="Save All" icon="check"
                        wire:click="saveAllTeacherAttendance" />
                @endif
            </div>

            {{-- Analytics strip --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 mb-3">
                @php
                    $stats = $activeTab === 'teacher' ? $teacherStats : $studentStats;
                @endphp
                <div class="flex items-center gap-2 bg-emerald-50 rounded-xl px-3 py-2 border border-emerald-100">
                    <div class="w-7 h-7 rounded-lg bg-emerald-500 flex items-center justify-center flex-shrink-0">
                        <x-icon name="users" class="w-3.5 h-3.5 text-white" />
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-medium uppercase">Total</p>
                        <p class="text-sm font-bold text-emerald-700">{{ $stats['total'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-green-50 rounded-xl px-3 py-2 border border-green-100">
                    <div class="w-7 h-7 rounded-lg bg-green-500 flex items-center justify-center flex-shrink-0">
                        <x-icon name="check-circle" class="w-3.5 h-3.5 text-white" />
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-medium uppercase">Present</p>
                        <p class="text-sm font-bold text-green-700">{{ $stats['present'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-red-50 rounded-xl px-3 py-2 border border-red-100">
                    <div class="w-7 h-7 rounded-lg bg-red-500 flex items-center justify-center flex-shrink-0">
                        <x-icon name="x-circle" class="w-3.5 h-3.5 text-white" />
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-medium uppercase">Absent</p>
                        <p class="text-sm font-bold text-red-700">{{ $stats['absent'] }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-amber-50 rounded-xl px-3 py-2 border border-amber-100">
                    <div class="w-7 h-7 rounded-lg bg-amber-500 flex items-center justify-center flex-shrink-0">
                        <x-icon name="clock" class="w-3.5 h-3.5 text-white" />
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 font-medium uppercase">Late</p>
                        <p class="text-sm font-bold text-amber-700">{{ $stats['late'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Tabs --}}
        <div class="px-6 flex gap-6 border-t border-gray-100">
            <button wire:click="setActiveTab('teacher')"
                class="pb-3 pt-2 px-1 text-sm font-semibold border-b-2 transition
                    {{ $activeTab === 'teacher' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <x-icon name="briefcase" class="w-4 h-4 inline mr-1" />Teacher Attendance
            </button>
            <button wire:click="setActiveTab('student')"
                class="pb-3 pt-2 px-1 text-sm font-semibold border-b-2 transition
                    {{ $activeTab === 'student' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                <x-icon name="academic-cap" class="w-4 h-4 inline mr-1" />Student Attendance
            </button>
        </div>
    </div>

    <div class="p-6 space-y-5">

        {{-- ═══ TEACHER ATTENDANCE ═══ --}}
        @if($activeTab === 'teacher')

            {{-- Sub-tabs --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="flex gap-4 px-5 border-b border-gray-100">
                    <button wire:click="setSubTab('by_date')"
                        class="py-3 text-sm font-semibold border-b-2 transition
                            {{ $subTab === 'by_date' ? 'border-emerald-500 text-emerald-700' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                        Mark Attendance
                    </button>
                    <button wire:click="setSubTab('by_teacher')"
                        class="py-3 text-sm font-semibold border-b-2 transition
                            {{ $subTab === 'by_teacher' ? 'border-emerald-500 text-emerald-700' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                        View by Teacher
                    </button>
                </div>

                {{-- Mark Attendance (by_date) --}}
                @if($subTab === 'by_date')
                    <div class="p-4">
                        {{-- Date picker --}}
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex items-center gap-2">
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</label>
                                <input type="date" wire:model.live="teacherDate"
                                    class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            </div>
                        </div>

                        @if($teacherList->isEmpty())
                            <div class="text-center py-10">
                                <x-icon name="users" class="w-10 h-10 text-gray-200 mx-auto mb-2" />
                                <p class="text-sm text-gray-400">No teachers found.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Teacher</th>
                                            <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($teacherList as $i => $teacher)
                                            @php $att = $teacherAttendanceData[$teacher->id] ?? ['status'=>'none','db_status'=>4,'remarks'=>'']; @endphp
                                            <tr class="hover:bg-gray-50/50 transition">
                                                <td class="px-4 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                                            <span class="text-xs font-bold text-emerald-700">
                                                                {{ strtoupper(substr($teacher->user->name ?? 'T', 0, 1)) }}
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-gray-800 text-sm">{{ $teacher->user->name ?? '-' }}</p>
                                                            <p class="text-xs text-gray-400">{{ $teacher->user->email ?? '' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <button wire:click="updateTeacherStatus({{ $teacher->id }}, 'present')"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $att['status'] === 'present' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-500 hover:bg-green-50 hover:text-green-700' }}">
                                                            Present
                                                        </button>
                                                        <button wire:click="updateTeacherStatus({{ $teacher->id }}, 'absent')"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $att['status'] === 'absent' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-500 hover:bg-red-50 hover:text-red-700' }}">
                                                            Absent
                                                        </button>
                                                        <button wire:click="updateTeacherStatus({{ $teacher->id }}, 'late')"
                                                            class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $att['status'] === 'late' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-500 hover:bg-amber-50 hover:text-amber-700' }}">
                                                            Late
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="text"
                                                        wire:model.lazy="teacherAttendanceData.{{ $teacher->id }}.remarks"
                                                        placeholder="Remark…"
                                                        class="w-full rounded-lg border-gray-200 text-xs focus:border-emerald-500 focus:ring-emerald-500 py-1.5" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex justify-end mt-4">
                                <x-button emerald label="Save All Attendance" icon="check"
                                    wire:click="saveAllTeacherAttendance" />
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="mt-3 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-green-700 text-sm">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if(session('error'))
                            <div class="mt-3 bg-red-50 border border-red-200 rounded-lg px-4 py-2 text-red-700 text-sm">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- View by Teacher --}}
                @if($subTab === 'by_teacher')
                    <div class="p-4 space-y-4">
                        {{-- Filters --}}
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date</label>
                                <input type="date" wire:model.live="filterTeacherDate"
                                    class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            </div>
                            <div class="flex-1 min-w-40">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Teacher</label>
                                <select wire:model.live="filterTeacherId"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">All Teachers</option>
                                    @foreach($teachers as $t)
                                        <option value="{{ $t->id }}">{{ $t->user->name ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                                <select wire:model.live="filterTeacherStatus"
                                    class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">All</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Teacher</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Remark</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($teacherAttendanceList as $i => $record)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $teacherAttendanceList->firstItem() + $i }}</td>
                                            <td class="px-4 py-3">
                                                <p class="font-semibold text-gray-800">{{ $record->teacherDetail->user->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-400">{{ $record->teacherDetail->user->email ?? '' }}</p>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $statusMap = [1=>'Present',0=>'Absent',2=>'Late',3=>'Half Day'];
                                                    $colorMap = [1=>'bg-green-100 text-green-700',0=>'bg-red-100 text-red-700',2=>'bg-amber-100 text-amber-700',3=>'bg-blue-100 text-blue-700'];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $colorMap[$record->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                    {{ $statusMap[$record->status] ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $record->remarks ?: '—' }}</td>
                                            <td class="px-4 py-3 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($record->attendance_date)->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center">
                                                <x-icon name="calendar" class="w-10 h-10 text-gray-200 mx-auto mb-2" />
                                                <p class="text-sm text-gray-400">No attendance records found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($teacherAttendanceList->hasPages())
                            <div class="mt-3">{{ $teacherAttendanceList->links() }}</div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

        {{-- ═══ STUDENT ATTENDANCE ═══ --}}
        @if($activeTab === 'student')

            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                {{-- Sub-tabs --}}
                <div class="flex gap-4 px-5 border-b border-gray-100">
                    <button wire:click="setSubTab('by_date')"
                        class="py-3 text-sm font-semibold border-b-2 transition
                            {{ $subTab === 'by_date' ? 'border-emerald-500 text-emerald-700' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                        Mark by Date
                    </button>
                    <button wire:click="setSubTab('by_student')"
                        class="py-3 text-sm font-semibold border-b-2 transition
                            {{ $subTab === 'by_student' ? 'border-emerald-500 text-emerald-700' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                        View by Student
                    </button>
                    <button wire:click="setSubTab('by_class')"
                        class="py-3 text-sm font-semibold border-b-2 transition
                            {{ $subTab === 'by_class' ? 'border-emerald-500 text-emerald-700' : 'border-transparent text-gray-400 hover:text-gray-600' }}">
                        Calendar View
                    </button>
                </div>

                {{-- Mark by Date --}}
                @if($subTab === 'by_date')
                    <div class="p-4 space-y-4">
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date</label>
                                <input type="date" wire:model.live="studentDate"
                                    class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            </div>
                            <div class="flex-1 min-w-36">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Class</label>
                                <select wire:model.live="selectedClass"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">All Classes</option>
                                    @foreach($standards as $std)
                                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($sections->isNotEmpty())
                                <div class="flex-1 min-w-32">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Section</label>
                                    <select wire:model.live="selectedSection"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="">All Sections</option>
                                        @foreach($sections as $sec)
                                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Student</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Class / Section</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($studentList as $i => $student)
                                        @php $att = $student->attendance->first(); @endphp
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $studentList->firstItem() + $i }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $student->user->name ?? $student->full_name ?? '-' }}
                                                <span class="text-xs text-gray-400 ml-1">{{ $student->admission_no }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500 text-xs">
                                                {{ $student->standard->name ?? '-' }} / {{ $student->section->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @if($att)
                                                    @php
                                                        $sMap = [1=>'Present',0=>'Absent',2=>'Late'];
                                                        $sColor = [1=>'bg-green-100 text-green-700',0=>'bg-red-100 text-red-700',2=>'bg-amber-100 text-amber-700'];
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sColor[$att->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                        {{ $sMap[$att->status] ?? 'Unknown' }}
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-400">
                                                        Not Marked
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-10 text-center">
                                                <x-icon name="academic-cap" class="w-10 h-10 text-gray-200 mx-auto mb-2" />
                                                <p class="text-sm text-gray-400">Select a class to view students.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(method_exists($studentList, 'hasPages') && $studentList->hasPages())
                            <div class="mt-3">{{ $studentList->links() }}</div>
                        @endif
                    </div>
                @endif

                {{-- View by Student --}}
                @if($subTab === 'by_student')
                    <div class="p-4 space-y-4">
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Date</label>
                                <input type="date" wire:model.live="filterStudentDate"
                                    class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            </div>
                            <div class="flex-1 min-w-36">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Class</label>
                                <select wire:model.live="filterStudentClass"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">All Classes</option>
                                    @foreach($standards as $std)
                                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($filterSections->isNotEmpty())
                                <div class="flex-1 min-w-32">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Section</label>
                                    <select wire:model.live="filterStudentSection"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="">All Sections</option>
                                        @foreach($filterSections as $sec)
                                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                                <select wire:model.live="filterStudentStatus"
                                    class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">All</option>
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                </select>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">#</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Student</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Class</th>
                                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($studentAttendanceList as $i => $record)
                                        <tr class="hover:bg-gray-50/50 transition">
                                            <td class="px-4 py-3 text-gray-400 text-xs">{{ $studentAttendanceList->firstItem() + $i }}</td>
                                            <td class="px-4 py-3 font-medium text-gray-800">
                                                {{ $record->studentDetail->user->name ?? $record->studentDetail->full_name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-gray-500 text-xs">
                                                {{ $record->studentDetail->standard->name ?? '-' }} / {{ $record->studentDetail->section->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                @php
                                                    $sMap = [1=>'Present',0=>'Absent',2=>'Late'];
                                                    $sColor = [1=>'bg-green-100 text-green-700',0=>'bg-red-100 text-red-700',2=>'bg-amber-100 text-amber-700'];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sColor[$record->status] ?? 'bg-gray-100 text-gray-600' }}">
                                                    {{ $sMap[$record->status] ?? 'Unknown' }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-gray-500 text-xs">{{ \Carbon\Carbon::parse($record->attendance_date)->format('d M Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-10 text-center">
                                                <x-icon name="calendar" class="w-10 h-10 text-gray-200 mx-auto mb-2" />
                                                <p class="text-sm text-gray-400">No records found for selected filters.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if(method_exists($studentAttendanceList, 'hasPages') && $studentAttendanceList->hasPages())
                            <div class="mt-3">{{ $studentAttendanceList->links() }}</div>
                        @endif
                    </div>
                @endif

                {{-- Calendar View --}}
                @if($subTab === 'by_class')
                    <div class="p-4 space-y-4">
                        {{-- Filters --}}
                        <div class="flex flex-wrap gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Month</label>
                                <input type="month" wire:model.live="calendarMonth"
                                    class="rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500" />
                            </div>
                            <div class="flex-1 min-w-36">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Class</label>
                                <select wire:model.live="calendarClass"
                                    class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="">Select Class</option>
                                    @foreach($standards as $std)
                                        <option value="{{ $std->id }}">{{ $std->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($calendarSections->isNotEmpty())
                                <div class="flex-1 min-w-32">
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Section</label>
                                    <select wire:model.live="calendarSection"
                                        class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="">All Sections</option>
                                        @foreach($calendarSections as $sec)
                                            <option value="{{ $sec->id }}">{{ $sec->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        @if(!empty($calendarData))
                            {{-- Calendar Grid --}}
                            @php
                                $month = \Carbon\Carbon::createFromFormat('Y-m', $calendarMonth);
                                $daysInMonth = $month->daysInMonth;
                                $firstDayOfWeek = $month->copy()->startOfMonth()->dayOfWeek;
                            @endphp
                            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                                <div class="grid grid-cols-7 bg-emerald-50 border-b border-emerald-100">
                                    @foreach(['Sun','Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                                        <div class="px-2 py-2 text-center text-xs font-bold text-emerald-700">{{ $day }}</div>
                                    @endforeach
                                </div>
                                <div class="grid grid-cols-7">
                                    {{-- Empty cells before month start --}}
                                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                                        <div class="p-2 border-b border-r border-gray-100 bg-gray-50/50 min-h-[70px]"></div>
                                    @endfor

                                    @for($day = 1; $day <= $daysInMonth; $day++)
                                        @php
                                            $dateKey = $month->copy()->setDay($day)->format('Y-m-d');
                                            $dayData = $calendarData[$dateKey] ?? null;
                                        @endphp
                                        <div class="p-2 border-b border-r border-gray-100 min-h-[70px] {{ $dateKey === now()->format('Y-m-d') ? 'bg-emerald-50' : '' }}">
                                            <p class="text-xs font-bold {{ $dateKey === now()->format('Y-m-d') ? 'text-emerald-700' : 'text-gray-600' }} mb-1">{{ $day }}</p>
                                            @if($dayData)
                                                <div class="space-y-0.5">
                                                    @if($dayData['present'] > 0)
                                                        <div class="text-[10px] bg-green-100 text-green-700 rounded px-1 py-0.5 font-semibold">P: {{ $dayData['present'] }}</div>
                                                    @endif
                                                    @if($dayData['absent'] > 0)
                                                        <div class="text-[10px] bg-red-100 text-red-700 rounded px-1 py-0.5 font-semibold">A: {{ $dayData['absent'] }}</div>
                                                    @endif
                                                    @if($dayData['late'] > 0)
                                                        <div class="text-[10px] bg-amber-100 text-amber-700 rounded px-1 py-0.5 font-semibold">L: {{ $dayData['late'] }}</div>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        @elseif($calendarClass)
                            <div class="text-center py-10">
                                <x-icon name="calendar" class="w-10 h-10 text-gray-200 mx-auto mb-2" />
                                <p class="text-sm text-gray-400">No attendance data for this period.</p>
                            </div>
                        @else
                            <div class="text-center py-10">
                                <x-icon name="calendar" class="w-10 h-10 text-gray-200 mx-auto mb-2" />
                                <p class="text-sm text-gray-400">Select a class and month to view the calendar.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>
